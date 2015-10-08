<?php
namespace app\components;

class MasterValueUtils {
	const SM_MODE_NAME = 'submitmode';
	const SM_MODE_INPUT = 1;
	const SM_MODE_CONFIRM = 2;
	const SM_MODE_BACK = 3;
	
	const PG_MODE_NAME = 'pagemode';
	const PG_MODE_CREATE = 1;
	const PG_MODE_EDIT = 2;
	
	const FLASH_SUCCESS = 'success';
	const FLASH_ERROR = 'error';
	
	const CSS_COLOR_EVEN = 'info';		// row % 2 == 0
	const CSS_COLOR_ODD = 'success';	// row % 2 == 1
	
	const MV_FIN_ACCOUNT_TYPE_CASH = 1;			// Cash
	const MV_FIN_ACCOUNT_TYPE_CURRENT = 2;		// Current account
	const MV_FIN_ACCOUNT_TYPE_CREDIT = 3;		// Credit card
	const MV_FIN_ACCOUNT_TYPE_TIME_DEPOSIT = 4;	// Time deposits
	const MV_FIN_ACCOUNT_TYPE_ADVANCE = 5;		// Advances
	const MV_FIN_ACCOUNT_TYPE_OIR = 6;			// Other internal receivables
	
	const MV_FIN_ENTRY_TYPE_SIMPLE = 1;		// Simple
	const MV_FIN_ENTRY_TYPE_DEPOSIT = 2;	// Internet
	const MV_FIN_ENTRY_TYPE_INTERNET = 3;	// Internet
	
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
}
?>