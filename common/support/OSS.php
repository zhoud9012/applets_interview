<?php
namespace common\support;

use chonder\AliyunOSS\AliyunOSS;
use Yii;

/**
 * OSS 封装阿里云OSS文件存储的API
 */

class OSS {

    private $ossClient;

    public function __construct($isInternal = false)
    {
        $serverAddress = $isInternal ? Yii::$app->params['oss']['ossServerInternal'] : Yii::$app->params['oss']['ossServer'];
        $this->ossClient = AliyunOSS::boot(
            $serverAddress,
            Yii::$app->params['oss']['AccessKeyId'],
            Yii::$app->params['oss']['AccessKeySecret']
        );
    }

    public static function upload($ossKey, $filePath)
    {
        //$oss = new OSS(true); // 上传文件使用内网，免流量费
        $oss = new OSS();
        $oss->ossClient->setBucket(Yii::$app->params['oss']['Bucket']);
        return $oss->ossClient->uploadFile($ossKey, $filePath);
    }

    public static function getUrl($ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket(Yii::$app->params['oss']['Bucket']);
        return preg_replace('/(.*)\?OSSAccessKeyId=.*/', '$1', $oss->ossClient->getUrl($ossKey, new \DateTime("+1 day")));
    }

    public static function delFile($ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket(Yii::$app->params['oss']['Bucket']);
        return $oss->ossClient->delFile($ossKey);
    }

    public static function createBucket($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->createBucket($bucketName);
    }

    public static function getAllObjectKey($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->getAllObjectKey($bucketName);
    }

}
