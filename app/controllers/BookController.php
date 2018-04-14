<?php

namespace app\controllers;
use yii\web\Response;

class BookController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Book';
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }
}
