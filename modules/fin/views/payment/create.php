<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	
	$this->title = Yii::t('fin.payment', 'Create Payment');
?>
<div class="row"><div class="col-xs-12"><div class="box">
	<div class="box-header">
		<h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3>
	</div>
	<div id="finPaymentCreateForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
		<div class="col-md-6">
			<?php echo $form->field($model, 'entry_date')->widget(DatePicker::className(), [
				'inline'=>false, 'dateFormat'=>'php:' . $phpFmShortDate, 'options'=>[
					'class'=>'form-control'
				]
			]); ?>
			<?php echo $form->field($model, 'account_source')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
		</div>
		<div class="col-md-6">
			<?php echo $form->field($model, 'entry_value')->textInput(['type'=>'number']); ?>
			<?php echo $form->field($model, 'account_target')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
		</div>
		<div class="col-md-12">
			<?php echo $form->field($model, 'description')->textarea(['rows'=>6]); ?>
			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('button', 'Confirm'), ['class' => 'btn btn-primary']); ?>
			</div>
		</div>
	<?php ActiveForm::end(); ?></div>
</div></div></div>