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
            //'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        //认证不需要cookie && session
        'user' => [
            'identityClass' => 'common\models\User',//指定认证的model类
            'enableAutoLogin' => true,
            'enableSession' => false,//用户认证状态就不通过session来保持,因为RESTful APIs为无状态的
            'loginUrl' => null,//属性为null（显示一个HTTP 403 错误而不是跳转到登录界面）
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
                    'except'=>['delete','create','view'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'GET dynamic' => 'dynamic', //http 动词 参数    动作名
                        'GET export-summary-candidates-info-csv' => 'export-summary-candidates-info-csv', //http 动词 参数    动作名
                        'GET export-dynamic-candidates-info-csv' => 'export-dynamic-candidates-info-csv', //http 动词 参数    动作名
                        'GET question' => 'question', //http 动词 参数    动作名
                        'GET candidates-status' => 'candidates-status', //http 动词 参数    动作名
                        'POST import-summary-candidates-info' => 'import-summary-candidates-info', //http 动词 参数    动作名
                        'OPTIONS import-summary-candidates-info' => 'import-summary-candidates-info', //http 动词 参数    动作名
                        'PUT update' => 'update', //http 动词 参数    动作名
                        'OPTIONS update' => 'update', //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'interviewer-info',
                    'except'=>['delete','view'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'POST create' => 'create', //http 动词 参数    动作名
                        'OPTIONS create' => 'create', //http 动词 参数    动作名
                        'PUT update' => 'update', //http 动词 参数    动作名
                        'OPTIONS update' => 'update', //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'office-info',
                    'except'=>['delete'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'OPTIONS create' => 'create', //http 动词 参数    动作名
                        'POST create' => 'create', //http 动词 参数    动作名
                        'OPTIONS update' => 'update', //http 动词 参数    动作名
                        'PUT update' => 'update', //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'question-info',
                    'except'=>['delete'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'POST create' => 'create', //http 动词 参数    动作名
                        'OPTIONS create' => 'create', //http 动词 参数    动作名
                        'PUT update' => 'update', //http 动词 参数    动作名
                        'OPTIONS update' => 'update', //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'question-relation-office',
                    'except'=>['delete'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'POST create' => 'create', //http 动词 参数    动作名
                        'OPTIONS create' => 'create', //http 动词 参数    动作名
                        'PUT update' => 'update', //http 动词 参数    动作名
                        'OPTIONS update' => 'update', //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'login',
                    'except'=>['delete'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'POST create' => 'create', //http 动词 参数    动作名
                        'OPTIONS create' => 'create', //http 动词 参数    动作名
                        'PUT update' => 'update', //http 动词 参数    动作名
                        'OPTIONS update' => 'update', //http 动词 参数    动作名
                    ],
                ],
                ['class'=>'yii\rest\UrlRule',
                    'controller'=>'identity',
                    'except'=>['delete'],//禁用的http动词
                    'pluralize'=>false,
                    'extraPatterns'=>[// 为方法配置restful 请求
                        'POST create' => 'create', //http 动词 参数    动作名
                        'OPTIONS create' => 'create', //http 动词 参数    动作名
                        'PUT update' => 'update', //http 动词 参数    动作名
                        'OPTIONS update' => 'update', //http 动词 参数    动作名
                    ],
                ],
            ],
        ],
        #小程序配置
        'applet' => [
            'class' => 'Jtcczu\Applet\Applet',
            'appid' => 'wxa740617ebd3f178d',
            'secret' => '1138a306a18627d7fdabf0b4d19f1dfd'
        ]

    ],
    'params' => $params,
];
