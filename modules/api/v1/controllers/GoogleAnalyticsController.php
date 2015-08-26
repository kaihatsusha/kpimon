<?php

namespace app\modules\api\v1\controllers;

use yii\web\Controller;
use app\components\GARequest;

class GoogleAnalyticsController extends Controller
{
	public function actionRequest(){
		$googleAnalytics = GARequest::install();
		$result = $googleAnalytics->realTimeCurl();
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $result;
	}  
}
