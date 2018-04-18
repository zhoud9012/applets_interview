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

    const ERR_CANDIDATES_INFO = '0010101';   //应试者信息过滤查询

}
