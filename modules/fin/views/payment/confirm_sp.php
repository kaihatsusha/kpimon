<?php
	use yii\bootstrap\ActiveForm;
	use yii\helpers\Html;
	use app\components\MasterValueUtils;
	use app\components\NumberUtils;
	
	$this->title = $formMode[MasterValueUtils::PG_MODE_NAME] === MasterValueUtils::PG_MODE_CREATE ? Yii::t('fin.payment', 'Create Payment') : Yii::t('fin.payment', 'Edit Payment');
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
	<div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
	<div class="box-footer" id="finPaymentConfirmForm"><?php $form = ActiveForm::begin(); ?>
		<ul class="nav nav-stacked nav-no-padding">
			<li><a href="javascript:void(0);">
				<?= $model->getAttributeLabel('entry_date'); ?>
				<span class="pull-right"><?= $model->entry_date; ?></span>
			</a></li>
			<li><a href="javascript:void(0);">
				<?= $model->getAttributeLabel('account_source'); ?>
				<span class="pull-right"><?= isset($arrFinAccount[$model->account_source]) ? $arrFinAccount[$model->account_source] : ''; ?></span>
			</a></li>
			<li><a href="javascript:void(0);">
				<?= $model->getAttributeLabel('account_target'); ?>
				<span class="pull-right"><?= isset($arrFinAccount[$model->account_target]) ? $arrFinAccount[$model->account_target] : ''; ?></span>
			</a></li>
			<li><a href="javascript:void(0);">
				<?= $model->getAttributeLabel('entry_value'); ?>
				<span class="pull-right badge bg-red"><?= NumberUtils::format($model->entry_value); ?></span>
			</a></li>
			<li><a href="javascript:void(0);">
				<?= $model->getAttributeLabel('description'); ?>
				<span class="pull-right"><?= $model->description; ?></span>
			</a></li>
		</ul>
		<div style="display: none">
			<?= $form->field($model, 'entry_date')->hiddenInput(); ?>
			<?= $form->field($model, 'account_source')->hiddenInput(); ?>
			<?= $form->field($model, 'account_target')->hiddenInput(); ?>
			<?= $form->field($model, 'entry_value')->hiddenInput(); ?>
			<?= $form->field($model, 'description')->textarea(); ?>
		</div>
		<div class="form-group">
			<?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
			<?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
		</div>
	<?php ActiveForm::end(); ?></div>
</div></div></div>