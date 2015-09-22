<?php
namespace app\components;

use yii\helpers\ArrayHelper;

class ModelUtils {
	/**
	 * getArrData
	 * @param type $activeQuery
	 * @param type $key
	 * @param type $label
	 * @param type $conditions
	 * @param type $order
	 * @return array
	 */
	public static function getArrData($activeQuery, $key, $label, $conditions = [], $order = '') {
		$arrFinAccount = $activeQuery->select([$key, $label])->where($conditions)->orderBy($order)->asArray()->all();
		return ArrayHelper::map($arrFinAccount, $key, $label);
	}
}
?>