<?php
namespace app\components;

class NumberUtils {
	const NUM_ROUND = 1;
	const NUM_FLOOR = 2;
	const NUM_CEIL = 3;

	/**
	 * format number
	 * @param type $number
	 * @param type $decimals
	 * @param type $decPoint
	 * @param type $thousandsSep
	 * @return type
	 */
	public static function format($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',') {
		return number_format($number, $decimals, $decPoint, $thousandsSep);
	}
	
	/**
	 * rounds number
	 * @param type $number
	 * @param type $rule
	 */
	public static function rounds($number, $rule = 1) {
		$result = null;
		switch($rule) {
			case self::NUM_ROUND:
				$result = round($number);
				break;
			case self::NUM_FLOOR:
				$result = floor($number);
				break;
			case self::NUM_CEIL:
				$result = ceil($number);
				break;
			default:
				$result = $number;
				break;
		}
		return $result;
	}
	
	/**
	 * get Interest base capital
	 * @param type $capital
	 * @param type $rateYear
	 * @param type $round
	 * @param type $days
	 * @param type $dayInYear
	 */
	public static function getInterest($capital, $rateYear, $round = 0, $days = 1, $dayInYear = 360) {
		if (is_null($capital)) {
			return 0;
		}
		
		$interest = ($days * $capital * $rateYear) / ($dayInYear * 100);
		return self::rounds($interest, $round);
	}
}