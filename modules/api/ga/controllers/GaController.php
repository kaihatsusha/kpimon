<?php

namespace app\modules\api\ga\controllers;

use yii\web\Controller;
use app\modules\api\ga\components\GARequest;
use app\modules\api\ga\components\GAUtils;
use Yii;
class GaController extends Controller
{
//	public function actionGetPageView($mediaId,$key){		
//		header('Content-Type: text/event-stream');
//        header('Cache-Control: no-cache');
//		
//		$googleAnalytics = GARequest::install();
//		$result = $googleAnalytics->getGAData($mediaId,$key);
//		
//		$last_time_haschange = Yii::$app->memCache->get('__GA__last-time-haschange-'.$mediaId);
//		Yii::warning("last_time_haschange_$mediaId _ $last_time_haschange" . ' xxx '. "resulttimestamp_$mediaId _ ". $result['timestamp']);
//		if ($result['timestamp'] != $last_time_haschange){
//			Yii::$app->memCache->set('__GA__last-time-haschange-'.$mediaId, $result['timestamp'], 1000);
//			echo "event: hasdata\n";
//			echo 'data: '.$result['value'];
//			echo "\n\n";
//		}else{
//			echo "event: ping\n";
//			$curDate = date(DATE_ISO8601);
//			echo 'data: {"time": "' . $curDate . '"}';
//			echo "\n\n";
//		}
//		flush();
//	}  
	
//	public function actionGetPageView($mediaId,$key){
//		$googleAnalytics = GARequest::install();
//		$last_time_haschange = null;
//		header('Content-Type: text/event-stream');
//        header('Cache-Control: no-cache');
//		
//		while (1) {
//			$result = $googleAnalytics->getGAData($mediaId,$key);
//			
//			echo "event: ping\n";
//			$curDate = date(DATE_ISO8601);
//			echo 'data: {"time": "' . $curDate . '"}';
//			echo "\n\n";
//			
//			if ($result['timestamp']  > $last_time_haschange){
//				$last_time_haschange = $result['timestamp'];
//				
//				echo "event: hasdata\n";
//	//			echo "data: Right now is: {$obj->rt_pvs_right_now->metricTotals[0]}\n\n";
//				echo 'data: '.$result['value'];
//				echo "\n\n";
//			}
//			ob_flush();
//			flush();
//			sleep(2);
//		}
//	}  
	
	public function actionGetPageView($mediaId,$key){
		$listMedia = array_filter(explode('|', $mediaId));
		$listKey = array_filter(explode('|', $key));
		$listIdAndKey = array_combine($listMedia, $listKey);
		
		$googleAnalytics = GARequest::install();
		$last_time_haschange = [];
		header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
		
		while (1) {					
			foreach ($listIdAndKey as $id => $key) {
				try {
					$result = $googleAnalytics->getGAData($id,$key);
					if(empty($result)||empty($result['timestamp'])){
						GAUtils::streamError($result,false,$id);
					}elseif (!isset($last_time_haschange[$id]) || $result['timestamp']  != $last_time_haschange[$id]){
						$last_time_haschange[$id] = $result['timestamp'];
						GAUtils::streamHasData($result['value'],$id);
					}else{
						GAUtils::streamPing($id);
					}

				} catch (\yii\base\Exception $exc) {
					$error = ['errno'=>$exc->getMessage()];
					GAUtils::streamError($error,false,$id);
				}

			}			
			ob_flush();
			flush();
			sleep(2);
		}
	}  
}
