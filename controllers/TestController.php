<?php

namespace app\controllers;

class TestController extends \yii\web\Controller
{
    public function actionIndex()
    {
     // test commit
        return $this->render('index');
    }

}
