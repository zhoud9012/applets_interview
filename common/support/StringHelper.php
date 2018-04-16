<?php

namespace common\support;

class StringHelper {

    /**
     * 36位GUID
     * @return string
     */
    public static function uuid() {
        list($usec, $sec) = explode(" ", microtime(false));
        $usec = (string) ($usec * 10000000);
        $timestamp = bcadd(bcadd(bcmul($sec, "10000000"), (string) $usec), "621355968000000000");
        $ticks = bcdiv($timestamp, 10000);
        $maxUint = 4294967295;
        $high = bcdiv($ticks, $maxUint) + 0;
        $low = bcmod($ticks, $maxUint) - $high;
        $highBit = (pack("N*", $high));
        $lowBit = (pack("N*", $low));
        $guid = str_pad(dechex(ord($highBit[2])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($highBit[3])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[0])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[1])), 2, "0", STR_PAD_LEFT) . "-" . str_pad(dechex(ord($lowBit[2])), 2, "0", STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[3])), 2, "0", STR_PAD_LEFT) . "-";
        $chars = "abcdef0123456789";
        for ($i = 0; $i < 4; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }
        $guid .= "-";
        for ($i = 0; $i < 4; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }
        $guid .= "-";
        for ($i = 0; $i < 12; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }

        return $guid;
    }

    /**
     * 电话号码格式化：隐藏中间四位
     * @param $str
     * @return string
     */
    public static function maskMobile($str) {
        if ($str && strlen($str) > 4) {
            //反转从第5位取
            $str = strrev($str);
            $str = substr($str, 0, 4) . "****" . substr($str, 8);
            return strrev($str);
        }
        return $str;
    }

    /**
     * 隐藏身份证/军官证之类的：中间5位
     * @param $str
     * @return string
     */
    public static function maskIdentity($str) {
        if (!$str) {
            return '';
        }

        $len = strlen($str);

        switch ($len) {
            case 15:
                return substr($str, 0, 7) . "*****" . substr($str, 12, 3);
                break;
            case 18:
                return substr($str, 0, 9) . "*****" . substr($str, 14, 4);
                break;
            default:
                if ($len > 5) {
                    $index = ceil(($len - 5) / 2);
                    return substr($str, 0, $index) . "*****" . substr($str, $index + 5, $len - $index - 5);
                } else {
                    return $str;
                }
        }
    }

    /**
     * 获取数组格式的图片地址
     *  
     * @param  json  $pciArr
     * @return array         
     */
    public static function getPicArrUrl($picJson)
    {
        $return = [];

        $picArr = json_decode($picJson,true);
        foreach ($picArr as $pic) {
            $return[] = $pic['url'];
        }
        return $return;
    }

    /**
     * 获取.net后台文件上传控件数据中的图片地址
     * @param type $pic_url
     * @return string
     */
    public static function getPicUrl($pic_url) {
        if (!empty($pic_url)) {
            $obj = json_decode($pic_url, true);
            if ($obj) {
                return $obj[0]["url"]; //url地址
            }
        }
        return "";
    }

    /**
     * 将数组格式的图片地址转换格式
     * 
     * @param  array  $imgArr 
     * @return array         
     */
    public static function convertArrToOldImgUrl(array $imgArr)
    {
        $return = [];
        foreach ($imgArr as $img_url) {
            $return[] = [
                "name" => basename($img_url),
                "url" => $img_url,
                "preview" => $img_url
            ];
        }
        return json_encode($return);
    }

    /**
     * 将图片地址转换为与.net上传控件兼容的格式
     * @param type $img_url
     * @return type
     */
    public static function convertToOldImgUrl($img_url){
        $data = [
            [
                "name" => basename($img_url),
                "url" => $img_url,
                "preview" => $img_url
            ]
        ];
        return json_encode($data);
    }
    
    /**
     * 判断是否是日期
     * @param type 日期字符串
     * @param type 日期格式
     * @return boolean
     */
    public static function is_date($str, $format = "Y-m-d H:i:s") {
        $unixTime_1 = strtotime($str);
        if (!is_numeric($unixTime_1)) {
            return false; //如果非日期，则返回
        }

        $checkDate = date($format, $unixTime_1);
        $unixTime_2 = strtotime($checkDate);

        return $unixTime_1 == $unixTime_2;
    }

    /**
     * 从文件路径中获取文件扩展名
     * @param type $fileName
     * @return string
     */
    public static function getFileExtName($fileName){
        if(is_null($fileName) || empty($fileName)){
            return '';
        }
        $extend = pathinfo($fileName);
        if(is_null($extend)){
            return '';
        }
        $extend = strtolower($extend["extension"]);
        return $extend;
    }

    /**
     * 获取最后11位手机号
     * @param $mobilePhone
     * @return mixed
     */
    public static function getLastMobilePhone($mobilePhone)
    {
        if (strlen($mobilePhone) > 11) {
            return substr($mobilePhone, -11);
        }
        return $mobilePhone;
    }

    /**
     * 格式化时间
     * @param string $time 时间
     * @return bool|string
     */
    public static function formatDate($time, $format = 'Y-m-d')
    {
        if(empty($time) || $time == '0000-00-00 00:00:00'){
            return '';
        }
        return date($format, strtotime($time));
    }
}
