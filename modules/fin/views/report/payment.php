<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\modules\fin\views\ReportAsset;
    use kartik\datetime\DateTimePicker;

    // css & js
    ReportAsset::$CONTEXT = ['js'=>['js/fin/reportPayment.js'], 'depends'=>['app\assets\ChartJsAsset']];
    ReportAsset::register($this);

    $this->title = Yii::t('fin.report', 'Report Payments');
    $rowindex = 0;
?>

<div class="row"><div class="col-md-12"><div class="box"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('fin.form', 'Monthly'); ?></h3>
        <div class="box-tools"><div style="width: 150px;" class="input-group input-group-sm">
            <?= DateTimePicker::widget(['model'=>$model, 'attribute'=>'fmonth', 'type'=>1, 'readonly'=>true,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmKeyJui, 'startView'=>3, 'minView'=>3, 'startDate'=>$startDateJui],
                'options'=>['class'=>'pull-right']]); ?>
            <div class="input-group-btn">
                <button class='btn btn-default' type='submit' name='<?= MasterValueUtils::SM_MODE_NAME;?>' value='<?= MasterValueUtils::SM_MODE_INPUT;?>'><i class="fa fa-edit"></i></button>
            </div>
        </div></div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'fmonth_from')->widget(DateTimePicker::className(), ['type'=>1, 'readonly'=>true,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmKeyJui, 'startView'=>3, 'minView'=>3]
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'fmonth_to')->widget(DateTimePicker::className(), ['type'=>1, 'readonly'=>true,
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmKeyJui, 'startView'=>3, 'minView'=>3]
                ]); ?>
            </div>
            <div class="col-md-12"><div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_LIST]); ?>
            </div></div>
        </div>
        <?php if (!is_null($gridData)): ?>
            <script type="text/javascript">
                CHART_DATA = <?= $chartData; ?>;
            </script>
            <div class="row"><div class="chart">
                <canvas id="paymentLineChart" style="height:500px"></canvas>
            </div></div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6"><table class="table table-bordered"><thead><tr>
                    <th class="warning" style="text-align: left; width: 120px"><?= Yii::t('fin.grid', 'This Month'); ?></th>
                    <th class="info" style="text-align: right;"><?= NumberUtils::format($sumCurrentMonthData['credit']); ?></th>
                    <th class="danger" style="text-align: right;"><?= NumberUtils::format($sumCurrentMonthData['debit']); ?></th>
                    <th class="success" style="text-align: right;"><?= NumberUtils::format($sumCurrentMonthData['credit'] - $sumCurrentMonthData['debit']); ?></th>
                </tr></thead></table></div>
            </div>
            <div class="row"><div id="w1"><div class="grid-view col-xs-12 table-responsive" id="w2"><table class="table table-bordered">
                <thead><tr class="warning">
                    <th style="text-align: center"><?= Yii::t('fin.grid', 'Month'); ?></th>
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Credit'); ?></th>
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Debit'); ?></th>
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Balance'); ?></th>
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
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($sumCredit); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($sumDebit); ?></th>
                        <th style="text-align: right" colspan="2"><?= $sumBalanceHtml; ?></th>
                    </tr>
                    <tr class="warning">
                        <th style="text-align: right"><?= Yii::t('fin.grid', 'Average'); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($avgCredit); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($avgDebit); ?></th>
                        <th style="text-align: right" colspan="2"><?= $avgBalanceHtml; ?></th>
                    </tr>
                </tfoot>
                <tbody><?php foreach($gridData as $girdRow): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $monthStr = $girdRow['month']->format($fmKeyPhp);
                        $compareCreditHtml = null;
                        $compareDebitHtml = null;
                        $compareBalanceHtml = null;
                        if ($rowindex == 0) {
                            $compareCreditHtml = '';
                            $compareDebitHtml = '';
                            $compareBalanceHtml = '';
                        } else {
                            $compareCreditConfig = ['template'=>'<i class="fa fa-fw {icon}"></i><span class="{color}">{number}</span>',
                                'incColor'=>'text-blue', 'decColor'=>'text-red', 'incIcon'=>'fa-thumbs-o-up', 'decIcon'=>'fa-thumbs-o-down'];
                            $compareCreditHtml = NumberUtils::getIncDecNumber($girdRow['compareCredit'], $compareCreditConfig);
                            $compareBalanceHtml = NumberUtils::getIncDecNumber($girdRow['compareBalance'], $compareCreditConfig);

                            $compareDebitConfig = ['template'=>'<i class="fa fa-fw {icon}"></i><span class="{color}">{number}</span>',
                                'incColor'=>'text-red', 'decColor'=>'text-blue', 'incIcon'=>'fa-thumbs-o-down', 'decIcon'=>'fa-thumbs-o-up'];
                            $compareDebitHtml = NumberUtils::getIncDecNumber($girdRow['compareDebit'], $compareDebitConfig);
                        }
                        $balanceHtml = NumberUtils::getIncDecNumber($girdRow['balance'], ['template'=>'<span class="{color}">{number}</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red']);

                        $rowindex++;
                    ?>
                    <tr class="<?= $class; ?>">
                        <td style="text-align: center"><?= $monthStr; ?></td>
                        <td style="text-align: right"><?= $compareCreditHtml; ?></td>
                        <td style="text-align: right"><?= NumberUtils::format($girdRow['credit']); ?></td>
                        <td style="text-align: right"><?= $compareDebitHtml; ?></td>
                        <td style="text-align: right"><?= NumberUtils::format($girdRow['debit']); ?></td>
                        <td style="text-align: right"><?= $compareBalanceHtml; ?></td>
                        <td style="text-align: right"><?= $balanceHtml; ?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table></div></div></div>
        <?php endif; ?>
    </div>
<?php ActiveForm::end(); ?></div></div></div>