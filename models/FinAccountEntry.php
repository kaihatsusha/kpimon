<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "fin_account_entry".
 *
 * @property string $entry_id
 * @property string $entry_date
 * @property string $entry_value
 * @property string $account_source
 * @property string $account_target
 * @property integer $entry_status
 * @property string $description
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class FinAccountEntry extends \yii\db\ActiveRecord {
	const SCENARIO_CREATE = 'create';
	
	public static $_PHP_FM_SHORTDATE = 'Y-m-d';
	
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'fin_account_entry';
    }
	
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['entry_date', 'create_date', 'update_date'], 'safe'],
            [['entry_value', 'account_source', 'account_target', 'entry_status'], 'integer'],
            [['description'], 'string'],
            [['delete_flag'], 'string', 'max' => 1],
			[['entry_date', 'entry_value'], 'required', 'on'=>[self::SCENARIO_CREATE]],
			[['entry_date'], 'date', 'format'=> 'php:' . self::$_PHP_FM_SHORTDATE, 'on'=>[self::SCENARIO_CREATE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'entry_id' => Yii::t('fin.models', 'Entry ID'),
            'entry_date' => Yii::t('fin.models', 'Entry Date'),
            'entry_value' => Yii::t('fin.models', 'Entry Value'),
            'account_source' => Yii::t('fin.models', 'Account Source'),
            'account_target' => Yii::t('fin.models', 'Account Target'),
            'entry_status' => Yii::t('fin.models', 'Entry Status'),
            'description' => Yii::t('fin.models', 'Description'),
            'create_date' => Yii::t('fin.models', 'Create Date'),
            'update_date' => Yii::t('fin.models', 'Update Date'),
            'delete_flag' => Yii::t('fin.models', 'Delete Flag'),
        ];
    }
}