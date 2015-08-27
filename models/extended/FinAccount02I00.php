<?php
namespace app\models\extended;

use \app\models\FinAccount;

class FinAccount02I00 extends FinAccount {
	protected function initialize() {
		$this->opening_balance = 0;
		$this->capital = 0;
	}
}
?>