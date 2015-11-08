<?php
namespace app\models;

use Yii;
use yii\db\Expression;

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
    const SCENARIO_LIST = 'list';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_COPY = 'copy';

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

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
            [['saving_account', 'current_assets'], 'required'],
            [['saving_account', 'current_assets', 'interest_add', 'entry_value', 'add_flag'], 'integer'],
            [['interest_unit', 'interest_rate'], 'number'],
            [['opening_date', 'closing_date', 'create_date', 'update_date'], 'safe'],
            [['delete_flag'], 'string', 'max' => 1],
            [['opening_date', 'closing_date', 'interest_rate', 'interest_add', 'entry_value'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
            [['opening_date', 'closing_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
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
}
