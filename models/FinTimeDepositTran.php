<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;
use app\components\StringUtils;

/**
 * This is the model class for table "fin_time_deposit_tran".
 *
 * @property string $transactions_id
 * @property integer $saving_account
 * @property integer $current_assets
 * @property double $interest_unit
 * @property double $interest_rate
 * @property integer $interest_add
 * @property integer $entry_value
 * @property integer $add_flag
 * @property string $opening_date
 * @property string $closing_date
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class FinTimeDepositTran extends \yii\db\ActiveRecord {
    public $opening_date_from = null;
    public $opening_date_to = null;
    public $withdrawal_value = null;
    public $adding_value = null;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';
    public static $_ARR_SAVING_ACOUNT = null;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'fin_time_deposit_tran';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['saving_account', 'current_assets', 'interest_add', 'entry_value', 'add_flag'], 'integer'],
            [['interest_unit', 'interest_rate'], 'number'],
            [['opening_date', 'opening_date_from', 'opening_date_to', 'closing_date', 'create_date', 'update_date'], 'safe'],
            [['delete_flag'], 'string', 'max' => 1],
            [['opening_date', 'closing_date', 'interest_rate', 'interest_add', 'entry_value', 'saving_account', 'current_assets'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['interest_add', 'entry_value'], 'integer', 'min' => 0, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['interest_rate'], 'number', 'min' => 0, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['saving_account'], 'unique', 'targetAttribute' => ['saving_account', 'opening_date'], 'message' => Yii::t('yii', '{attribute} "{{value}}" has already been taken.'), 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['opening_date'], 'unique', 'targetAttribute' => ['saving_account', 'opening_date'], 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['opening_date', 'closing_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['opening_date_from', 'opening_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'transactions_id' => Yii::t('fin.models', 'Transactions ID'),
            'saving_account' => Yii::t('fin.models', 'Saving Account'),
            'current_assets' => Yii::t('fin.models', 'Current Assets'),
            'interest_unit' => Yii::t('fin.models', 'Unit'),
            'interest_rate' => Yii::t('fin.models', 'Rate'),
            'interest_add' => Yii::t('fin.models', 'Interest'),
            'entry_value' => Yii::t('fin.models', 'Principal Amount'),
            'add_flag' => Yii::t('fin.models', 'Amount Type'),
            'opening_date' => Yii::t('fin.models', 'Opening Date'),
            'opening_date_from' => Yii::t('fin.models', 'Opening Date From'),
            'opening_date_to' => Yii::t('fin.models', 'Opening Date To'),
            'closing_date' => Yii::t('fin.models', 'Closing Date'),
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
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->transactions_id = null;
            }
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate ( ) {
        $arrOldErrors = $this->getErrors('saving_account');
        if (count($arrOldErrors) > 0) {
            $this->clearErrors('saving_account');
            foreach ($arrOldErrors as $oldError) {
                $newError = null;
                if (empty($this->saving_account)) {
                    $newError = $oldError;
                } else {
                    $newError = StringUtils::format($oldError, [$this->saving_account => self::$_ARR_SAVING_ACOUNT[$this->saving_account]]);
                }
                $this->addError('saving_account', $newError);
            }
        }
    }
}
