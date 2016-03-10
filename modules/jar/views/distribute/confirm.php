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
?>

<div class="box box-default"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div id="jarDistributeConfirmForm" class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <?php if (!is_null($model->share_id)): ?><tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('share_id'); ?></th>
                <td class="info"><?= $model->share_id; ?></td>
            </tr><?php endif; ?>
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('share_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->share_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('share_value'); ?></th>
                <td class="info"><?= NumberUtils::format($model->share_value); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('description'); ?></th>
                <td class="info"><?= $model->description; ?></td>
            </tr>
        </table>
        <div style="display: none">
            <?= $form->field($model, 'share_date')->hiddenInput(); ?>
            <?= $form->field($model, 'share_value')->hiddenInput(); ?>
            <?= $form->field($model, 'description')->hiddenInput(); ?>
        </div>
    </div></div></div>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3></div>
    <div class="box-body">
        <div class="row"><div id="w1"><div class="grid-view col-xs-12 table-responsive" id="w2">
            <?php
                $rowindex = 0;
                $sumUnit = 0;
                $sumShared = 0;
            ?>
            <table class="table table-bordered">
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
            </table>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
                <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
            </div>
        </div></div></div>
    </div>
<?php ActiveForm::end(); ?></div>