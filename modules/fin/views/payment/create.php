<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	//use app\components\DateTimeUtils;
	//use app\components\NumberUtils;
	
	//$this->title = Yii::t('fin.payment', 'Personal Accounts List');
?>
<div class="finPaymentCreateForm"><?php $form = ActiveForm::begin(); ?>
	<?php echo $form->field($model, 'entry_date')->widget(DatePicker::className(), [
		'inline'=>false, 'dateFormat'=>'yyyy-MM-dd', 'options'=>[
			'class'=>'form-control', 'maxlength'=>10
		]
	]); ?>
	<?php echo $form->field($model, 'entry_value'); ?>
	<div class="form-group">
        <?php echo Html::submitButton(Yii::t('button', 'Entry'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end(); ?></div>