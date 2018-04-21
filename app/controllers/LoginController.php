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
     * 应试者信息过滤查询
     * @return array
     */
    public function actionIndex()
    {
        $request = \Yii::$app->request;
        $paramsArr = $request->get();

        $applet = \Yii::$app->applet->makeSession($paramsArr['code']);
        $getUser = $applet->getUser($paramsArr['encryptedData'], $paramsArr['iv']); //返回用户信息
        $checkSignature = $applet->checkSignature($paramsArr['rawData'], $paramsArr['signature']); //数据签名校验

        $session = $applet->getSession();
        $openid = $session->getOpenid(); //openid
        $session_key = $session->getSessionKey(); //session_key
        $unionid = $session->getUnionid(); //unionid

        $accessToken = StringHelper::uuid();

        //todo 1.判断是否有token 有就通过token获取openid 没就通过code 调微信接口 获取openid 生成toeken返回



        //todo 通过code 获取openid toke
        //todo 初次登录 hash表存入redis 3rd_session = /dev/urandom  key = openid value =  session_key
        //todo 前端将3rd_session 写入storage
        //todo 后续进入小程序先从storage 读取3rd_session 发送给后端
        //todo 在redis 中查找合法openid

        //\Yii::$app->cache->redis->hset('token:'.$accessToken, 'somefield1', 'somevalue2');
        \Yii::$app->cache->redis->hmset('token:'.$accessToken, 'openid', $openid,'session_key',$session_key);
        $tokenExist =\Yii::$app->cache->redis->exists('token:39e5fb74-48f7-a2a3-8627-457ba2c85b55');
        $expire = \Yii::$app->cache->redis->expire('token:mykey2', 10);
        $token = \Yii::$app->cache->redis->hget('token:mykey2', 'somefield1');

        //$command = 'head /dev/urandom | tr -dc A-Za-z0-9 | head -c 168';//生成168 真随机
        //echo exec($command,$array);
        return [
            'session'=>$session,
            'openid'=>$openid,
            'session_key'=>$session_key,
            'unionid'=>$unionid,
            'getUser'=>$getUser,
            'checkSignature'=>$checkSignature,
            'array'=>StringHelper::uuid(),
            'token'=>$token,
            'tokenExist'=>$tokenExist,
        ];

    }

}
