<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('jar.distribute', 'Create Distribute');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('jar.distribute', 'Edit Distribute');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('jar.distribute', 'Copy Distribute');
    }
    $rowindex = 0;
    $sumUnit = 0;
    $sumShared = 0;
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div class="box-footer"><ul class="nav nav-stacked nav-no-padding">
        <?php if (!is_null($model->share_id)): ?><li><a href="javascript:void(0);">
            <?= $model->getAttributeLabel('share_id'); ?>
            <span class="pull-right"><?= $model->share_id; ?></span>
        </a></li><?php endif; ?>
        <li><a href="javascript:void(0);">
            <?= $model->getAttributeLabel('share_date'); ?>
            <?= DateTimeUtils::htmlDateFormat($model->share_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, ['class'=>'pull-right']); ?>
        </a></li>
        <li><a href="javascript:void(0);">
            <?= $model->getAttributeLabel('share_value'); ?>
            <span class="pull-right badge bg-red"><?= NumberUtils::format($model->share_value); ?></span>
        </a></li>
        <li><a href="javascript:void(0);">
            <?= $model->getAttributeLabel('description'); ?>
            <span class="pull-right"><?= $model->description; ?></span>
        </a></li>
    </ul></div>
    <div style="display: none">
        <?= $form->field($model, 'share_date')->hiddenInput(); ?>
        <?= $form->field($model, 'share_value')->hiddenInput(); ?>
        <?= $form->field($model, 'description')->hiddenInput(); ?>
    </div>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3></div>
    <div class="box-footer">
        <div class="row"><div id="w0"><div id="w1" class="grid-view col-xs-12"><div class="table-responsive"><table class="table table-bordered">
            <thead><tr class="warning">
                <th style="text-align: center"><?= Yii::t('fin.grid', 'No.'); ?></th>
                <th style="text-align: center"><?= Yii::t('fin.grid', 'Account Name'); ?></th>
                <th style="text-align: center"><?= Yii::t('fin.grid', 'Unit'); ?></th>
                <th style="text-align: center"><?= Yii::t('fin.grid', 'Shared'); ?></th>
            </tr></thead>
            <tbody>
                <?php foreach($arrShareDetail as $item): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $sumUnit += $item->share_unit;
                        $sumShared += $item->share_value;
                        $rowindex++;
                    ?>
                    <tr class="<?= $class; ?>">
                        <td style="vertical-align: middle; text-align: center"><?= $rowindex; ?></td>
                        <td style="vertical-align: middle; text-align: left"><?= $item->account_name; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $item->share_unit; ?> %</td>
                        <td style="vertical-align: middle; text-align: right"><?= NumberUtils::format($item->share_value); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="warning">
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Total'); ?></th>
                    <th style="text-align: right"><?= $sumUnit; ?> %</th>
                    <th style="text-align: right"><?= NumberUtils::format($sumShared); ?></th>
                </tr>
            </tbody>
        </table></div></div></div></div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?></div></div></div>