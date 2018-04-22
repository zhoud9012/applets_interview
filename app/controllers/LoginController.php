<?php

namespace app\controllers;

use common\support\OSS;
use common\support\StringHelper;
use common\error\ErrorInfo;
use app\models\UserApplet;
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

        $applet = \Yii::$app->applet->makeSession($paramsArr['code']);
        $session = $applet->getSession();

        $this->__userBindOpenId($session,$paramsArr['phone']);
        $this->__makeAccessToken($session,3600*7*24);

        //todo 1.通过手机号找到改用户将获取到的openid更新用户表,交换token 增加token 频率
        //todo 2.以后其他接口都通过校验token获得访问权限
        //todo 3.通过token查询出对应openid
        //todo 4.然后通过openid联合查询确定该接口都具体权限
        //todo 5.具体接口通过token获取到的

    }

    private function __userBindOpenId($session,$phone)
    {
        //todo 1.如果不存在用户就新建一个用户记录
        //todo 2.如果存在就修改openid
        $openid = $session->getOpenid(); //openid
        $userApplet = UserApplet::findOne(['phone' =>$phone]);
        if(empty($userApplet)){
            $userApplet = new UserApplet();
            $this->__createUser($userApplet,$openid,$phone);
        }else{
            $this->__updateUser($userApplet,$openid);
        }

    }

    private function __createUser($userApplet,$openid,$phone)
    {
        $userApplet->setScenario('create');
        $userApplet->user_id = StringHelper::uuid();
        $userApplet->openid = $openid;
        $userApplet->phone = $phone;
        $userApplet->save();
        return $userApplet->primaryKey;
    }

    private function __updateUser($userApplet,$openid)
    {
        $userApplet->setScenario('update');
        $userApplet->openid = $openid;
        $userApplet->save();
        return $userApplet->primaryKey;
    }

    /**
     * @param $session
     * @param $time
     * @return mixed
     */
    private function __makeAccessToken($session,$time){

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
     * @param $applet
     * @param $encryptedData
     * @param $iv
     * @param $rawData
     * @param $signature
     * @return array
     */
    private function __getWechatOtherInfo($applet,$encryptedData,$iv,$rawData,$signature){

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
