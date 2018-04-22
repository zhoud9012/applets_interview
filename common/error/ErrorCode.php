<?php

namespace common\error;

/**
 * ErrorCode module definition class
 */
class ErrorCode
{
    const SUCCESS = '0';    //0表示成功

    /**
     * 100-99-99 //控制器-动作-具体错误
     * 错误码由7位数字组成，以负数字符串表示，前三位错误码标识模块（可理解为service，比如项目模块100，中介机构模块101等），
     * 模块部分从100开始，到999，紧接着的两位错误码标识模块下的动作，从01开始，到99，最后
     * 两位错误码标识具体的错误，从01开始，到99，所有错误码定义为常量，以ERR_做前缀
     * 活动模块：-100xxxx
     */

    /*业务错误*/
    const ERR_CANDIDATES_INFO_A = '0010101';   //应试者信息过滤查询
    const ERR_WECHAT_LOGIN_INFO_A = '0010201';   //微信登陆必须传Code
    const ERR_WECHAT_LOGIN_INFO_B = '0010202';   //需要手机号确认角色

    /*系统错误*/

    //上传错误
    const ERR_SYSTEM_UPLOAD_INFO_A = '0050101';   //上传文件大小超过php.ini中upload_max_filesize 选项限制的值
    const ERR_SYSTEM_UPLOAD_INFO_B = '0050102';   //上传文件大小超过 HTML 表单中 MAX_FILE_SIZE 选项指定的值
    const ERR_SYSTEM_UPLOAD_INFO_C = '0050103';   //文件只有部分被上传
    const ERR_SYSTEM_UPLOAD_INFO_D = '0050104';   //没有文件被上传
    const ERR_SYSTEM_UPLOAD_INFO_DEF = '0050105';   //请查看上传错误码

    //EXCEL导入错误
    const ERR_SYSTEM_EXCEL_IMPORT_DEF = '0050201';  //php excel 插件异常
    const ERR_SYSTEM_EXCEL_IMPORT_A = '0050202';  //php excel 返回结果非数组
    const ERR_SYSTEM_EXCEL_IMPORT_B = '0050203';  //日期格式不对

}
