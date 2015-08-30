<?php
namespace app\models\extended;

use app\models\FinAccount;

class FinAccount01I00 extends FinAccount {
	protected function initialize() {
		$this->opening_balance = 0;
		$this->capital = 0;
	}
}
?>