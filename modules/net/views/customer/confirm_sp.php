<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('net.customer', 'Create Customer');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('net.customer', 'Edit Customer');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('net.customer', 'Copy Customer');
    }
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div class="box-footer" id="netCustomerConfirmForm"><?php $form = ActiveForm::begin(); ?>
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('name'); ?>
                <span class="pull-right"><?= $model->name; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('status'); ?>
                <span class="pull-right"><?= isset($arrCustomerStatus[$model->status]) ? $arrCustomerStatus[$model->status] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('description'); ?>
                <span class="pull-right"><?= $model->description; ?></span>
            </a></li>
        </ul>
        <div style="display: none">
            <?= $form->field($model, 'name')->hiddenInput(); ?>
            <?= $form->field($model, 'status')->hiddenInput(); ?>
            <?= $form->field($model, 'description')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    <?php ActiveForm::end(); ?></div>
</div></div></div>