<?php

namespace app\controllers;

use common\support\StringHelper;
use app\models\QuestionRelationOffice;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\base\Event;
use yii;

class QuestionRelationOfficeController extends BaseController
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
     * 试题类型查询
     * @return array
     */
    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        $query = $this->__getQuestionRelationOffice();
        $pageSize = empty($paramsArr['pageSize'])?20:$paramsArr['pageSize'];

        return new ActiveDataProvider(
            [
                'query'=>$query,
                'pagination'=>['pageSize'=>$pageSize],//分页大小设置
            ]
        );
    }

    private function __getQuestionRelationOffice()
    {
        $query = (new Query())
            ->select([
                'question_relation_office.relation_id',
                'question_info.question_id',
                'question_info.question_name',
                'office_info.office_id',
                'office_info.office_name'
            ])
            ->from('question_relation_office')
            ->leftJoin('question_info','question_relation_office.question_id = question_info.question_id')
            ->leftJoin('office_info','office_info.office_id = question_relation_office.office_id');
        return $query;
    }

    public function actionCreate()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();
        $QuestionRelationOffice = new QuestionRelationOffice;
        $QuestionRelationOffice->relation_id = StringHelper::uuid();
        $QuestionRelationOffice->question_id = $paramsArr['question_id'];
        $QuestionRelationOffice->office_id = $paramsArr['office_id'];
        $QuestionRelationOffice->save();

        return $QuestionRelationOffice->primaryKey;
    }

    public function actionUpdate()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();
        $QuestionRelationOffice = QuestionRelationOffice::findOne($paramsArr['relation_id']);
        $QuestionRelationOffice->question_id = $paramsArr['question_id'];
        $QuestionRelationOffice->office_id = $paramsArr['office_id'];
        $QuestionRelationOffice->save();

        return $QuestionRelationOffice->primaryKey;
    }
}
