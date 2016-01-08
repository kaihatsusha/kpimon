<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('net.customer', 'Create Payment');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('net.customer', 'Edit Payment');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('net.customer', 'Copy Payment');
    }
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div class="box-footer" id="netPaymentConfirmForm"><?php $form = ActiveForm::begin(); ?>
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('customer_id'); ?>
                <span class="pull-right"><?= isset($arrNetCustomer[$model->customer_id]) ? $arrNetCustomer[$model->customer_id] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('credit'); ?>
                <span class="pull-right badge bg-aqua"><?= NumberUtils::format($model->credit); ?></span>
            </a></li>
        </ul>
        <div style="display: none">
            <?= $form->field($model, 'customer_id')->hiddenInput(); ?>
            <?= $form->field($model, 'entry_date')->hiddenInput(); ?>
            <?= $form->field($model, 'credit')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    <?php ActiveForm::end(); ?></div>
</div></div></div>