<?php

namespace app\controllers;

use common\error\ErrorInfo;
use common\support\StringHelper;
use app\models\InterviewerInfo;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\base\Event;
use yii;

class InterviewerInfoController extends BaseController
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

    public  function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        $query = $this->__getSummaryInterviewer($paramsArr);
        $pageSize = empty($paramsArr['pageSize'])?20:$paramsArr['pageSize'];

        return new ActiveDataProvider(
            [
                'query'=>$query,
                'pagination'=>['pageSize'=>$pageSize],//分页大小设置
            ]
        );
    }

    private function __buildSummaryInterviewerWhereSql($where,$paramsArr)
    {
        $params = [];

        if (isset($paramsArr['interviewer_name']) && !empty($paramsArr['interviewer_name'])) {
            $where = $where . " and (interviewer_info.name like CONCAT('%', :interviewer_name, '%'))";
            $params[':interviewer_name'] = $paramsArr['interviewer_name'];
        }

        if (isset($paramsArr['phone']) && !empty($paramsArr['phone'])) {
            $where = $where . " and (interviewer_name.phone like CONCAT('%', :phone, '%'))";
            $params[':phone'] = $paramsArr['phone'];
        }

        if (isset($paramsArr['email']) && !empty($paramsArr['email'])) {
            $where = $where . " and (interviewer_info.email like CONCAT('%', :email, '%'))";
            $params[':email'] = $paramsArr['email'];
        }

        return ['where' => $where, 'params' => $params];
    }

    private function __getSummaryInterviewer($paramsArr)
    {
        $where = '1=1';
        $whereSql = self::__buildSummaryInterviewerWhereSql($where,$paramsArr);

        return (new Query())
            ->select([
                'interviewer_info.phone',
                'interviewer_info.name',
                'interviewer_info.email',
                'interviewer_info.remark'
            ])
            ->from('interviewer_info')
            ->where($whereSql['where'], $whereSql['params']);
    }

    public function actionCreate()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();
        $interviewerInfo = new InterviewerInfo;
        $interviewerInfo->interviewer_id = StringHelper::uuid();
        $interviewerInfo->phone = $paramsArr['phone'];
        $interviewerInfo->name = $paramsArr['name'];
        $interviewerInfo->email = $paramsArr['email'];
        $interviewerInfo->remark = $paramsArr['remark'];
        $interviewerInfo->save();
        return $interviewerInfo->primaryKey;
    }

    public function actionUpdate()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();
        $interviewerInfo = InterviewerInfo::findOne($paramsArr['interviewer_id']);
        $interviewerInfo->phone = $paramsArr['phone'];
        $interviewerInfo->name = $paramsArr['name'];
        $interviewerInfo->email = $paramsArr['email'];
        $interviewerInfo->remark = $paramsArr['remark'];
        $interviewerInfo->save();
        return $interviewerInfo->primaryKey;
    }

}
