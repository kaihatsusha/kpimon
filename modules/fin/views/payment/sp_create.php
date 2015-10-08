<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use app\components\MasterValueUtils;
	use app\components\ModelUtils;
	
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
			<?= $form->field($model, 'entry_date')->widget(DatePicker::className(), [
				'inline'=>false, 'dateFormat'=>'php:' . $phpFmShortDate, 'options'=>[
					'class'=>'form-control', 'ng-model'=>'mdModel.entry_date'
				]
			]); ?>
			<?= $form->field($model, 'account_source')->dropDownList($arrFinAccount, ['prompt'=>'', 'ng-model'=>'mdModel.account_source']); ?>
			<?= $form->field($model, 'account_target')->dropDownList($arrFinAccount, ['prompt'=>'', 'ng-model'=>'mdModel.account_target']); ?>
			<?= $form->field($model, 'entry_value')->textInput(['type'=>'number', 'ng-model'=>'mdModel.entry_value']); ?>
			<?= $form->field($model, 'description')->textarea(['rows'=>3, 'ng-model'=>'mdModel.description']); ?>
			<div class="form-group">
				<?= Html::button(Yii::t('button', 'Reset'), ['class'=>'btn btn-default btn-lg btn-block', 'ng-click'=>'fnReset()']); ?>
				<?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
			</div>
		</div></div>
	<?php ActiveForm::end(); ?></div>
</div>
<script type="text/javascript">
var myAngularApp = angular.module('myAngularApp', []);
myAngularApp.controller('myAngularCtrl', function($scope) {
    $scope.mdMaster = <?php echo ModelUtils::toJsonHtmlEncode($model); ?>;
	$scope.mdMaster.entry_value = isNaN(parseInt($scope.mdMaster.entry_value)) ? 0 : parseInt($scope.mdMaster.entry_value);
	$scope.fnReset = function() {
		$scope.mdModel = angular.copy($scope.mdMaster);
		$('#finaccountentry-entry_date').datepicker('setDate', $scope.mdMaster.entry_date);
	};
	$scope.fnReset();
});
</script>