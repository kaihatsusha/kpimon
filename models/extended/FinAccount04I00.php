<?php
namespace app\models\extended;

use app\components\DateTimeUtils;
use app\models\FinAccount;

class FinAccount04I00 extends FinAccount {
	public $closing_diff = null;
	public $now_diff = null;
	
	protected function initialize() {
		$oTime = DateTimeUtils::getDateTimeFromDB($this->opening_date);
		$cTime = DateTimeUtils::getDateTimeFromDB($this->closing_date);
		$now = new \DateTime();
		
		$this->closing_diff = $cTime->diff($oTime)->days;
		$this->now_diff = $now->diff($oTime)->days;
				/*
opening_balance
closing_balance
capital
*/
	}
}
?>