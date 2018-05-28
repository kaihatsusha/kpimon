<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;
    use kartik\datetime\DateTimePicker;

    $this->title = Yii::t('oef.purchase', 'Sell Fund Certificate');
?>

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('oef.form', 'Input Values'); ?></h3></div>
    <div id="oefSaleToolForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'sell_date')->widget(DateTimePicker::className(), ['type'=>1,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
                ]); ?>
                <?= $form->field($model, 'sell_certificate')->textInput(['type'=>'number', 'step'=>'any']); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'nav')->textInput(['type'=>'number', 'step'=>'any']); ?>
                <?= $form->field($model, 'income_tax_rate')->textInput(['type'=>'number', 'step'=>'any']); ?>
            </div>
            <div class="col-md-12"><div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
                <?= Html::submitButton(Yii::t('button', 'Calculate'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div></div>
        </div>
        <?php if($arrFundCertificate4Sell): ?>
            <?php
                $sumInvestment = NumberUtils::format($model->investment, 2);
                $sumSellableCertificate = NumberUtils::format($model->sellable_certificate, 2);
                $sumSellCertificate = NumberUtils::format($model->sum_sell_certificate, 2);
                $sumRevenue = NumberUtils::format($model->revenue, 2);
                $sumSellFee = NumberUtils::format($model->sell_fee, 2);
                $sumProfitBeforeTaxes = NumberUtils::format($model->profit_before_taxes, 2);
                $sumProfitAfterTaxes = NumberUtils::format($model->profit_after_taxes, 2);
                $sumIncomeTax = NumberUtils::format($model->income_tax, 2);
                $otherFee = 50600;
                $sumInvestmentResult = NumberUtils::format($model->investment_result - $otherFee, 2);
                $sumOtherFee = NumberUtils::format($otherFee, 2);
            ?>
            <div class="row"><div id="w3"><div class="grid-view col-xs-9 table-responsive" id="w4"><table class="table table-bordered">
                <tr>
                    <th class="danger" style="width: 160px"><?= Yii::t('oef.grid', 'Investment'); ?> <sup>(1)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumInvestment; ?></td>
                    <th class="danger" style="width: 160px"><?= Yii::t('oef.grid', 'Income Taxes'); ?> <sup>(5)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumIncomeTax; ?></td>
                </tr>
                <tr>
                    <th class="danger"><?= Yii::t('oef.grid', 'Revenue'); ?> <sup>(2)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumRevenue; ?></td>
                    <th class="danger"><?= Yii::t('oef.grid', 'Profit After Taxes'); ?> <sup>(6) = (4) - (5)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumProfitAfterTaxes; ?></td>
                </tr>
                <tr>
                    <th class="danger"><?= Yii::t('oef.grid', 'Sell Fee'); ?> <sup>(3)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumSellFee; ?></td>
                    <th class="danger"><?= Yii::t('oef.grid', 'Other Fee (Transfer money ...)'); ?> <sup>(7)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumOtherFee; ?></td>
                </tr>
                <tr>
                    <th class="danger"><?= Yii::t('oef.grid', 'Profit Before Taxes'); ?> <sup>(4) = (2) - (3)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumProfitBeforeTaxes; ?></td>
                    <th class="danger"><?= Yii::t('oef.grid', 'Investment Result'); ?> <sup>(8) = (6) - (1) - (7)</sup></th>
                    <td class="info" style="text-align: right"><?= $sumInvestmentResult; ?></td>
                </tr>
            </table></div></div></div>
            <div class="row"><div id="w1"><div class="grid-view col-xs-12 table-responsive" id="w2"><table class="table table-bordered">
                <thead><tr class="warning">
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'No.'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Ref'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Purchase Date'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Investment'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Sellable'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Sell'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Revenue'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Months'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Fee'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'PBT'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Income Taxes'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'PAT'); ?></th>
                    <th style="text-align: center"><?= Yii::t('oef.grid', 'Result'); ?></th>
                </tr></thead>
                <tfoot>
                    <tr class="warning">
                        <th style="text-align: right" colspan="3"><?= Yii::t('oef.grid', 'Total'); ?></th>
                        <th style="text-align: right"><?= $sumInvestment; ?></th>
                        <th style="text-align: right"><?= $sumSellableCertificate; ?></th>
                        <th style="text-align: right"><?= $sumSellCertificate; ?></th>
                        <th style="text-align: right"><?= $sumRevenue; ?></th>
                        <th style="text-align: right"></th>
                        <th style="text-align: right"><?= $sumSellFee; ?></th>
                        <th style="text-align: right"><?= $sumProfitBeforeTaxes; ?></th>
                        <th style="text-align: right"><?= $sumIncomeTax; ?></th>
                        <th style="text-align: right"><?= $sumProfitAfterTaxes; ?></th>
                        <th style="text-align: right"><?= $sumInvestmentResult; ?></th>
                    </tr>
                </tfoot>
                <?php
                    $rowindex = 0;
                ?>
                <tbody><?php foreach($arrFundCertificate4Sell as $fundCertificate4Sell): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $purchaseId = str_pad($fundCertificate4Sell->purchase_id, 6, '0', STR_PAD_LEFT);
                        $purchaseDate = $fundCertificate4Sell->purchase_date->format(DateTimeUtils::FM_VIEW_DATE);
                        $purchaseType = StringUtils::format('{0}{1}{2}</br>{3}', [$arrPurchaseType[$fundCertificate4Sell->purchase_type],
                            is_null($fundCertificate4Sell->sip_date) ? '' : ' ' . $fundCertificate4Sell->sip_date->format(DateTimeUtils::FM_VIEW_DATE),
                            is_null($fundCertificate4Sell->sip_months) ? '' : ' (' . NumberUtils::format($fundCertificate4Sell->sip_months, 1) . ' M)',
                            NumberUtils::format($fundCertificate4Sell->investment, 2)]);
                        $sellableCertificate = NumberUtils::format($fundCertificate4Sell->sellable_certificate, 2);
                        $sellCertificate = NumberUtils::format($fundCertificate4Sell->sell_certificate, 2);
                        $revenue = NumberUtils::format($fundCertificate4Sell->revenue, 2);
                        $keptMonths = NumberUtils::format($fundCertificate4Sell->kept_months, 1);
                        $sellFee = StringUtils::format('{0}%<br/>{1}', [NumberUtils::format($fundCertificate4Sell->sell_fee_rate, 2), NumberUtils::format($fundCertificate4Sell->sell_fee, 2)]);
                        $profitBeforeTaxes = NumberUtils::format($fundCertificate4Sell->profit_before_taxes, 2);
                        $profitAfterTaxes = NumberUtils::format($fundCertificate4Sell->profit_after_taxes, 2);
                        $incomeTax = NumberUtils::format($fundCertificate4Sell->income_tax, 2);
                        $investmentResult = NumberUtils::format($fundCertificate4Sell->investment_result, 2);
                        $rowindex++;
                    ?>
                    <tr class="<?= $class; ?>">
                        <td style="vertical-align: middle; text-align: center"><?= $rowindex; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?= $purchaseId; ?></td>
                        <td style="vertical-align: middle; text-align: center"><?= $purchaseDate; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $purchaseType; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $sellableCertificate; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $sellCertificate; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $revenue; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $keptMonths; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $sellFee; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $profitBeforeTaxes; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $incomeTax; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $profitAfterTaxes; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $investmentResult; ?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table></div></div></div>
        <?php endif ?>
    <?php ActiveForm::end(); ?></div>
</div>
