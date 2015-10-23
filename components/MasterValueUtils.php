<?php
namespace app\components;

use app\components\ModelUtils;
use app\models\MasterValue;

class MasterValueUtils {
	const SM_MODE_NAME = 'submitmode';
	const SM_MODE_INPUT = 1;
	const SM_MODE_CONFIRM = 2;
	const SM_MODE_BACK = 3;
	
	const PG_MODE_NAME = 'pagemode';
	const PG_MODE_CREATE = 1;
	const PG_MODE_EDIT = 2;
	const PG_MODE_COPY = 3;
	
	const FLASH_SUCCESS = 'success';
	const FLASH_ERROR = 'error';
	
	const CSS_COLOR_EVEN = 'info';		// row % 2 == 0
	const CSS_COLOR_ODD = 'success';	// row % 2 == 1
	
	const MV_FIN_ACCOUNT_NONE = 0;	// NONE
	
	const MV_FIN_ACCOUNT_TYPE_CASH = 1;			// Cash
	const MV_FIN_ACCOUNT_TYPE_CURRENT = 2;		// Current account
	const MV_FIN_ACCOUNT_TYPE_CREDIT = 3;		// Credit card
	const MV_FIN_ACCOUNT_TYPE_TIME_DEPOSIT = 4;	// Time deposits
	const MV_FIN_ACCOUNT_TYPE_ADVANCE = 5;		// Advances
	const MV_FIN_ACCOUNT_TYPE_OIR = 6;			// Other internal receivables
	
	const MV_FIN_ENTRY_TYPE_SIMPLE = 1;				// Simple
	const MV_FIN_ENTRY_TYPE_DEPOSIT = 2;			// Deposits
	const MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT = 3;	// Interest of Deposits
	const MV_FIN_ENTRY_TYPE_COST_INTERNET = 4;		// Cost of Internet
	
	const MV_FIN_FLG_DELETE_TRUE = 1;	// is deleted
	const MV_FIN_FLG_DELETE_FALSE = 0;	// not delete
	
	/**
	 * get color of a row
	 * @param Number $index
	 * @return String
	 */
	public static function getColorRow($index) {
		return ($index % 2 == 0) ? self::CSS_COLOR_EVEN : self::CSS_COLOR_ODD;
	}
	
	/**
	 * get data as an Array [value_code=>locale]
	 * @param type $code
	 * @return Array
	 */
	public static function getArrData($code) {
		$locale = 'en';
		$arrData = ModelUtils::getArrData(MasterValue::find()->select(['value', 'label'])
				->where(['delete_flag'=>self::MV_FIN_FLG_DELETE_FALSE, 'value_code'=>$code, 'locale'=>$locale])
				->orderBy('order'), 'value', 'label');
		return $arrData;
	}
}
?>