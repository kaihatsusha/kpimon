<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use app\components\MasterValueUtils;
	use app\modules\fin\views\PaymentAsset;
	
	// css & js
	PaymentAsset::register($this);
	
	$this->title = Yii::t('fin.payment', 'Create Payment');
?>

<div class="box box-default">
	<div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
	<div id="finPaymentCreateForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
		<div class="row"><div class="col-md-12">
			<?= $form->field($model, 'entry_date')->widget(DatePicker::className(), [
				'inline'=>false, 'dateFormat'=>'php:' . $phpFmShortDate, 'options'=>['class'=>'form-control']
			]); ?>
			<?= $form->field($model, 'account_source')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			<?= $form->field($model, 'account_target')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			<?= $form->field($model, 'entry_value')->textInput(['type'=>'number']); ?>
			<?= $form->field($model, 'arr_entry_log')->inline(true)->checkboxList($arrEntryLog); ?>
			<div class="form-group">
				<?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
				<?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
			</div>
		</div></div>
	<?php ActiveForm::end(); ?></div>
</div>