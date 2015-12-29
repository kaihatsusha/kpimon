<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;

    $this->title = Yii::t('net.customer', 'Create Customer');
?>

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div id="netCustomerCreateForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($model, 'name')->textInput(); ?>
            <?= $form->field($model, 'status')->inline(true)->radioList($arrCustomerStatus); ?>
            <?= $form->field($model, 'description')->textInput(); ?>
            <div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
                <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div>