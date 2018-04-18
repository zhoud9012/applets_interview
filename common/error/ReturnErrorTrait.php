<?php

namespace common\error;

/**
 * ErrorCode module definition class
 */
class ReturnErrorTrait
{
    public static function setAndReturn($errCode, $logMessage = '') {
        ErrorInfo::setAndReturn($errCode, $logMessage);
        return false;
    }

    public static function getErrCode() {
        $errCode = ErrorInfo::getErrCode();
        if ($errCode == '') {
            $errCode = ErrorCode::SUCCESS;
        }

        return $errCode;
    }

    public static function getErrMsg() {
        return ErrorInfo::getErrMsg();
    }

    public static function getLogMsg() {
        return ErrorInfo::getLogMsg();
    }

    public function getAllFirstErrorMessage(){
        if(empty($this->firstErrors)){
            return '';
        }
        return implode(';', $this->firstErrors);
    }
}
