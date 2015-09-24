<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use app\components\MasterValueUtils;
	$this->title = $formMode[MasterValueUtils::PG_MODE_NAME] === MasterValueUtils::PG_MODE_CREATE ? Yii::t('fin.payment', 'Create Payment') : Yii::t('fin.payment', 'Edit Payment');
?>
<?php if(Yii::$app->session->hasFlash(MasterValueUtils::FLASH_ERROR)): ?><div class="alert alert-error">
	<?php echo Yii::$app->session->getFlash(MasterValueUtils::FLASH_ERROR); ?>
</div><?php endif; ?>
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3>
	</div>
	<div id="finPaymentCreateForm" class="box-body"><?php $form = ActiveForm::begin(); ?>
		<div class="row"><div class="col-md-12">
			<div class="form-group"><table class="table table-bordered">
				<tr>
					<th class="warning" style="width: 200px;"><?php echo $model->getAttributeLabel('entry_date'); ?></th>
					<td class="info"><?php echo $model->entry_date; ?></td>
				</tr>
				<tr>
					<th class="warning"><?php echo $model->getAttributeLabel('account_source'); ?></th>
					<td class="info"><?php echo isset($arrFinAccount[$model->account_source]) ? $arrFinAccount[$model->account_source] : ''; ?></td>
				</tr>
				<tr>
					<th class="warning"><?php echo $model->getAttributeLabel('account_target'); ?></th>
					<td class="info"><?php echo isset($arrFinAccount[$model->account_target]) ? $arrFinAccount[$model->account_target] : ''; ?></td>
				</tr>
				<tr>
					<th class="warning"><?php echo $model->getAttributeLabel('entry_value'); ?></th>
					<td class="info"><?php echo $model->entry_value; ?></td>
				</tr>
				<tr>
					<th class="warning"><?php echo $model->getAttributeLabel('description'); ?></th>
					<td class="info"><?php echo $model->description; ?></td>
				</tr>
			</table></div>
			<div style="display: none">
				<?php echo $form->field($model, 'entry_date')->hiddenInput(); ?>
				<?php echo $form->field($model, 'account_source')->hiddenInput(); ?>
				<?php echo $form->field($model, 'account_target')->hiddenInput(); ?>
				<?php echo $form->field($model, 'entry_value')->hiddenInput(); ?>
				<?php echo $form->field($model, 'description')->textarea(); ?>
			</div>
			<div class="form-group">
				<?php echo Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-primary', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
				<?php echo Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-primary', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
			</div>
		</div></div>
	<?php ActiveForm::end(); ?></div>
</div>