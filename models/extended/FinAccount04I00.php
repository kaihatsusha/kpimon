<?php
namespace app\models\extended;

use app\components\DateTimeUtils;
use app\components\NumberUtils;
use app\models\FinAccount;

class FinAccount04I00 extends FinAccount {
	public $closing_diff = null;
	public $closing_interest_unit = null;
	public $closing_interest = null;
	public $now_diff = null;
	public $now_interest_unit = null;
	public $now_interest = null;
	
	protected function initialize() {
		$oTime = DateTimeUtils::getDateTimeFromDB($this->opening_date);
		$cTime = DateTimeUtils::getDateTimeFromDB($this->closing_date);
		$now = new \DateTime();
		
		$this->closing_diff = $cTime->diff($oTime)->days;
		$this->now_diff = $now->diff($oTime)->days;
		
		// calculate Closing Interest
		$this->closing_interest_unit = NumberUtils::getInterest($this->opening_balance, $this->term_interest_rate, NumberUtils::NUM_ROUND);
		$this->closing_interest = $this->closing_interest_unit * $this->closing_diff;
		$this->closing_balance = $this->opening_balance + $this->closing_interest;
		
		// calculate Interest of current date
		$this->now_interest_unit = NumberUtils::getInterest($this->opening_balance, $this->noterm_interest_rate, NumberUtils::NUM_ROUND);
		$delta = $this->now_diff - $this->closing_diff;
		if ($delta < 0) {
			$this->now_interest = $this->now_interest_unit * $this->now_diff;
		} else {
			$this->now_interest = $this->closing_interest + NumberUtils::getInterest($this->closing_balance, $this->noterm_interest_rate, NumberUtils::NUM_ROUND, $delta);
		}
	}
}
?>