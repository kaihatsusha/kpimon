<?php
namespace app\models\extended;

use \app\models\FinAccount;

class FinAccount06I00 extends FinAccount {
	protected function initialize() {
		$this->opening_balance = $this->closing_balance;
		$this->capital = 0;
	}
}
?>