<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use yii\jui\DatePicker;
    use app\components\MasterValueUtils;

    $this->title = Yii::t('fin.deposit', 'Create Fixed Deposit');
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div class="box-footer" id="finDepositCreateForm">
        <?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
            <?= $form->field($model, 'saving_account')->dropDownList($arrSavingAccount, ['prompt'=>'']); ?>
            <?= $form->field($model, 'interest_rate')->textInput(['type'=>'number', 'step'=>'any']); ?>
            <?= $form->field($model, 'opening_date')->widget(DatePicker::className(), [
                'inline'=>false, 'dateFormat'=>'php:' . $phpFmShortDate, 'options'=>['class'=>'form-control']
            ]); ?>
            <?= $form->field($model, 'closing_date')->widget(DatePicker::className(), [
                'inline'=>false, 'dateFormat'=>'php:' . $phpFmShortDate, 'options'=>['class'=>'form-control']
            ]); ?>
            <?= $form->field($model, 'interest_add')->textInput(['type'=>'number']); ?>
            <?= $form->field($model, 'entry_value')->textInput(['type'=>'number']); ?>
            <?= $form->field($model, 'add_flag')->inline(true)->radioList($arrTimedepositTrantype); ?>
            <?= $form->field($model, 'current_assets')->dropDownList($arrCurrentAssets, ['prompt'=>'']); ?>
            <div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default btn-lg btn-block']); ?>
                <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div></div></div>