<?php
namespace app\models\extended;

use app\models\FinAccount;

class FinAccount07I00 extends FinAccount {
    public $now_balance = null;

    public function initialize() {
        $this->closing_balance = $this->opening_balance;
        $this->now_balance = $this->opening_balance;
    }
}