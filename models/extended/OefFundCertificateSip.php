<?php
namespace app\models\extended;

use app\models\OefFundCertificate;
use app\components\DateTimeUtils;
use app\components\ParamUtils;

class OefFundCertificateSip extends OefFundCertificate {
    /**
     * @param OefPurchase $purchase
     * @param OefFundCertificate $condition
     */
    public function initialize($purchase, $condition) {
        $this->sip_date = DateTimeUtils::getDateFromDB($purchase->sip_date);
        $this->sip_months = DateTimeUtils::diffMonths($this->sip_date, $condition->sell_date_obj);
        parent::initialize($purchase, $condition);
    }

    protected function calculate() {
        $this->sip_exit_fee_rate = ParamUtils::getExitSipFreeRate($this->sip_months);
        $this->sell_fee_discount_rate = ParamUtils::getSaleFeeDiscountRate($this->purchase_type);
        $saleFeeRate = ParamUtils::getSaleFeeRate($this->kept_months);
        $this->sell_fee_rate = ($saleFeeRate + $this->sip_exit_fee_rate)*(1 - $this->sell_fee_discount_rate/100);
        $this->sell_fee = ($this->sell_fee_rate * $this->revenue) / 100;
        $this->profit_before_taxes = $this->revenue - $this->sell_fee;
        $this->income_tax = ($this->profit_before_taxes * $this->income_tax_rate) / 100;
        $this->profit_after_taxes = $this->profit_before_taxes - $this->income_tax;
        $this->investment_result = $this->profit_after_taxes - $this->investment;
    }
}