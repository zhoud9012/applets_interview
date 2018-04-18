<?php

namespace app\controllers;

use yii;
use yii\web\Response;
use common\error\ReturnErrorTrait;

Abstract class BaseController extends \yii\rest\Controller
{

    /**
     * 格式化
     * @param $event
     */
    public function formatDataBeforeSend($event){
        $sender = $event->sender;
        //自已定义失败的返回
        if ($sender->data !== null && $sender->isSuccessful==false) {
            $sender->data = [
                'error'    => ReturnErrorTrait::getErrCode(),
                'status'  => $sender->statusCode,
                'message' => ReturnErrorTrait::getErrMsg(),
            ];
        }
    }

    /**
     * 输出json格式数据
     * @param string|bool|array $data 输出数据
     * @param string|int $retCode 自定义错误码
     * @param string $errMsg 错误信息
     * @param bool $sucess 是否设置500错误码
     */
    protected function exportJson($data = [], $retCode = 0, $errMsg = '', $sucess = true) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($sucess === false) {
            Yii::$app->response->setStatusCode(500);
        }
        $exportData['data'] = $data;
        $exportData['retCode'] = $retCode;
        $exportData['errMsg'] = $errMsg;

        Yii::$app->response->data = $exportData;

        Yii::$app->end();
    }
}
