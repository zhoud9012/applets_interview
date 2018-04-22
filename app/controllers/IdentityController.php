<?php

namespace app\controllers;

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

class IdentityController extends BaseController
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
     * 小程序端校验身份
     * @return array
     */
    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        //todo 判断是否为面试官
        //todo 1.依据token 查openid
        //todo 2.通过openid 查是否为面试官权限重写如果面试官表中能获取到记录则就是面试官否则是应试者

        $accessToken = 'token:'.$paramsArr['access-token'];
        $openid =\Yii::$app->cache->redis->HGET($accessToken,'openid');

        //非面试官返回试题类型 面试官返回候选人
        $identityRole = $this->__isInterviewer($openid);
        
        return ['role'=>$identityRole];

    }


    private function __getInterviewerInfoByOpenid($openid)
    {
        return (new Query())
            ->select([
                'interviewer_info.interviewer_id'
            ])
            ->from('user_applet')
            ->innerJoin('interviewer_info','user_applet.phone = interviewer_info.phone')
            ->where(['user_applet.openid'=>$openid])
            ->all();
    }

    private function __isInterviewer($openid)
    {

        $query = $this->__getInterviewerInfoByOpenid($openid);
        return empty($query)?'candidates':'interviewer';
    }

    private function __getCandidatesInfoByOpenid($openid)
    {
        return (new Query())
            ->select([
                'interviewer_info.interviewer_id'
            ])
            ->from('user_applet')
            ->innerJoin('interviewer_info','user_applet.phone = interviewer_info.phone')
            ->where(['user_applet.openid'=>$openid])
            ->all();
    }


}
