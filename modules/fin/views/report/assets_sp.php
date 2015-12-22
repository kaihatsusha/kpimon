<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\modules\fin\views\ReportAsset;
    use kartik\datetime\DateTimePicker;

    $this->title = Yii::t('fin.report', 'Report Assets');
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
                    'pluginOptions'=>['autoclose'=>true, 'format'=>$fmKeyJui, 'startView'=>3, 'minView'=>3, 'startDate'=>$startDateJui],
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
    <?php if (!is_null($gridData)): ?><div class="box-body-notool">
        <div class="row">
            <div id="w1"><div id="w2" class="grid-view col-xs-12"><table class="table table-bordered">
                <thead><tr class="warning">
                    <th style="text-align: center"><?= Yii::t('fin.grid', 'Month'); ?></th>
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Assets'); ?></th>
                </tr></thead>
                <tbody><?php foreach($gridData as $girdRow): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $monthStr = $girdRow['month']->format($fmKeyPhp);
                        $compareAssetsHtml = null;
                        if ($rowindex == 0) {
                            $compareAssetsHtml = '';
                        } else {
                            $compareCreditConfig = ['template'=>'<span class="{color}">{number}</span>',
                                'incColor'=>'text-blue', 'decColor'=>'text-red', 'incIcon'=>'fa-thumbs-o-up', 'decIcon'=>'fa-thumbs-o-down'];
                            $compareAssetsHtml = NumberUtils::getIncDecNumber($girdRow['compareAssets'], $compareCreditConfig);
                        }

                        $rowindex++;
                    ?>
                    <tr class="<?= $class; ?>">
                        <td style="text-align: center"><?= $monthStr; ?></td>
                        <td style="text-align: right"><?= NumberUtils::format($girdRow['assets']); ?></td>
                        <td style="text-align: right"><?= $compareAssetsHtml; ?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table></div></div>
        </div>
    </div><?php endif; ?>
</div>
