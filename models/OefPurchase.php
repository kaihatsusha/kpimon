<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "oef_purchase".
 *
 * @property string $id
 * @property string $purchase_date
 * @property integer $purchase_type
 * @property string $sip_date
 * @property string $account
 * @property integer $purchase
 * @property double $purchase_fee_rate
 * @property integer $purchase_fee_rule
 * @property double $discount_rate
 * @property double $nav
 * @property double $found_stock_sold
 * @property double $found_stock
 * @property integer $found_stock_rule
 * @property integer $transfer_fee
 * @property integer $other_fee
 * @property string $fin_entry_id
 * @property string $description
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class OefPurchase extends \yii\db\ActiveRecord {
    public $purchase_date_from = null;
    public $purchase_date_to = null;
    public $purchase_fee = 0;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'oef_purchase';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['purchase_date', 'purchase_date_from', 'purchase_date_to', 'purchase_type', 'sip_date', 'nav', 'transfer_fee', 'purchase'], 'safe'],
            [['purchase_fee', 'purchase_fee_rule', 'found_stock_rule', 'other_fee', 'fin_entry_id'], 'integer'],
            [['purchase_fee_rate', 'discount_rate', 'found_stock_sold', 'found_stock'], 'number'],
            [['account'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 100],
            [['delete_flag'], 'string', 'max' => 1],
            [['purchase_date_from', 'purchase_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]],
            [['purchase_date', 'purchase_type', 'purchase', 'nav'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['purchase_date', 'sip_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['purchase_type', 'purchase', 'transfer_fee'], 'integer', 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['nav'], 'number', 'on' => [MasterValueUtils::SCENARIO_CREATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('oef.models', 'ID'),
            'purchase_date' => Yii::t('oef.models', 'Purchase Date'),
            'purchase_date_from' => Yii::t('oef.models', 'Purchase Date From'),
            'purchase_date_to' => Yii::t('oef.models', 'Purchase Date To'),
            'purchase_type' => Yii::t('oef.models', 'Purchase Type'),
            'sip_date' => Yii::t('oef.models', 'SIP Date'),
            'account' => Yii::t('oef.models', 'Account'),
            'purchase' => Yii::t('oef.models', 'Request Purchase'),
            'purchase_fee' => Yii::t('oef.models', 'Purchase Fee'),
            'purchase_fee_rate' => Yii::t('oef.models', 'Purchase Fee Rate'),
            'purchase_fee_rule' => Yii::t('oef.models', 'Purchase Fee Rule'),
            'discount_rate' => Yii::t('oef.models', 'Discount Rate'),
            'nav' => Yii::t('oef.models', 'NAV'),
            'found_stock_sold' => Yii::t('oef.models', 'Sold'),
            'found_stock' => Yii::t('oef.models', 'Found Stock'),
            'found_stock_rule' => Yii::t('oef.models', 'Found Stock Rule'),
            'transfer_fee' => Yii::t('oef.models', 'Transfer Fee'),
            'other_fee' => Yii::t('oef.models', 'Other Fee'),
            'fin_entry_id' => Yii::t('oef.models', 'Entry ID'),
            'description' => Yii::t('oef.models', 'Description'),
            'create_date' => Yii::t('oef.models', 'Create Date'),
            'update_date' => Yii::t('oef.models', 'Update Date'),
            'delete_flag' => Yii::t('oef.models', 'Delete Flag'),
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
