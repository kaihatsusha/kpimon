<?php
namespace app\components;

use Yii;

class ParamUtils {
    const K_EXIT_FEE_RATE               = 'exit_fee_rate';
    const K_FOUND_STOCK_RULE            = 'found_stock_rule';
    const K_INCOME_TAX_RATE             = 'income_tax_rate';
    const K_MAX                         = 'max';
    const K_MIN                         = 'min';
    const K_PURCHASE_FEE                = 'purchase_fee';
    const K_PURCHASE_FEE_DISCOUNT_RATE  = 'purchase_fee_discount_rate';
    const K_PURCHASE_TYPE               = 'purchase_type';
    const K_RATE                        = 'rate';
    const K_RULE                        = 'rule';
    const K_SALE_FEE                    = 'sale_fee';
    const K_SALE_FEE_DISCOUNT_RATE      = 'sale_fee_discount_rate';
    const K_SIP_PROGRAM                 = 'sip_program';
    const K_VAL                         = 'val';

    /**
     * get Purchase Fee Rate
     * 1 TRIEU VND den 500 TRIEU VND (2,0%)
     * tren 500 TRIEU VND den 1 TY VND (1,5%)
     * tren 1 TY VND den 10 TY VND (1,0%)
     * tren 10 TY VND den 20 TY VND (0,5%)
     * tren 20 TY VND (0%)
     * @param $purchase
     * @return float
     */
    public static function getPurchaseFeeRate($purchase) {
        $result = 2;
        $arrParam = Yii::$app->params;
        if (isset($arrParam[self::K_PURCHASE_FEE][self::K_RATE])) {
            $arrFee = $arrParam[self::K_PURCHASE_FEE][self::K_RATE];
            foreach ($arrFee as $fee) {
                $min = $fee[self::K_MIN];
                $max = $fee[self::K_MAX];
                if ($purchase > $min && ($max === false || $purchase <= $max)) {
                    return $fee[self::K_VAL];
                }
            }
        }
        return $result;
    }

    /**
     * get Purchase Fee Rule
     * @return float
     */
    public static function getPurchaseFeeRule() {
        return self::getParam2Keys(self::K_PURCHASE_FEE, self::K_RULE, NumberUtils::NUM_ROUND);
    }

    /**
     * get Sale Fee Rate
     * 1 THANG hoac ngan hon (3,0%)
     * TREN 1 THANG den 12 THANG (1,0%)
     * TREN 12 THANG den 24 THANG (0,5%)
     * TREN 24 THANG (0%)
     * @param $month
     * @return float
     */
    public static function getSaleFeeRate($month) {
        $result = 3;
        $arrParam = Yii::$app->params;
        if (isset($arrParam[self::K_SALE_FEE][self::K_RATE])) {
            $arrFee = $arrParam[self::K_SALE_FEE][self::K_RATE];
            foreach ($arrFee as $fee) {
                $min = $fee[self::K_MIN];
                $max = $fee[self::K_MAX];
                if ($month > $min && ($max === false || $month <= $max)) {
                    return $fee[self::K_VAL];
                }
            }
        }
        return $result;
    }

    /**
     * get Sale Fee Rule
     * @return float
     */
    public static function getSaleFeeRule() {
        return self::getParam2Keys(self::K_SALE_FEE, self::K_RULE, NumberUtils::NUM_ROUND);
    }

    /**
     * get Income Tax Rate Sale
     * @return float
     */
    public static function getIncomeTaxRateSale() {
        return self::getParam2Keys(self::K_SALE_FEE, self::K_INCOME_TAX_RATE, 0.1);
    }

    /**
     * get Purchase Fee Discount Rate
     * @param $purchaseType
     * @return float
     */
    public static function getPurchaseFeeDiscountRate($purchaseType) {
        $result = 0;
        $arrParam = Yii::$app->params;
        if (isset($arrParam[self::K_PURCHASE_TYPE][$purchaseType])) {
            $type = $arrParam[self::K_PURCHASE_TYPE][$purchaseType];
            $result = isset($type[self::K_PURCHASE_FEE_DISCOUNT_RATE]) ? $type[self::K_PURCHASE_FEE_DISCOUNT_RATE] : 0;
        }
        return $result;
    }

    /**
     * get Sale Fee Discount Rate
     * @param $purchaseType
     * @return float
     */
    public static function getSaleFeeDiscountRate($purchaseType) {
        $result = 0;
        $arrParam = Yii::$app->params;
        if (isset($arrParam[self::K_PURCHASE_TYPE][$purchaseType])) {
            $type = $arrParam[self::K_PURCHASE_TYPE][$purchaseType];
            $result = isset($type[self::K_SALE_FEE_DISCOUNT_RATE]) ? $type[self::K_SALE_FEE_DISCOUNT_RATE] : 0;
        }
        return $result;
    }

    /**
     * get Exit Sip Free Rate
     * dung SIP trong vong 12 THANG (0,4%)
     * dung SIP hon 12 THANG (0%)
     * @param $month
     * @return float
     */
    public static function getExitSipFreeRate($month) {
        $result = 0.4;
        $arrParam = Yii::$app->params;
        if (isset($arrParam[self::K_SIP_PROGRAM][self::K_EXIT_FEE_RATE] )) {
            $arrFee = $arrParam[self::K_SIP_PROGRAM][self::K_EXIT_FEE_RATE];
            foreach ($arrFee as $fee) {
                $min = $fee[self::K_MIN];
                $max = $fee[self::K_MAX];
                if ($month > $min && ($max === false || $month <= $max)) {
                    return $fee[self::K_VAL];
                }
            }
        }
        return $result;
    }

    /**
     * get Found Stock Rule
     * @return float
     */
    public static function getFoundStockRule() {
        return self::getParamOneKey(self::K_FOUND_STOCK_RULE, NumberUtils::NUM_FLOOR);
    }

    /**
     * get param value from 1 key
     * @param $key
     * @param $default
     * @return mixed
     */
    private static function getParamOneKey($key, $default) {
        $arrParam = Yii::$app->params;
        return isset($arrParam[$key]) ? $arrParam[$key] : $default;
    }

    /**
     * get param value from 2 keys
     * @param $key1
     * @param $key2
     * @param $default
     * @return mixed
     */
    private static function getParam2Keys($key1, $key2, $default) {
        $arrParam = Yii::$app->params;
        return isset($arrParam[$key1][$key2]) ? $arrParam[$key1][$key2] : $default;
    }
}