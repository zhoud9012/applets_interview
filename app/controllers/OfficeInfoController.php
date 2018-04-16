<?php

namespace app\controllers;

use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\db\Query;
use common\support\StringHelper;
use app\models\OfficeInfo;

class OfficeInfoController extends \yii\rest\Controller
{
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

    public function actionIndex()
    {
        $query = (new Query())
            ->select([
                'office_info.office_name'
            ])
            ->from('office_info')
            ->all();
        return $query;
    }

    public function actionCreate()
    {
        $officeInfo = new OfficeInfo;
        $officeInfo->office_id = StringHelper::uuid();
        $officeInfo->office_name = $_POST['office_name'];
        $officeInfo->save();
        return $officeInfo->primaryKey;
    }

}
