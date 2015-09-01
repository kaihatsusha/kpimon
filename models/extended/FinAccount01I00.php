<?php
namespace app\models\extended;

use app\models\FinAccount;

class FinAccount01I00 extends FinAccount {
	public $now_balance = null;
	
	protected function initialize() {
		$this->closing_balance = $this->opening_balance;
		$this->now_balance = $this->opening_balance;
	}
}
?>