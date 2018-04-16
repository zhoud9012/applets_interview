<?php

namespace app\controllers;

use yii\web\Response;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\db\Query;

class CandidatesInfoController extends \yii\rest\Controller
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
                'candidates_info.phone',
                'candidates_info.name',
                'candidates_info.email',
                'candidates_info.office_id',
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
            ->all();
        return $query;
    }

    public function actionDynamic() {
        $query = (new Query())
            ->select([
                'candidates_info.phone',
                'candidates_info.name',
                'candidates_info.email',
                'candidates_info.office_id',
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
            ->where([
                'and',
                ['>','candidates_info.interview_time',date('Y-m-d',strtotime('+1 day'))],
                ['<','candidates_info.interview_time',date('Y-m-d',strtotime('+2 day'))],
                ['or','candidates_info.interview_result = 1','candidates_info.interview_result = 4']
            ])
            //->createCommand()->getRawSql();
            ->all();
        return $query;
    }

}
