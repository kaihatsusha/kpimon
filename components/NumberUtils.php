<?php
namespace app\components;

use app\components\StringUtils;

class NumberUtils {
	const NUM_ROUND = 1;
	const NUM_FLOOR = 2;
	const NUM_CEIL = 3;

	/**
	 * format number
	 * @param Number $number
	 * @param Int $decimals
	 * @param String $decPoint
	 * @param String $thousandsSep
	 * @return String
	 */
	public static function format($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',') {
		return number_format($number, $decimals, $decPoint, $thousandsSep);
	}
	
	/**
	 * rounds number
	 * @param Number $number
	 * @param Int $rule
	 * @return Number
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
	 * @param Number $capital
	 * @param Number $rateYear
	 * @param Int $round
	 * @param Int $days
	 * @param Int $dayInYear
	 * @return Number
	 */
	public static function getInterest($capital, $rateYear, $round = 0, $days = 1, $dayInYear = 360) {
		if (is_null($capital)) {
			return 0;
		}
		
		$interest = ($days * $capital * $rateYear) / ($dayInYear * 100);
		return self::rounds($interest, $round);
	}

	/**
	 * get color of a row
	 * @param Number $number
	 * @param Array $config template {icon}{number}{color}
	 * @param Int $decimals
	 * @param String $decPoint
	 * @param String $thousandsSep
	 * @return String
	 */
	public static function getIncDecNumber($number, $config = [], $decimals = 0, $decPoint = '.', $thousandsSep = ',') {
		if (!isset($config['template'])) {
			return self::format($number, $decimals, $decPoint, $thousandsSep);
		}
		// template sample {icon}{number}{color}
		$template = $config['template'];
		$numberStr = self::format(abs($number), $decimals, $decPoint, $thousandsSep);
		$params = ['number'=>$numberStr];

		if ($number < 0) {
			$params['color'] = isset($config['decColor']) ? $config['decColor'] : '';
			$params['icon'] = isset($config['decIcon']) ? $config['decIcon'] : '';
		} elseif ($number > 0) {
			$params['color'] = isset($config['incColor']) ? $config['incColor'] : '';
			$params['icon'] = isset($config['incIcon']) ? $config['incIcon'] : '';
		} else {
			$params['color'] = isset($config['zeroColor']) ? $config['zeroColor'] : '';
			$params['icon'] = isset($config['zeroIcon']) ? $config['zeroIcon'] : '';
		}
		return StringUtils::format($template, $params);
	}
}