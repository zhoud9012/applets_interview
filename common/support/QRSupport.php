<?php
/**
 * Created by PhpStorm.
 * User: wangx08
 * Date: 2015-03-26
 * Time: 17:47
 */

namespace common\support;

use Endroid\QrCode\QrCode;

class QRSupport {
    /**
     * 创建二维码
     * @param  string $info  信息
     * @param  int $size  大小（单位：px）
     */
    public static function responseQR($info, $size) {
        $qrcode = new QrCode();

        $qrcode->setImageType(QrCode::IMAGE_TYPE_PNG);
        $qrcode->setText($info);
        $qrcode->setSize($size);
        $qrcode->setPadding($size*0.1);

        $qrcode->render();
    }
    
    /**
     * 创建二维码，并保存二维码到本地
     * @param string $info 信息
     * @param int $size 二维码大小，单位：像素
     * @param string 本地保存路径 （绝对路径）
     * @return string 返回本地文件地址
     */
    public static function saveQR($info,$size,$path){
    	$qrcode = new QrCode();
    	$qrcode->setImageType(QrCode::IMAGE_TYPE_PNG);
    	$qrcode->setText($info);
    	$qrcode->setSize($size);
    	$qrcode->setPadding($size*0.1);
    	$qrcode->save($path);
    	return $path;
    }
}
