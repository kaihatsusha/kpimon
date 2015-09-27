<?php
namespace app\widgets\main;

use \yii\base\Widget;

class MainSidebarWidget extends Widget {
	public $user = null;
	
	public function run() {
		return $this->render('mainSidebar', ['user'=>$this->user]);
	}
}