<?php
namespace app\models\extended;

use app\models\FinAccount;

class FinAccount03I00 extends FinAccount {
	protected function initialize() {
		$this->opening_balance = 0;
		$this->capital = 0;
	}
}
?>