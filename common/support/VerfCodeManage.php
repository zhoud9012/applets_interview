<?php
/**
 * Created by PhpStorm.
 * User: wangx08
 * Date: 2015-04-21
 * Time: 18:41
 */

namespace common\support;


class VerfCodeManage {

    private static function getSessionKey($moduleName){
        return "app_verfcode_".$moduleName;
    }

    private static function generateVerfCode($moduleName){
        $code = self::generateRandCode(4);
        \Yii::$app->getSession()->set(self::getSessionKey($moduleName),$code);
        return $code;
    }

    private static function generateRandCode($length){
        $code="23456789abcdefghigkmnpqrstuvwsyzABCDEFGHIMNPQRSTUVWSYZ";
        $string="";
        for($i=0;$i<$length;$i++){
            $string.=$code{rand(0,strlen($code)-1)};
        }
        return $string;
    }

    public static function responseVerfCode($moduleName){
        $code =  self::generateVerfCode($moduleName);

        $width=65;
        $height=36;
        $img=imagecreatetruecolor($width,$height);
        $color=imagecolorallocate($img,rand(245,255),rand(245,255),rand(245,255));
        imagefill($img, 0, 0, $color);
        for($i=0;$i<100;$i++){
            $color=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
            imagesetpixel($img,rand(0,$width),rand(0,$height),$color);
        }
        //$color=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
        //imagerectangle($img,0,0,$width-1,$height-1,$color);
        //$color=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
        //for($i=0;$i<4;$i++){
        //    imagearc($img,rand(-10,$width),rand(-10,$height),rand(30,300),rand(20,200),55,44,$color);
        //}
        $fontFile = '@yii/captcha/SpicyRice.ttf';
        $fontFile = \Yii::getAlias($fontFile);
        $color=imagecolorallocate($img,rand(220,255),rand(109,129),rand(0,29));
        for($i=0;$i<4;$i++){
            $x=floor(($width-5)/4)*$i+5;
            $y=rand(20,30);
            $angle = rand(-10,10);
            $size = rand(15,18);
            imagefttext ($img, $size, $angle, $x, $y, $color, $fontFile, $code{$i});
        }
        imagepng($img);
        die;
    }

    public static function validVerfCode($moduleName,$verfCode)
    {
        if (YII_ENV == "dev") {
            return true;
        } else {
            return strlen($verfCode) == 4
            && strtolower(\Yii::$app->getSession()->get(self::getSessionKey($moduleName))) == strtolower($verfCode);
        }
    }
}