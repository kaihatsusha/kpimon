<?php
namespace app\components;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ModelUtils {
	/**
	 * Get data of Model as Array [$key=>$label]
	 * @param type $activeQuery
	 * @param type $key
	 * @param type $label
	 * @return array
	 */
	public static function getArrData($activeQuery, $key, $label) {
		$arrFinAccount = $activeQuery->asArray()->all();
		return ArrayHelper::map($arrFinAccount, $key, $label);
	}
	
	/**
	 * Convert a Model to an Array
	 * @param type $model
	 * @param type $properties
	 * @param type $properties
	 * @return array
	 */
	public static function toArray($model, $properties = []) {
		if (empty($properties)) {
			foreach ($model->getTableSchema()->columns as $column) {
				$properties[] = $column->name;
			}
		}
		return ArrayHelper::toArray($model, $properties);//
	}
	
	/**
	 * Convert a Model to a Json String
	 * @param type $model
	 * @param type $properties
	 * @return string
	 */
	public static function toJsonHtmlEncode($model, $properties = []) {
		return Json::htmlEncode(self::toArray($model, $properties));
	}
	
	/**
	 * Convert a Model to a Json String
	 * @param type $model
	 * @param type $properties
	 * @return string
	 */
	public static function toJsonEncode($model, $properties = []) {
		return Json::encode(self::toArray($model, $properties));
	}
}
?>