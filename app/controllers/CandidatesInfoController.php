<?php

namespace app\controllers;

use common\support\OSS;
use common\support\StringHelper;
use common\error\ErrorInfo;
use common\support\ExcelManage;
use app\models\CandidatesInfo;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\base\Event;
use yii;

class CandidatesInfoController extends BaseController
{
    public $response;

    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'enableRateLimitHeaders' => true,
        ];

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                /*下面是三种验证access_token方式*/
                //1.HTTP 基本认证: access token 当作用户名发送，应用在access token可安全存在API使用端的场景，例如，API使用端是运行在一台服务器上的程序。
                //HttpBasicAuth::className(),
                //2.OAuth 2: 使用者从认证服务器上获取基于OAuth2协议的access token，然后通过 HTTP Bearer Tokens 发送到API 服务器。
                //HttpBearerAuth::className(),
                //3.请求参数: access token 当作API URL请求参数发送，这种方式应主要用于JSONP请求，因为它不能使用HTTP头来发送access token
                //http://localhost/user/index/index?access-token=123
                QueryParamAuth::className(),
            ],
        ];

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function init() {
        parent::init();
        $this->response = Yii::$app->response;
        //绑定事件
        Event::on(Response::className(), Response::EVENT_BEFORE_SEND, [$this, 'formatDataBeforeSend']);
    }

    /**
     * 使用 controller 中的 afterAction 方法，在响应完 action 之后，对数据格式化
     * @param yii\base\Action $action
     * @param mixed $result
     * @return array
     */
    public function afterAction($action, $result)
    {
        $rs = parent::afterAction($action, $result);
        return ['data' => $rs, 'error' => '0','status'=>$this->response->statusCode];
    }

    /**
     * @return array
     */
    public  function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * 应试者信息过滤查询
     * @return array
     */
    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        $query = $this->__getSummaryCandidatesInfo($paramsArr);
        $pageSize = empty($paramsArr['pageSize'])?20:$paramsArr['pageSize'];

        return new ActiveDataProvider(
            [
                'query'=>$query,
                'pagination'=>['pageSize'=>$pageSize],//分页大小设置
            ]
        );
    }

    /**
     * @param $where
     * @param $paramsArr
     * @return array
     */
    private static function __buildGetCandidatesInfoWhereSql($where,$paramsArr)
    {

        $params = [];

        if (isset($paramsArr['candidates_name']) && !empty($paramsArr['candidates_name'])) {
            $where = $where . " and (candidates_info.name like CONCAT('%', :candidates_name, '%'))";
            $params[':candidates_name'] = $paramsArr['candidates_name'];
        }

        if (isset($paramsArr['phone']) && !empty($paramsArr['phone'])) {
            $where = $where . " and (candidates_info.phone like CONCAT('%', :phone, '%'))";
            $params[':phone'] = $paramsArr['phone'];
        }

        if (isset($paramsArr['office_name']) && !empty($paramsArr['office_name'])) {
            $where = $where . " and (office_info.office_name = :office_name)";
            $params[':office_name'] = $paramsArr['office_name'];
        }

        if (isset($paramsArr['interview_time_begin']) && !empty($paramsArr['interview_time_begin'])) {
            $where = $where . ' and (candidates_info.interview_time >= :interview_time_begin)';
            $params[':interview_time_begin'] = $paramsArr['interview_time_begin'] . ' 00:00:00';
        }

        if (isset($paramsArr['interview_time_end']) && !empty($paramsArr['interview_time_end'])) {
            $where = $where . ' and (candidates_info.interview_time <= :interview_time_end)';
            $params[':interview_time_end'] = $paramsArr['interview_time_end'] . ' 00:00:00';
        }

        if (isset($paramsArr['interview_result']) && !empty($paramsArr['interview_result'])) {
            $where = $where . ' and (candidates_info.interview_result = :interview_result)';
            $params[':interview_result'] = $paramsArr['interview_result'];
        }

        return ['where' => $where, 'params' => $params];
    }

    /**
     * @param $paramsArr
     * @return $this
     */
    private function __getSummaryCandidatesInfo($paramsArr)
    {
        $where = '1=1';
        $whereSql = self::__buildGetCandidatesInfoWhereSql($where,$paramsArr);
        return (new Query())
            ->select([
                'candidates_info.candidates_id',
                'candidates_info.phone',
                'candidates_info.name as candidates_name',
                'candidates_info.email',
                'office_info.office_name',
                'interviewer_info.name as interviewer_name',
                'candidates_info.interview_time',
                'company_info.company_name',
                'candidates_info.sign_in_time',
                'candidates_info.interview_state',
                'candidates_info.interview_result',
                'candidates_info.interview_appraise',
                'candidates_info.written_test_appraise'
            ])
            ->from('candidates_info')
            ->leftJoin('user_applet','candidates_info.phone = user_applet.phone')
            ->leftJoin('company_info','company_info.company_id = candidates_info.company_id')
            ->leftJoin('interviewer_info','interviewer_info.interviewer_id = candidates_info.interviewer_id')
            ->leftJoin('office_info','candidates_info.office_id = office_info.office_id')
            ->where($whereSql['where'], $whereSql['params']);
            //->all();

    }

    /**
     * 动态应试者信息查询
     * 1.“暂无”和“爽约” 状态应试者
     * 2.面试时间为当前时间的第二天的应试者
     * 3.9点后查询新数据//貌似无需抓取
     * @return array
     */
    public function actionDynamic()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        $query = $this->__getDynamicCandidatesInfo();
        $pageSize = empty($paramsArr['pageSize'])?20:$paramsArr['pageSize'];

        return new ActiveDataProvider(
            [
                'query'=>$query,
                'pagination'=>['pageSize'=>$pageSize],//分页大小设置
            ]
        );
    }

    /**
     * @return $this
     */
    private function __getDynamicCandidatesInfo()
    {
        return (new Query())
            ->select([
                'candidates_info.candidates_id',
                'candidates_info.phone',
                'candidates_info.name as candidates_name',
                'candidates_info.email',
                'office_info.office_name',
                'interviewer_info.name as interviewer_name',
                'candidates_info.interview_time',
                'company_info.company_name',
                'candidates_info.sign_in_time',
                'candidates_info.interview_state',
                'candidates_info.interview_result',
                'candidates_info.interview_appraise',
                'candidates_info.written_test_appraise'
            ])
            ->from('candidates_info')
            ->leftJoin('user_applet','candidates_info.phone = user_applet.phone')
            ->leftJoin('company_info','company_info.company_id = candidates_info.company_id')
            ->leftJoin('interviewer_info','interviewer_info.interviewer_id = candidates_info.interviewer_id')
            ->leftJoin('office_info','candidates_info.office_id = office_info.office_id')
            ->where([
                'and',
                ['>','candidates_info.interview_time',date('Y-m-d',strtotime('+1 day'))],
                ['<','candidates_info.interview_time',date('Y-m-d',strtotime('+2 day'))],
                ['or','candidates_info.interview_result = 1','candidates_info.interview_result = 4']
            ]);
            //->all();
    }

    public function actionImportSummaryCandidatesInfo()
    {
        $filePath = $_FILES['file']['tmp_name'];
        $data = $this->__importDataByFilePath($filePath);

        //TODO 数据处理反向关联到id 考虑是否同名问题

        return $this->__saveSummaryCandidatesInfo($data);//数据循环插入

    }

    private function __saveSummaryCandidatesInfo($data)
    {
        $success = [];
        foreach($data as $value)
        {
            $candidatesInfoModel = new CandidatesInfo();
            $candidatesInfoModel->candidates_id = StringHelper::uuid();
            $candidatesInfoModel->phone = $value[1];
            $candidatesInfoModel->name = $value[2];
            $candidatesInfoModel->email = $value[3];
            $candidatesInfoModel->office_id = $value[4];
            $candidatesInfoModel->interviewer_id = $value[5];
            $candidatesInfoModel->interview_time = $this->__excelConvertTime($value[6]);
            $candidatesInfoModel->company_id = $value[7];
            $candidatesInfoModel->save();
            $success[] = $candidatesInfoModel->interview_time;
        }

        return $success;
    }

    private function __excelConvertTime($time)
    {
        // 开票日期转换
        if (is_numeric($time) && strlen(explode('.',$time)[0]) <= 5) {
            return date('Y-m-d H:i:s', \PHPExcel_Shared_Date::ExcelToPHP($time)-3600*8);//exl 时间与php 时间相差8小时
        }else {
            $this->response->statusCode = 500;//自定义HTTP返回码
            ErrorInfo::setAndReturn('0050203' );
        }
    }

    private function __importDataByFilePath($filePath)
    {
        ob_end_clean();
        try {
            $data = ExcelManage::importDataByFilePath($filePath, 2);
        }catch (\PHPExcel_Exception $exception) {
            $this->response->statusCode = 500;//自定义HTTP返回码
            ErrorInfo::setAndReturn('0050201');
        }
        if (!is_array($data)) {
            $this->response->statusCode = 501;//自定义HTTP返回码
            ErrorInfo::setAndReturn('0050202');
        }
        return $data;
    }


    /**
     * 应试者信息汇总导出csv
     * @return string
     */
    public function actionExportSummaryCandidatesInfoCsv()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        return $this->__exportSummaryCandidatesInfo($paramsArr);
    }

    /**
     * @param $paramsArr
     * @return string
     */
    private function __exportSummaryCandidatesInfo($paramsArr)
    {
        try {
            set_time_limit(0);

            //数据源
            $data = $this->__getSummaryCandidatesInfo($paramsArr)->all();

            //列标题
            $columnTitles = ['序号','手机号','应试者','邮箱','职位','面试官','预约时间','公司','签到时间','面试结果','面试评价','笔试评价'];

            //创建目录
            $exportDir = \Yii::getAlias("@runtime/web/xls/");
            \yii\helpers\FileHelper::createDirectory($exportDir);

            //创建文件
            $fileName = sprintf("interview-data_%s.csv",$paramsArr['access-token']);
            $allFilePath = $exportDir . $fileName;

            $fp = fopen($allFilePath, 'w');

            //boom头
            fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

            //写入标题
            fputcsv($fp, $columnTitles);

            if (!empty($data)) {
                $i = 1;
                foreach ($data as $key => $val) {

                    //装拼正文内容
                    $contactArr = [
                        $i,//序号
                        $val['phone'], //手机号
                        $val['candidates_name'], //应试者
                        $val['email'], //邮箱
                        $val['office_name'], //职位
                        $val['interviewer_name'], //面试官
                        $val['interview_time'], //预约时间
                        $val['company_name'], //公司
                        $val['sign_in_time'], //签到时间
                        self::__interviewResultConvertString($val['interview_result']),//面试结果
                        $val['interview_appraise'], //面试评价
                        $val['written_test_appraise'], //笔试评价
                    ];

                    //写入正文
                    fputcsv($fp, $contactArr);

                    $i++;
                }
            }

            $key = $fileName;
            OSS::upload($key, $allFilePath);
            @unlink($allFilePath);
            $ossUrl = OSS::getUrl($key);

            return $ossUrl . '?t=' . StringHelper::uuid();

        } catch (\Exception $e) {
            Yii::error((string)$e);
            return '导出异常';
        }
    }

    /**
     * @param $num
     * @return mixed
     */
    private function __interviewResultConvertString($num){

        $stateArr = ['数组索引0','暂无','通过','淘汰','爽约'];
        return $stateArr[$num];
    }

    /**
     * 动态应试者信息导出csv
     * 应试者信息导出csv
     * @return string
     */
    public function actionExportDynamicCandidatesInfoCsv()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        return $this->__exportDynamicCandidatesInfo($paramsArr);
    }

    /**
     * @param $paramsArr
     * @return string
     */
    private function __exportDynamicCandidatesInfo($paramsArr)
    {
        try {
            set_time_limit(0);

            //数据源
            $data = $this->__getDynamicCandidatesInfo()->all();

            //列标题
            $columnTitles = ['序号','应试者','手机号','职位','面试官','预约时间','公司','状态','签到时间'];

            //创建目录
            $exportDir = \Yii::getAlias("@runtime/web/xls/");
            \yii\helpers\FileHelper::createDirectory($exportDir);

            //创建文件
            $fileName = sprintf("interview-data_%s.csv",$paramsArr['access-token']);
            $allFilePath = $exportDir . $fileName;

            $fp = fopen($allFilePath, 'w');

            //boom头
            fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

            //写入标题
            fputcsv($fp, $columnTitles);

            if (!empty($data)) {
                $i = 1;
                foreach ($data as $key => $val) {

                    //装拼正文内容
                    $contactArr = [
                        $i,//序号
                        $val['candidates_name'], //应试者
                        $val['phone'], //手机号
                        $val['office_name'], //职位
                        $val['interviewer_name'], //面试官
                        $val['interview_time'], //预约时间
                        $val['company_name'], //公司
                        self::__interviewStateConvertString($val['interview_state']), //状态
                        $val['sign_in_time'], //签到时间

                    ];

                    //写入正文
                    fputcsv($fp, $contactArr);

                    $i++;
                }
            }

            $key = $fileName;
            OSS::upload($key, $allFilePath);
            @unlink($allFilePath);
            $ossUrl = OSS::getUrl($key);

            return $ossUrl . '?t=' . StringHelper::uuid();

        } catch (\Exception $e) {
            Yii::error((string)$e);
            return '导出异常';
        }
    }

    /**
     * @param $num
     * @return mixed
     */
    private function __interviewStateConvertString($num){

        $stateArr = ['数组索引0','未签到','答题中','等待审题','审题中','面试中','面试结束','未签到'];
        return $stateArr[$num];
    }

    /**
     * @return mixed
     */
    public function actionUpdate()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();

        $candidatesInfo = CandidatesInfo::find()->where(['candidates_id'=>$paramsArr['candidates_id']])->one();
        $candidatesInfo->setScenario('update');
        $candidatesInfo->name = $paramsArr['name'];
        $candidatesInfo->phone = $paramsArr['phone'];
        $candidatesInfo->office_id = $paramsArr['office_id'];
        $candidatesInfo->interviewer_id = $paramsArr['interviewer_id'];
        $candidatesInfo->interview_time = $paramsArr['interview_time'];
        $candidatesInfo->company_id = $paramsArr['company_id'];
        $candidatesInfo->save();

        return $candidatesInfo->primaryKey;
    }

    //todo 应试者依据岗位获取试题类型

    public function actionQuestion(){

        $request = \Yii::$app->request;
        $paramsArr = $request->get();
        $accessToken = $paramsArr['access-token'];
        $openid = \Yii::$app->cache->redis->hget('token:'.$accessToken,'openid');

        return $this->__getQuestionByOpenid($openid);
    }

    private function __getQuestionByOpenid($openid)
    {
        return (new Query())
            ->select([
                //'user_applet.openid AS openid',
                'candidates_info.phone',
                'office_info.office_id AS office_id',
                'office_info.office_name AS office_name',
                'question_relation_office.relation_id AS relation_id',
                'question_info.question_id AS question_id',
                'question_info.question_name AS question_name'
            ])
            ->from('user_applet')
            ->innerJoin('candidates_info','user_applet.phone = candidates_info.phone')
            ->innerJoin('office_info','office_info.office_id = candidates_info.office_id')
            ->innerJoin('question_relation_office','question_relation_office.office_id = office_info.office_id')
            ->innerJoin('question_info','question_info.question_id = question_relation_office.question_id')
            ->where([
                'user_applet.openid'=>$openid
            ])
            //->createCommand()->getRawSql();
            ->all();
    }

    //todo 获取本人应试记录状态
    public function actionCandidatesStatus(){

        $request = \Yii::$app->request;
        $paramsArr = $request->get();
        $accessToken = $paramsArr['access-token'];
        $openid = \Yii::$app->cache->redis->hget('token:'.$accessToken,'openid');

        return $this->__getCandidatesStatusByOpenid($openid);
    }

    private function __getCandidatesStatusByOpenid($openid)
    {
        return (new Query())
            ->select([
                'candidates_info.candidates_id',
                'candidates_info.phone',
                'candidates_info.name',
                'office_info.office_name AS office_name',
                'candidates_info.sign_in_time',
                'candidates_info.interview_state',
                'candidates_info.interview_result',
                'candidates_info.interview_appraise',
                'candidates_info.written_test_appraise'
            ])
            ->from('user_applet')
            ->innerJoin('candidates_info','user_applet.phone = candidates_info.phone')
            ->innerJoin('office_info','office_info.office_id = candidates_info.office_id')
            ->where([
                'user_applet.openid'=>$openid
            ])
            //->createCommand()->getRawSql();
            ->all();
    }

    public function actionFinishExam()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();

        $candidatesInfo = CandidatesInfo::find()->where(['candidates_id'=>$paramsArr['candidates_id']])->one();
        $candidatesInfo->setScenario('finish_exam');
        $candidatesInfo->interview_state = 3;
        $candidatesInfo->save();

        return $candidatesInfo->primaryKey;
    }

}
