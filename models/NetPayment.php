<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "net_payment".
 *
 * @property integer $customer_id
 * @property string $entry_date
 * @property integer $credit
 * @property integer $debit
 * @property string $order_id
 * @property string $create_date
 * @property string $update_date
 * @property string $secret_key
 * @property string $delete_flag
 */
class NetPayment extends \yii\db\ActiveRecord {
    public $entry_date_from = null;
    public $entry_date_to = null;
    public $credit_old = null;
    public $entry_date_old = null;
    public $bill_date = null;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'net_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['entry_date', 'entry_date_from', 'entry_date_to', 'customer_id', 'credit'], 'safe'],
            [['secret_key'], 'string', 'max' => 50],
            [['delete_flag'], 'string', 'max' => 1],
            [['entry_date_from', 'entry_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]],
            [['customer_id', 'entry_date', 'credit'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['credit'], 'integer', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['entry_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['entry_date'], 'unique', 'targetAttribute' => ['customer_id', 'entry_date'], 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'customer_id' => Yii::t('fin.models', 'Customer'),
            'entry_date' => Yii::t('fin.models', 'Entry Date'),
            'entry_date_from' => Yii::t('fin.models', 'Entry Date From'),
            'entry_date_to' => Yii::t('fin.models', 'Entry Date To'),
            'credit' => Yii::t('fin.models', 'Credit'),
            'debit' => Yii::t('fin.models', 'Debit'),
            'order_id' => Yii::t('fin.models', 'Bill ID'),
            'create_date' => Yii::t('fin.models', 'Create Date'),
            'update_date' => Yii::t('fin.models', 'Update Date'),
            'secret_key' => Yii::t('fin.models', 'Secret Key'),
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
}