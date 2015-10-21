<?php
namespace app\models;

use Yii;
use yii\db\Expression;

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
	const SCENARIO_LIST = 'list';
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_COPY = 'copy';
	
	public $entry_date_from = null;
	public $entry_date_to = null;
	public $entry_adjust = 0;
	public $entry_updated = 0;
	public $arr_entry_log = null;
	public $account_source_old = null;
	public $account_target_old = null;
	
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
            [['entry_date', 'entry_date_from', 'entry_date_to', 'create_date', 'update_date', 'arr_entry_log'], 'safe'],
            [['entry_value', 'entry_adjust', 'account_source', 'account_target', 'entry_status'], 'integer'],
            [['description'], 'string'],
            [['delete_flag'], 'string', 'max' => 1],
			[['entry_date', 'entry_value'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_COPY]],
			[['entry_date'], 'required', 'on' => [self::SCENARIO_UPDATE]],
			[['account_source', 'account_target'], 'default', 'value' => 0, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY, self::SCENARIO_LIST]],
			[['account_source'], 'validateSourceRelatedTarget', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
			[['account_source'], 'validateEntryAdjust', 'on' => [self::SCENARIO_UPDATE]],
			[['entry_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
			[['entry_date_from', 'entry_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [self::SCENARIO_LIST]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'entry_id' => Yii::t('fin.models', 'Entry ID'),
            'entry_date' => Yii::t('fin.models', 'Entry Date'),
			'entry_date_from' => Yii::t('fin.models', 'Entry Date From'),
			'entry_date_to' => Yii::t('fin.models', 'Entry Date To'),
            'entry_value' => Yii::t('fin.models', 'Entry Value'),
			'entry_adjust' => Yii::t('fin.models', 'Adjustment'),
			'entry_updated' => Yii::t('fin.models', 'Updated Value'),
            'account_source' => Yii::t('fin.models', 'Debit Account'),
            'account_target' => Yii::t('fin.models', 'Credit Account'),
            'entry_status' => Yii::t('fin.models', 'Entry Status'),
            'description' => Yii::t('fin.models', 'Description'),
			'arr_entry_log' => Yii::t('fin.models', 'Description'),
            'create_date' => Yii::t('fin.models', 'Create Date'),
            'update_date' => Yii::t('fin.models', 'Update Date'),
            'delete_flag' => Yii::t('fin.models', 'Delete Flag'),
        ];
    }
	
	/**
     * @inheritdoc
     */
	public function behaviors() {
		return [
			'timestamp' => [
				'class' => 'yii\behaviors\TimestampBehavior',
				'attributes' => [
					\yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_date', 'update_date'],
					\yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['update_date'],
				],
				'value' => new Expression('NOW()'),
			],
		];
	}
	
	/**
	 * validate Source Related Target
	 */
	public function validateSourceRelatedTarget() {
		if ($this->account_source == 0 && $this->account_target == 0) {
			$this->addError('account_source', Yii::t('common', 'Debit Account and Credit Account must not be empty at the same time.'));
			$this->addError('account_target', Yii::t('common', 'Debit Account and Credit Account must not be empty at the same time.'));
			return false;
		}
		if ($this->account_source == $this->account_target) {
			$this->addError('account_source', Yii::t('common', 'Debit Account must be different to Credit Account.'));
			$this->addError('account_target', Yii::t('common', 'Credit Account must be different to Debit Account.'));
			return false;
		}
		return true;
	}
	
	/**
	 * validate Entry Adjust
	 */
	public function validateEntryAdjust() {
		$temp = $this->entry_value + $this->entry_adjust;
		if ($temp < 0) {
			$this->addError('entry_adjust', Yii::t('common', '{field} must be larger than {value}', ['field'=>$this->getAttributeLabel('entry_adjust'), 'value'=>-$this->entry_value]));
			return false;
		}
		return true;
	}
}