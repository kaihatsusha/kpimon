<?php
namespace app\models\extended;

use app\models\OefFundCertificate;

class OefFundCertificateIpo extends OefFundCertificate {
    /**
     * @param OefPurchase $purchase
     * @param OefFundCertificate $condition
     */
    public function initialize($purchase, $condition) {
        parent::initialize($purchase, $condition);
    }
}