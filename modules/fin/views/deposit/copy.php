<?php
    //use yii\bootstrap\ActiveForm;
    //use yii\helpers\Html;
    //use yii\jui\DatePicker;
    //use app\components\MasterValueUtils;

    $this->title = Yii::t('fin.deposit', 'Copy Fixed Deposit');
?>

<?php if ($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div id="finDepositCopyForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
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
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
                <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div><?php endif; ?>