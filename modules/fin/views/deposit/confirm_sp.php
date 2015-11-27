<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('fin.deposit', 'Create Fixed Deposit');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('fin.deposit', 'Edit Fixed Deposit');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('fin.deposit', 'Copy Fixed Deposit');
    }

    $openingDate = DateTimeUtils::getDateFromDB($model->opening_date);
    $closingDate = DateTimeUtils::getDateFromDB($model->closing_date);
    $dateDiff = $closingDate->diff($openingDate);
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div class="box-footer" id="finPaymentConfirmForm"><?php $form = ActiveForm::begin(); ?>
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('saving_account'); ?>
                <span class="pull-right"><?= isset($arrSavingAccount[$model->saving_account]) ? $arrSavingAccount[$model->saving_account] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_rate'); ?>
                <span class="pull-right badge bg-green"><?= NumberUtils::format($model->interest_rate, 4); ?> %</span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('opening_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->opening_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('closing_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->closing_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_days'); ?>
                <span class="pull-right"><?= $dateDiff->days; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_add'); ?>
                <span class="pull-right badge bg-aqua"><?= NumberUtils::format($model->interest_add); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_value'); ?>
                <?php if ($model->add_flag == MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING): ?>
                    <span class="pull-right badge bg-aqua"><?= NumberUtils::format($model->entry_value); ?></span>
                <?php else: ?>
                    <span class="pull-right badge bg-red"><?= NumberUtils::format($model->entry_value); ?></span>
                <?php endif; ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('add_flag'); ?>
                <span class="pull-right"><?= isset($arrTimedepositTrantype[$model->add_flag]) ? $arrTimedepositTrantype[$model->add_flag] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('current_assets'); ?>
                <span class="pull-right"><?= isset($arrCurrentAssets[$model->current_assets]) ? $arrCurrentAssets[$model->current_assets] : ''; ?></span>
            </a></li>
        </ul>
        <div style="display: none">
            <?= $form->field($model, 'saving_account')->hiddenInput(); ?>
            <?= $form->field($model, 'interest_rate')->hiddenInput(); ?>
            <?= $form->field($model, 'opening_date')->hiddenInput(); ?>
            <?= $form->field($model, 'closing_date')->hiddenInput(); ?>
            <?= $form->field($model, 'interest_add')->hiddenInput(); ?>
            <?= $form->field($model, 'entry_value')->hiddenInput(); ?>
            <?= $form->field($model, 'add_flag')->hiddenInput(); ?>
            <?= $form->field($model, 'current_assets')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    <?php ActiveForm::end(); ?></div>
</div></div></div>
