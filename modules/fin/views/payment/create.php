<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use app\components\MasterValueUtils;
	
	$this->title = Yii::t('fin.payment', 'Create Payment');
?>
<?php if(Yii::$app->session->hasFlash(MasterValueUtils::FLASH_ERROR)): ?><div class="alert alert-error">
	<?php echo Yii::$app->session->getFlash(MasterValueUtils::FLASH_ERROR); ?>
</div><?php endif; ?>
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3>
	</div>
	<div id="finPaymentCreateForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
		<div class="row"><div class="col-md-12" ng-app="myAngularApp" ng-controller="myAngularCtrl">
			<?php echo $form->field($model, 'entry_date')->widget(DatePicker::className(), [
				'inline'=>false, 'dateFormat'=>'php:' . $phpFmShortDate, 'options'=>[
					'class'=>'form-control'
				]
			]); ?>
			<?php echo $form->field($model, 'account_source')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			<?php echo $form->field($model, 'account_target')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			<?php echo $form->field($model, 'entry_value')->textInput(['type'=>'number', 'ng-model'=>'firstName']); ?>
			<?php echo $form->field($model, 'description')->textarea(['rows'=>3]); ?>
			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
			</div>
		</div></div>
	<?php ActiveForm::end(); ?></div>
</div>
<script type="text/javascript">
var myAngularApp = angular.module('myAngularApp', []);
myAngularApp.controller('myAngularCtrl', function($scope) {
    //$scope.firstName = 10500;
    //$scope.lastName = "Doe";
});
</script>