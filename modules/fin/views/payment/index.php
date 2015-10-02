<?php
	use yii\bootstrap\ActiveForm;
	use yii\data\ActiveDataProvider;
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use yii\widgets\Pjax;
	use app\components\MasterValueUtils;
	use app\components\NumberUtils;
	
	$this->title = Yii::t('fin.payment', 'Payments List');
	$phpFmShortDateGui = 'php:' . $phpFmShortDate;
?>
<?php if(Yii::$app->session->hasFlash(MasterValueUtils::FLASH_SUCCESS)): ?><div class="alert alert-success">
	<?php echo Yii::$app->session->getFlash(MasterValueUtils::FLASH_SUCCESS); ?>
</div><?php endif; ?>
<?php if(Yii::$app->session->hasFlash(MasterValueUtils::FLASH_ERROR)): ?><div class="alert alert-error">
	<?php echo Yii::$app->session->getFlash(MasterValueUtils::FLASH_ERROR); ?>
</div><?php endif; ?>

<div class="row"><div class="col-xs-12"><div class="box">
	<div class="box-header with-border">
		<h3 class="box-title"><?= Yii::t('fin.payment', 'Transaction'); ?></h3>
	</div>
	<div class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
		<div class="row">
			<div class="col-xs-6">
				<?= $form->field($searchModel, 'entry_date_from')->widget(DatePicker::className(), [
					'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
						'class'=>'form-control'
					]
				]); ?>
				<?= $form->field($searchModel, 'account_source')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			</div>
			<div class="col-xs-6">
				<?= $form->field($searchModel, 'entry_date_to')->widget(DatePicker::className(), [
					'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
						'class'=>'form-control'
					]
				]); ?>
				<?= $form->field($searchModel, 'account_target')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
			</div>
			<div class="col-xs-12"><div class="form-group">
				<?= Html::a(Yii::t('button', 'Create'), ['/fin/payment/create'], ['class'=>'btn btn-info']) ?>
				<?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
			</div></div>
		</div>
		<div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
			'options'=>['class'=>'grid-view col-xs-12 table-responsive'],
			'tableOptions'=>['class'=>'table table-bordered'],
			'showFooter'=>true,
			'dataProvider'=>new ActiveDataProvider([
				'query'=>$dataQuery,
				'pagination'=>['pagesize'=>20]
			]),
			'columns'=>[
				[
					'label'=>Yii::t('fin.grid', 'No.'),
					'value'=>function($model, $key) {
						return $key;
					}
				],
				[
					'label'=>Yii::t('fin.grid', 'Reference'),
					'value'=>function($model) {
						return str_pad($model->entry_id, 6, '0', STR_PAD_LEFT);
					}
				],
				[
					'attribute'=>'entry_date',
					'label'=>Yii::t('fin.grid', 'Transaction Date'),
					'format'=>['date', $phpFmShortDateGui]
				],
				[
					'label'=>Yii::t('fin.grid', 'Credit Account'),
					'value'=>function($model) use ($arrFinAccount) {
						return isset($arrFinAccount[$model->account_target]) ? $arrFinAccount[$model->account_target] : '';
					}
				],
				[
					'label'=>Yii::t('fin.grid', 'Credit Amount'),
					'value'=>function($model) {
						$amount = $model->account_target == 0 ? '' : NumberUtils::format($model->entry_value);
						return $amount;
					}
				],
				[
					'label'=>Yii::t('fin.grid', 'Debit Account'),
					'value'=>function($model) use ($arrFinAccount) {
						return isset($arrFinAccount[$model->account_source]) ? $arrFinAccount[$model->account_source] : '';
					}
				],
				[
					'label'=>Yii::t('fin.grid', 'Debit Amount'),
					'value'=>function($model) {
						$amount = $model->account_source == 0 ? '' : NumberUtils::format($model->entry_value);
						return $amount;
					}
				],
				[
					'attribute'=>'description',
					'label'=>Yii::t('fin.grid', 'Description'),
					'enableSorting'=>false
				],
				[
					'label'=>Yii::t('fin.grid', 'Action'),
					'value'=>function($model) {
						return '';
					}
				]
			]
		]); ?><?php Pjax::end(); ?></div>
	<?php ActiveForm::end(); ?></div>
</div></div>