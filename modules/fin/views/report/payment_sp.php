<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\modules\fin\views\ReportAsset;
    use kartik\datetime\DateTimePicker;

    // css & js
    ReportAsset::$CONTEXT = ['js'=>['js/fin/reportPaymentSp.js'], 'depends'=>['app\assets\ChartSparklineAsset']];
    ReportAsset::register($this);

    $this->title = Yii::t('fin.report', 'Report Payments');
    $rowindex = 0;
?>

<div class="row"><div class="col-md-12"><div class="box box-maroon collapsed-box">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('fin.form', 'Monthly'); ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body" style="padding-bottom: 0;"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <div class="input-group">
                <?= DateTimePicker::widget(['model'=>$model, 'attribute'=>'fmonth', 'type'=>1, 'readonly'=>true,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmKeyJui, 'startView'=>3, 'minView'=>3],
                    'options'=>['class'=>'pull-right']]); ?>
                <div class="input-group-btn">
                    <button class='btn btn-default' type='submit' name='<?= MasterValueUtils::SM_MODE_NAME;?>' value='<?= MasterValueUtils::SM_MODE_INPUT;?>'><i class="fa fa-edit"></i></button>
                </div>
            </div>
            <div class="progress progress-xxs" style="margin-top: 8px;">
                <div style="width: 100%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="100" role="progressbar" class="progress-bar progress-bar-warning progress-bar-striped"></div>
            </div>
            <?= $form->field($model, 'fmonth_from')->widget(DateTimePicker::className(), ['type'=>1, 'readonly'=>true,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmKeyJui, 'startView'=>3, 'minView'=>3]
            ]); ?>
            <?= $form->field($model, 'fmonth_to')->widget(DateTimePicker::className(), ['type'=>1, 'readonly'=>true,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmKeyJui, 'startView'=>3, 'minView'=>3]
            ]); ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_LIST]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
    <?php if (!is_null($gridData)): ?>
        <div class="box-body-notool">
            <script type="text/javascript">
                CHART_DATA = <?= $chartData; ?>;
            </script>
            <div class="row"><div id="paymentCompositeInlineChart" style="margin-left: 8px; padding-bottom: 10px;"></div></div>
            <div class="row">
                <div id="w1"><div id="w2" class="grid-view col-xs-12"><table class="table table-bordered">
                    <thead><tr class="warning">
                        <th style="text-align: center"><?= Yii::t('fin.grid', 'Month'); ?></th>
                        <th style="text-align: center"><?= Yii::t('fin.grid', 'Credit'); ?></th>
                        <th style="text-align: center"><?= Yii::t('fin.grid', 'Debit'); ?></th>
                        <th style="text-align: center"><?= Yii::t('fin.grid', 'Balance'); ?></th>
                    </tr></thead>
                    <tfoot>
                        <?php
                        $sumCredit = is_null($sumGridData['sum_credit']) ? 0 : $sumGridData['sum_credit'];
                        $avgCredit = is_null($sumGridData['avg_credit']) ? 0 : $sumGridData['avg_credit'];
                        $sumDebit = is_null($sumGridData['sum_debit']) ? 0 : $sumGridData['sum_debit'];
                        $avgDebit = is_null($sumGridData['avg_debit']) ? 0 : $sumGridData['avg_debit'];
                        $sumBalance = $sumCredit - $sumDebit;
                        $avgBalance = $avgCredit - $avgDebit;
                        $sumBalanceConfig = ['template'=>'<span class="{color}">{number}</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red'];
                        $sumBalanceHtml = NumberUtils::getIncDecNumber($sumBalance, $sumBalanceConfig);
                        $avgBalanceHtml = NumberUtils::getIncDecNumber($avgBalance, $sumBalanceConfig);
                        ?>
                        <tr class="warning">
                            <th style="text-align: right"><?= Yii::t('fin.grid', 'Total'); ?></th>
                            <th style="text-align: right"><?= NumberUtils::format($sumCredit); ?></th>
                            <th style="text-align: right"><?= NumberUtils::format($sumDebit); ?></th>
                            <th style="text-align: right"><?= $sumBalanceHtml; ?></th>
                        </tr>
                        <tr class="warning">
                            <th style="text-align: right"><?= Yii::t('fin.grid', 'Average'); ?></th>
                            <th style="text-align: right"><?= NumberUtils::format($avgCredit); ?></th>
                            <th style="text-align: right"><?= NumberUtils::format($avgDebit); ?></th>
                            <th style="text-align: right"><?= $avgBalanceHtml; ?></th>
                        </tr>
                    </tfoot>
                    <tbody><?php foreach($gridData as $girdRow): ?>
                        <?php
                            $class = MasterValueUtils::getColorRow($rowindex);
                            $monthStr = $girdRow['month']->format($fmKeyPhp);
                            $arrCreditHtml = [];
                            $arrDebitHtml = [];
                            $arrBalanceHtml = [];
                            if ($rowindex > 0) {
                                $compareCreditConfig = ['template'=>'<span class="{color}">{number}</span>',
                                    'incColor'=>'text-blue', 'decColor'=>'text-red'];
                                $arrCreditHtml[] = NumberUtils::getIncDecNumber($girdRow['compareCredit'], $compareCreditConfig);
                                $arrBalanceHtml[] = NumberUtils::getIncDecNumber($girdRow['compareBalance'], $compareCreditConfig);

                                $compareDebitConfig = ['template'=>'<span class="{color}">{number}</span>',
                                    'incColor'=>'text-red', 'decColor'=>'text-blue'];
                                $arrDebitHtml[] = NumberUtils::getIncDecNumber($girdRow['compareDebit'], $compareDebitConfig);
                            }

                            $arrCreditHtml[] = NumberUtils::format($girdRow['credit']);
                            $arrDebitHtml[] = NumberUtils::format($girdRow['debit']);
                            $arrBalanceHtml[] = NumberUtils::getIncDecNumber($girdRow['balance'], ['template'=>'<span class="{color}">{number}</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red']);
                            $creditHtml = implode('<br/>', $arrCreditHtml);
                            $debitHtml = implode('<br/>', $arrDebitHtml);
                            $balanceHtml = implode('<br/>', $arrBalanceHtml);

                            $rowindex++;
                        ?>
                        <tr class="<?= $class; ?>">
                            <td style="vertical-align: middle; text-align: center"><?= $monthStr; ?></td>
                            <td style="vertical-align: middle; text-align: right"><?= $creditHtml; ?></td>
                            <td style="vertical-align: middle; text-align: right"><?= $debitHtml; ?></td>
                            <td style="vertical-align: middle; text-align: right"><?= $balanceHtml; ?></td>
                        </tr>
                    <?php endforeach; ?></tbody>
                </table></div></div>
            </div>
        </div>
    <?php endif; ?>
</div></div></div>