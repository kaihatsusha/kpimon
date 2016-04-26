<?php
namespace app\components;

use app\models\MasterValue;

class MasterValueUtils {
	const SM_MODE_NAME = 'submitmode';
	const SM_MODE_INPUT = 1;
	const SM_MODE_CONFIRM = 2;
	const SM_MODE_BACK = 3;
	const SM_MODE_LIST = 4;
	const SM_MODE_ADD_ITEM = 5;
	const SM_MODE_DEL_ITEM = 6;
	
	const PG_MODE_NAME = 'pagemode';
	const PG_MODE_CREATE = 1;
	const PG_MODE_EDIT = 2;
	const PG_MODE_COPY = 3;

	const SCENARIO_LIST = 'list';
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_COPY = 'copy';
	const SCENARIO_TOOL = 'tool';
	
	const FLASH_SUCCESS = 'success';
	const FLASH_ERROR = 'error';
	
	const CSS_COLOR_EVEN = 'info';		// row % 2 == 0
	const CSS_COLOR_ODD = 'success';	// row % 2 == 1
	
	const MV_FIN_ACCOUNT_NONE = 0;	// NONE
	const MV_FIN_ACCOUNT_ATM_VCB = 2;
	const MV_FIN_ACCOUNT_OTHER_FOUND = 9;
	const MV_FIN_ACCOUNT_VCBF_TBF = 11;
	
	const MV_FIN_ACCOUNT_TYPE_CASH = 1;			// Cash
	const MV_FIN_ACCOUNT_TYPE_CURRENT = 2;		// Current account
	const MV_FIN_ACCOUNT_TYPE_CREDIT = 3;		// Credit card
	const MV_FIN_ACCOUNT_TYPE_TIME_DEPOSIT = 4;	// Time deposits
	const MV_FIN_ACCOUNT_TYPE_ADVANCE = 5;		// Advances
	const MV_FIN_ACCOUNT_TYPE_OIR = 6;			// Other internal receivables
	const MV_FIN_ACCOUNT_TYPE_CCQ = 7;			// CCQ
	
	const MV_FIN_ENTRY_TYPE_SIMPLE = 1;				// Simple
	const MV_FIN_ENTRY_TYPE_DEPOSIT = 2;			// Deposits
	const MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT = 3;	// Interest of Deposits
	const MV_FIN_ENTRY_TYPE_COST_INTERNET = 4;		// Cost of Internet
	const MV_FIN_ENTRY_TYPE_INTEREST_VCBF_TBF = 5;	// Cost of Internet

	const MV_FIN_ENTRY_LOG_INTEREST = 13;	// Interest of Deposits
	const MV_FIN_ENTRY_LOG_TRANSFER = 14;	// Transfer
	const MV_FIN_ENTRY_LOG_SAVING = 15;		// Saving

	const MV_FIN_TIMEDP_TRANTYPE_ADDING = 1;		// Adding funds
	const MV_FIN_TIMEDP_TRANTYPE_WITHDRAWAL = 2;	// Partial withdrawal
	
	const MV_FIN_FLG_DELETE_TRUE = 1;	// is deleted
	const MV_FIN_FLG_DELETE_FALSE = 0;	// not delete

	const MV_NET_CUSTOMER_STATUS_ON = 1;	// ON
	const MV_NET_CUSTOMER_STATUS_OFF = 2;	// OFF

	const MV_JAR_ACCOUNT_STATUS_ON = 1;		// ON
	const MV_JAR_ACCOUNT_STATUS_OFF = 2;	// OFF

	const MV_JAR_ACCOUNT_TYPE_JAR = 1;	// Cash
	const MV_JAR_ACCOUNT_TYPE_TEMP = 2;	// Current account

	const MV_JAR_ACCOUNT_NONE = 0;	// NONE
	const MV_JAR_ACCOUNT_LTSS = 4;	// TEMP (VCBF_TBF, ...)
	const MV_JAR_ACCOUNT_TEMP = 9;	// TEMP

	const MV_JAR_ENTRY_TYPE_SIMPLE = 1;	// Simple
	const MV_JAR_ENTRY_TYPE_TEMP = 2;	// Temp

	const MV_OEF_PERCHASE_TYPE_NORMAL	= 1;
	const MV_OEF_PERCHASE_TYPE_SIP		= 2;
	const MV_OEF_PERCHASE_TYPE_DIVIDEND	= 3;
	const MV_OEF_PERCHASE_TYPE_IPO		= 4;
	
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