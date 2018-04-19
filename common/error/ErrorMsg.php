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
    public static $errMsg = [

        /*业务错误*/
        ErrorCode::ERR_CANDIDATES_INFO => '应试者信息过滤查询信息为空',

        /*系统错误*/
        //上传错误
        ErrorCode::ERR_SYSTEM_UPLOAD_INFO_A => '上传文件大小超过php.ini中upload_max_filesize 选项限制的值',
        ErrorCode::ERR_SYSTEM_UPLOAD_INFO_B => '上传文件大小超过 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        ErrorCode::ERR_SYSTEM_UPLOAD_INFO_C => '文件只有部分被上传',
        ErrorCode::ERR_SYSTEM_UPLOAD_INFO_D => '没有文件被上传',
        ErrorCode::ERR_SYSTEM_UPLOAD_INFO_DEF => '请查看上传错误码',
        ErrorCode::ERR_SYSTEM_EXCEL_IMPORT_DEF => 'php excel 插件异常',
        ErrorCode::ERR_SYSTEM_EXCEL_IMPORT_A => 'php excel 返回结果非数组',
        ErrorCode::ERR_SYSTEM_EXCEL_IMPORT_B => 'php excel 日期格式不对',

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
