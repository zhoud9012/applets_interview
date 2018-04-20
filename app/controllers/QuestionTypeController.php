<?php

namespace app\controllers;

use common\error\ErrorInfo;
use common\support\StringHelper;
use app\models\QuestionType;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\base\Event;
use yii;

class QuestionTypeController extends BaseController
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

        $query = $this->__getQuestionType();
        $pageSize = empty($paramsArr['pageSize'])?20:$paramsArr['pageSize'];

        return new ActiveDataProvider(
            [
                'query'=>$query,
                'pagination'=>['pageSize'=>$pageSize],//分页大小设置
            ]
        );

    }

    private function __getQuestionType()
    {
        return (new Query())
            ->select([
                'question_type.question_type_id',
                'question_type.question_name',
                'office_info.office_name'
            ])
            ->from('question_type')
            ->leftJoin('office_info','office_info.office_id = question_type.office_id');
    }

    public function actionCreate()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();
        $questionType = new QuestionType;
        $questionType->question_type_id = StringHelper::uuid();
        $questionType->office_id = $paramsArr['office_id'];
        $questionType->question_name = $paramsArr['question_name'];
        $questionType->save();
        return $questionType->primaryKey;
    }

    public function actionUpdate()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->post();
        $questionType = QuestionType::findOne($paramsArr['question_type_id']);
        $questionType->office_id = $paramsArr['office_id'];
        $questionType->question_name = $paramsArr['question_name'];
        $questionType->save();
        return $questionType->primaryKey;
    }

}
