<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use app\components\MasterValueUtils;
	use app\components\NumberUtils;
	use app\components\StringUtils;
	
	$formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
	$this->title = $formModeValue === MasterValueUtils::PG_MODE_CREATE ? Yii::t('fin.payment', 'Create Payment') : Yii::t('fin.payment', 'Edit Payment');
?>

<div class="box box-default">
	<div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
	<div id="finPaymentConfirmForm" class="box-body"><?php $form = ActiveForm::begin(); ?>
		<div class="row"><div class="col-md-12">
			<table class="table table-bordered">
				<tr>
					<th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('entry_date'); ?></th>
					<td class="info"><?= $model->entry_date; ?></td>
				</tr>
				<tr>
					<th class="warning"><?= $model->getAttributeLabel('account_source'); ?></th>
					<td class="info"><?= isset($arrFinAccount[$model->account_source]) ? $arrFinAccount[$model->account_source] : ''; ?></td>
				</tr>
				<tr>
					<th class="warning"><?= $model->getAttributeLabel('account_target'); ?></th>
					<td class="info"><?= isset($arrFinAccount[$model->account_target]) ? $arrFinAccount[$model->account_target] : ''; ?></td>
				</tr>
				<tr>
					<th class="warning"><?= $model->getAttributeLabel('entry_value'); ?></th>
					<td class="info"><?= NumberUtils::format($model->entry_value); ?></td>
				</tr>
				<?php if ($formModeValue === MasterValueUtils::PG_MODE_EDIT): ?>
					<tr>
						<th class="warning"><?= $model->getAttributeLabel('entry_adjust'); ?></th>
						<td class="info"><?= NumberUtils::format($model->entry_adjust); ?></td>
					</tr>
					<tr>
						<th class="warning"><?= $model->getAttributeLabel('entry_updated'); ?></th>
						<td class="info"><?= NumberUtils::format($model->entry_updated); ?></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th class="warning"><?= $model->getAttributeLabel('description'); ?></th>
					<td class="info"><?= StringUtils::showArrValueAsString($model->arr_entry_log, $arrEntryLog); ?></td>
				</tr>
			</table>
			<div style="display: none">
				<?= $form->field($model, 'entry_date')->hiddenInput(); ?>
				<?= $form->field($model, 'account_source')->hiddenInput(); ?>
				<?= $form->field($model, 'account_target')->hiddenInput(); ?>
				<?= $form->field($model, 'entry_value')->hiddenInput(); ?>
				<?= $form->field($model, 'entry_adjust')->hiddenInput(); ?>
				<?= $form->field($model, 'arr_entry_log')->checkboxList($arrEntryLog); ?>
			</div>
			<div class="form-group">
				<?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
				<?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
			</div>
		</div></div>
	<?php ActiveForm::end(); ?></div>
</div>