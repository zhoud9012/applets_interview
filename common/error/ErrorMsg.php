<?php

namespace common\error;

/**
 * ErrorCode module definition class
 */
class ErrorMsg
{
    /**
     * 定义错误描述信息
     * @var array
     */
    const ERR_CANDIDATES_INFO = '0010101';   //应试者信息过滤查询
    public static $errMsg = [
        ErrorCode::ERR_CANDIDATES_INFO => '应试者信息过滤查询信息为空',
    ];

    /**
     * 获取错误描述信息
     * @param $errCode
     * @return string
     */
    public static function getErrMsg($errCode) {
        return isset(self::$errMsg[$errCode]) ? self::$errMsg[$errCode] : self::getDefaultMsg();
    }

    /**
     * 获取默认的错误描述信息
     * @return string
     */
    public static function getDefaultMsg() {
        return '服务器错误';
    }

}
