<?php
namespace app\controllers;

use yii\web\Controller;

class MobiledetectController extends Controller {
	public function render($view, $params = []) {
		$devicedetect = \Yii::$app->devicedetect;
		$isMobile = $devicedetect->isMobile();
		$isTablet = $devicedetect->isTablet();
		$mobileTpl = $isMobile && !$isTablet;
		
		$detectView = ($mobileTpl ? 'sp_' : '') . $view;
		$detectPath = parent::getViewPath() . "/$detectView.php";
		
		return parent::render(file_exists($detectPath) ? $detectView : $view, $params);
	}
}
?>