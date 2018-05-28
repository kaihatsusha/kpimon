<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;
use app\components\NumberUtils;
use app\components\ParamUtils;

/**
 * This is the model class for table "oef_purchase".
 *
 * @property string $id
 * @property string $purchase_date
 * @property integer $purchase_type
 * @property string $sip_date
 * @property integer $purchase
 * @property integer $purchase_fee
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
 * @property string $jar_payment_id
 * @property string $description
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class OefPurchase extends \yii\db\ActiveRecord {
    public $purchase_date_from = null;
    public $purchase_date_to = null;
    public $investment_old = null;
    public $total_fee_rate = null;
    public $real_purchase = null;
    public $investment = null;
    public $found_certificate_status = null;

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
            [['purchase_date', 'purchase_date_from', 'purchase_date_to', 'purchase_type', 'sip_date', 'nav', 'transfer_fee', 'purchase', 'found_certificate_status'], 'safe'],
            [['purchase_fee', 'purchase_fee_rule', 'found_stock_rule', 'other_fee', 'fin_entry_id', 'jar_payment_id'], 'integer'],
            [['purchase_fee_rate', 'discount_rate', 'found_stock_sold', 'found_stock'], 'number'],
            [['description'], 'string', 'max' => 100],
            [['delete_flag'], 'string', 'max' => 1],
            [['purchase_date_from', 'purchase_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]],
            [['purchase_date', 'purchase_type', 'purchase', 'nav'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['purchase_date', 'sip_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['purchase_type', 'purchase', 'purchase_fee', 'transfer_fee', 'other_fee'], 'integer', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['nav'], 'number', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_TOOL]],
            [['other_fee', 'transfer_fee', 'purchase_type'], 'default', 'value' => 0, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['purchase_type'], 'validatePurchaseTypeSipDate', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['purchase_type', 'purchase', 'nav'], 'required', 'on' => [MasterValueUtils::SCENARIO_TOOL]],
            [['purchase_type', 'purchase'], 'integer', 'on' => [MasterValueUtils::SCENARIO_TOOL]]
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
            'purchase' => Yii::t('oef.models', 'Request Purchase'),
            'real_purchase' => Yii::t('oef.models', 'Real Purchase'),
            'purchase_fee' => Yii::t('oef.models', 'Purchase Fee'),
            'purchase_fee_rate' => Yii::t('oef.models', 'Purchase Fee Rate'),
            'purchase_fee_rule' => Yii::t('oef.models', 'Purchase Fee Rule'),
            'discount_rate' => Yii::t('oef.models', 'Discount Rate'),
            'total_fee_rate' => Yii::t('oef.models', 'Total Fee Rate'),
            'investment' => Yii::t('oef.models', 'Investment'),
            'nav' => Yii::t('oef.models', 'NAV'),
            'found_stock_sold' => Yii::t('oef.models', 'Sold'),
            'found_stock' => Yii::t('oef.models', 'Found Stock'),
            'found_certificate_status' => Yii::t('oef.models', 'Status'),
            'found_stock_rule' => Yii::t('oef.models', 'Found Stock Rule'),
            'transfer_fee' => Yii::t('oef.models', 'Transfer Fee'),
            'other_fee' => Yii::t('oef.models', 'Other Fee'),
            'fin_entry_id' => Yii::t('oef.models', 'Entry ID'),
            'jar_payment_id' => Yii::t('oef.models', 'Entry ID'),
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

    public function validatePurchaseTypeSipDate() {
        if ($this->purchase_type == MasterValueUtils::MV_OEF_PERCHASE_TYPE_SIP && empty($this->sip_date)) {
            $this->addError('sip_date', Yii::t('common', 'Sip Date must be inputed.'));
            return false;
        }
        return true;
    }

    public static function getModel($id) {
        $result = OefPurchase::findOne(['id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
        if (!is_null($result)) {
            $result->investment = $result->purchase + $result->transfer_fee + $result->other_fee;
            $result->investment_old = $result->investment;
            $result->total_fee_rate = $result->purchase_fee_rate * (100 - $result->discount_rate) / 100;
            $result->real_purchase = $result->purchase - $result->purchase_fee;
        }
        return $result;
    }

    public function calculate() {
        $this->purchase_fee_rate = ParamUtils::getPurchaseFeeRate($this->purchase);
        $this->discount_rate = ParamUtils::getPurchaseFeeDiscountRate($this->purchase_type);
        $this->total_fee_rate = $this->purchase_fee_rate * (100 - $this->discount_rate) / 100;
        $this->purchase_fee_rule = ParamUtils::getPurchaseFeeRule();
        $this->purchase_fee = NumberUtils::rounds(($this->purchase * $this->total_fee_rate) / 100, $this->purchase_fee_rule);
        $this->real_purchase = $this->purchase - $this->purchase_fee;
        $this->found_stock_rule = ParamUtils::getFoundStockRule();
        $this->found_stock = NumberUtils::rounds(100 * ($this->real_purchase / $this->nav), $this->found_stock_rule) / 100;
        $this->investment = $this->purchase + $this->transfer_fee + $this->other_fee;
        if ($this->purchase_type == MasterValueUtils::MV_OEF_PERCHASE_TYPE_DIVIDEND) {
            $this->fin_entry_id = 0;
            $this->jar_payment_id = 0;
        }
    }
}