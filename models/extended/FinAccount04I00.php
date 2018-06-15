<?php
namespace app\models\extended;

use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\NumberUtils;
use app\models\FinAccount;

class FinAccount04I00 extends FinAccount {
	public $closing_diff = null;
	public $closing_interest_unit = null;
	public $closing_interest = null;
	public $now_diff = null;
	public $now_interest_unit = null;
	public $now_interest = null;
	public $result_interest = null;
	public $past_time = null;
	
	public function initialize() {
		$oTime = DateTimeUtils::getDateTimeFromDB($this->opening_date);
		$cTime = DateTimeUtils::getDateTimeFromDB($this->closing_date);
		$now = new \DateTime();

		// check in the past time
		$pastDateInterval = $now->diff($cTime);
		$this->past_time = ($pastDateInterval->invert == 1) && ($pastDateInterval->days > 0);

		// check Time deposits OFF
		if (MasterValueUtils::MV_FIN_ACCOUNT_TIME_DEPOSIT_FLAG_ON != $this->time_deposit_flag) {
			$this->term_interest_rate = 0;
			$this->noterm_interest_rate = 0;
		}

		$this->closing_diff = $cTime->diff($oTime)->days;
		$this->now_diff = $now->diff($oTime)->days;
		
		// calculate Closing Interest
		$this->closing_interest_unit = NumberUtils::getInterest($this->opening_balance, $this->term_interest_rate, 0, 1, $this->days_of_year);
		$this->closing_interest = NumberUtils::rounds($this->closing_interest_unit * $this->closing_diff, NumberUtils::NUM_ROUND);
		$this->closing_balance = $this->opening_balance + $this->closing_interest;
		
		// calculate Interest of current date
		$this->now_interest_unit = NumberUtils::getInterest($this->opening_balance, $this->noterm_interest_rate, 0, 1, $this->days_of_year);
		$delta = $this->now_diff - $this->closing_diff;
		if ($delta < 0) {
			$this->now_interest = NumberUtils::rounds($this->now_interest_unit * $this->now_diff, NumberUtils::NUM_ROUND);
		} else {
			$this->now_interest = $this->closing_interest + NumberUtils::getInterest($this->closing_balance, $this->noterm_interest_rate, NumberUtils::NUM_ROUND, $delta, $this->days_of_year);
		}
		
		// result
		$this->result_interest = $this->opening_balance - $this->capital;
	}
}
?>