<?php

namespace app\controllers;

use common\support\OSS;
use common\support\StringHelper;
use common\error\ErrorInfo;
use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\base\Event;
use yii;


class LoginController extends BaseController
{
    public $response;

    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'enableRateLimitHeaders' => true,
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
     * 用户登陆
     * @return array
     */
    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        $this->__makeAccessToken($paramsArr['code'],3600*7*24);

    }


    /**
     * @param $code
     * @param $time
     * @return mixed
     */
    private function __makeAccessToken($code,$time){

        $applet = \Yii::$app->applet->makeSession($code);

        $session = $applet->getSession();
        $openid = $session->getOpenid(); //openid
        $session_key = $session->getSessionKey(); //session_key

        $accessToken = $this->__makeThirdSession('token:',1);

        \Yii::$app->cache->redis->hmset($accessToken, 'openid', $openid,'session_key',$session_key);
        \Yii::$app->cache->redis->expire($accessToken,$time);

        return $accessToken;
    }

    /**
     * @param $prefix
     * @param $type
     * @return mixed
     */
    private function __makeThirdSession($prefix,$type){

        $uuid = $prefix.StringHelper::uuid();
        $command = 'head /dev/urandom | tr -dc A-Za-z0-9 | head -c 168';
        $random = $prefix.exec($command);
        $list = ['index0',$uuid,$random];

        return $list[$type];

    }

    /**
     * @param $code
     * @param $encryptedData
     * @param $iv
     * @param $rawData
     * @param $signature
     * @return array
     */
    private function __getWechatOtherInfo($code,$encryptedData,$iv,$rawData,$signature){

        $applet = \Yii::$app->applet->makeSession($code);
        $session = $applet->getSession();
        $openid = $session->getOpenid(); //openid
        $getUser = $applet->getUser($encryptedData,$iv); //返回用户信息
        $checkSignature = $applet->checkSignature($rawData,$signature); //数据签名校验
        $unionid = $session->getUnionid(); //unionid

        return [
            'session'=>$session,
            'openid'=>$openid,
            'getUser'=>$getUser,
            'checkSignature'=>$checkSignature,
            'unionid'=>$unionid,
        ];

    }



}
