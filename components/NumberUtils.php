<?php
namespace app\components;

class NumberUtils {
	public static function format($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',') {
		return number_format($number, $decimals, $decPoint, $thousandsSep);
	}
}