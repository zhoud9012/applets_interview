<?php

namespace console\controllers\script;
use yii\base\Controller;
use Yii;
class TestController extends Controller
{
    public function actionIndex(){
        echo '6666';
    }

    public function actionMy($param1,$param2=''){
        echo "param1:".$param1;
        echo "param2:".$param2;
    }

}
