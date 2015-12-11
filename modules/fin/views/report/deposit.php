<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\modules\fin\views\ReportAsset;
    use kartik\datetime\DateTimePicker;

    // css & js
    ReportAsset::$CONTEXT = ['js'=>['js/fin/reportDeposit.js'], 'depends'=>['app\assets\ChartJsAsset']];
    ReportAsset::register($this);

    $this->title = Yii::t('fin.report', 'Report Interest');
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
                <canvas id="depositBarChart" style="height:500px"></canvas>
            </div></div>
            <div class="row"><div id="w1"><div class="grid-view col-xs-12 table-responsive" id="w2"><table class="table table-bordered">
                <thead><tr class="warning">
                    <th style="text-align: center"><?= Yii::t('fin.grid', 'Month'); ?></th>
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Term'); ?></th>
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Noterm'); ?></th>
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Total'); ?></th>
                </tr></thead>
                <tfoot>
                    <?php
                        $sumNoterm = is_null($sumGridData['sum_noterm']) ? 0 : $sumGridData['sum_noterm'];
                        $avgNoterm = is_null($sumGridData['avg_noterm']) ? 0 : $sumGridData['avg_noterm'];
                        $sumTerm = is_null($sumGridData['sum_term']) ? 0 : $sumGridData['sum_term'];
                        $avgTerm = is_null($sumGridData['avg_term']) ? 0 : $sumGridData['avg_term'];
                        $sumTotal = $sumNoterm + $sumTerm;
                        $avgTotal = $avgNoterm + $avgTerm;
                    ?>
                    <tr class="warning">
                        <th style="text-align: right"><?= Yii::t('fin.grid', 'Total'); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($sumTerm); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($sumNoterm); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($sumTotal); ?></th>
                    </tr>
                    <tr class="warning">
                        <th style="text-align: right"><?= Yii::t('fin.grid', 'Average'); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($avgTerm); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($avgNoterm); ?></th>
                        <th style="text-align: right" colspan="2"><?= NumberUtils::format($avgTotal); ?></th>
                    </tr>
                </tfoot>
                <tbody><?php foreach($gridData as $girdRow): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $monthStr = $girdRow['month']->format($fmKeyPhp);
                        $compareNotermHtml = null;
                        $compareTermHtml = null;
                        $compareTotalHtml = null;
                        if ($rowindex == 0) {
                            $compareNotermHtml = '';
                            $compareTermHtml = '';
                            $compareTotalHtml = '';
                        } else {
                            $compareConfig = ['template'=>'<i class="fa fa-fw {icon}"></i><span class="{color}">{number}</span>',
                                'incColor'=>'text-blue', 'decColor'=>'text-red', 'incIcon'=>'fa-thumbs-o-up', 'decIcon'=>'fa-thumbs-o-down'];
                            $compareNotermHtml = NumberUtils::getIncDecNumber($girdRow['compareNoterm'], $compareConfig);
                            $compareTermHtml = NumberUtils::getIncDecNumber($girdRow['compareTerm'], $compareConfig);
                            $compareTotalHtml = NumberUtils::getIncDecNumber($girdRow['compareTotal'], $compareConfig);
                        }

                        $rowindex++;
                    ?>
                    <tr class="<?= $class; ?>">
                        <td style="text-align: center"><?= $monthStr; ?></td>
                        <td style="text-align: right"><?= $compareTermHtml; ?></td>
                        <td style="text-align: right"><?= NumberUtils::format($girdRow['term']); ?></td>
                        <td style="text-align: right"><?= $compareNotermHtml; ?></td>
                        <td style="text-align: right"><?= NumberUtils::format($girdRow['noterm']); ?></td>
                        <td style="text-align: right"><?= $compareTotalHtml; ?></td>
                        <td style="text-align: right"><?= NumberUtils::format($girdRow['total']); ?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table></div></div></div>
        <?php endif; ?>
    </div>
<?php ActiveForm::end(); ?></div></div></div>
