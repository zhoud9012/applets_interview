<?php
return [
    //'adminEmail' => 'admin@example.com',
    "oss" => [
        'ossServer' => 'http://oss-cn-shenzhen.aliyuncs.com', //服务器外网地址，深圳为 http://oss-cn-shenzhen.aliyuncs.com
        'ossServerInternal' => 'http://oss-cn-shenzhen-internal.aliyuncs.com', //服务器内网地址，深圳为 http://oss-cn-shenzhen-internal.aliyuncs.com
        "AccessKeyId" => '61vs8qNHuFvbNF5Y',
        "AccessKeySecret" => 'edwMrM4bCX5TAp0P7Cw5UW8roCloR0',
        'Bucket' => 'qa-yunketest',
    ],
    "uploader" => [
        "type" => "oss", //oss
        "rootPath" => "mybackend"//weiloushu
    ],
];
