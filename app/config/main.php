<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,//用户认证状态就不通过session来保持,因为RESTful APIs为无状态的
            'loginUrl' => null,//属性为null（显示一个HTTP 403 错误而不是跳转到登录界面）
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-app',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */

        //http://app.interview-wechat.com/index.php/gii  改了后要用这种路径搞定
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'book',
                    'pluralize' => false //Yii 将在末端使用的控制器的名称自动变为复数 pluralize为false来禁用此行为
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'user-book',
                    'except'=>['delete','create','update','view'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'POST search' => 'search' //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'candidates-info',
                    'except'=>['delete','create','update','view'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'GET dynamic' => 'dynamic' //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'interviewer-info',
                    'except'=>['delete','create','update','view'],//禁用的http动词
                    'pluralize'=>false,
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'office-info',
                    'except'=>['delete'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'POST create' => 'create' //http 动词 参数    动作名
                    ],
                ],
            ],
        ],

    ],
    'params' => $params,
];
