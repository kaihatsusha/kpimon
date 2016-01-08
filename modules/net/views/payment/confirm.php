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

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div id="netPaymentConfirmForm" class="box-body"><?php $form = ActiveForm::begin(); ?>
        <div class="row"><div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('customer_id'); ?></th>
                    <td class="info"><?= isset($arrNetCustomer[$model->customer_id]) ? $arrNetCustomer[$model->customer_id] : ''; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('entry_date'); ?></th>
                    <td class="info"><?= DateTimeUtils::htmlDateFormat($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('credit'); ?></th>
                    <td class="info"><?= NumberUtils::format($model->credit); ?></td>
                </tr>
            </table>
            <div style="display: none">
                <?= $form->field($model, 'customer_id')->hiddenInput(); ?>
                <?= $form->field($model, 'entry_date')->hiddenInput(); ?>
                <?= $form->field($model, 'credit')->hiddenInput(); ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
                <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div>