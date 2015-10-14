<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use app\components\MasterValueUtils;
	
	$this->title = Yii::t('fin.payment', 'Create Payment');
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
	<div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
	<div class="box-footer" id="finPaymentCreateForm">
		<?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
			<?= $form->field($model, 'entry_date')->widget(DatePicker::className(), [
				'inline'=>false, 'dateFormat'=>'php:' . $phpFmShortDate, 'options'=>['class'=>'form-control']
			]); ?>
			<?= $form->field($model, 'account_source')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			<?= $form->field($model, 'account_target')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			<?= $form->field($model, 'entry_value')->textInput(['type'=>'number']); ?>
			<?= $form->field($model, 'description')->textarea(['rows'=>3]); ?>
			<div class="form-group">
				<?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default btn-lg btn-block']); ?>
				<?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
			</div>
		<?php ActiveForm::end(); ?>
	</div>
</div></div></div>