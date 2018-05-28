<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\ParamUtils;

class OefFundCertificate extends Model {
    public $sell_fee;
    public $sell_fee_rate;
    public $sell_fee_discount_rate;
    public $sell_date;
    public $sell_date_obj;
    public $nav;
    public $sell_certificate;
    public $sellable_certificate;
    public $income_tax_rate;
    public $sum_sell_certificate;
    public $purchase_id;
    public $purchase_date;
    public $purchase_type;
    public $investment;
    public $investment_result;
    public $sip_date;
    public $sip_months;
    public $sip_exit_fee_rate;
    public $revenue;
    public $kept_months;
    public $profit_before_taxes;
    public $income_tax;
    public $profit_after_taxes;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @param OefPurchase $purchase
     * @param OefFundCertificate $condition
     */
    public function initialize($purchase, $condition) {
        $this->purchase_id = $purchase->id;
        $this->purchase_date = DateTimeUtils::getDateFromDB($purchase->purchase_date);
        $this->purchase_type = $purchase->purchase_type;
        $this->sellable_certificate = $purchase->found_stock - $purchase->found_stock_sold;
        $this->sell_date = $condition->sell_date_obj;

        $sellCertificate = $condition->sell_certificate - $condition->sum_sell_certificate;
        $this->sell_certificate = ($sellCertificate < $this->sellable_certificate) ? $sellCertificate : $this->sellable_certificate;
        $this->investment = $this->sell_certificate * ($purchase->purchase + $purchase->transfer_fee + $purchase->other_fee) / $purchase->found_stock;

        $this->revenue = $this->sell_certificate * $condition->nav;
        $this->kept_months = DateTimeUtils::diffMonths($this->purchase_date, $this->sell_date);
        $this->income_tax_rate = $condition->income_tax_rate;
        $this->calculate();
    }

    protected function calculate() {
        $this->sip_exit_fee_rate = 0;
        $this->sell_fee_discount_rate = ParamUtils::getSaleFeeDiscountRate($this->purchase_type);
        $saleFeeRate = ParamUtils::getSaleFeeRate($this->kept_months);
        $this->sell_fee_rate = ($saleFeeRate + $this->sip_exit_fee_rate)*(1 - $this->sell_fee_discount_rate/100);
        $this->sell_fee = ($this->sell_fee_rate * $this->revenue) / 100;
        $this->profit_before_taxes = $this->revenue - $this->sell_fee;
        $this->income_tax = ($this->profit_before_taxes * $this->income_tax_rate) / 100;
        $this->profit_after_taxes = $this->profit_before_taxes - $this->income_tax;
        $this->investment_result = $this->profit_after_taxes - $this->investment;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['sell_date', 'sell_certificate', 'income_tax_rate', 'nav'], 'safe'],
            [['sell_date', 'sell_certificate', 'income_tax_rate'], 'required', 'on' => [MasterValueUtils::SCENARIO_TOOL]],
            [['sell_certificate', 'income_tax_rate', 'nav'], 'number', 'on' => [MasterValueUtils::SCENARIO_TOOL]],
            [['sell_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_TOOL]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'sell_date' => Yii::t('oef.models', 'Sell Date'),
            'sell_certificate' => Yii::t('oef.models', 'Sell Certificate'),
            'income_tax_rate' => Yii::t('oef.models', 'Income Tax Rate'),
            'nav' => Yii::t('oef.models', 'NAV'),
        ];
    }
}