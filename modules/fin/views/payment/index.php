<?php
	use yii\bootstrap\ActiveForm;
	use yii\data\ActiveDataProvider;
	use yii\grid\GridView;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use yii\widgets\Pjax;
	use app\components\MasterValueUtils;
	use app\components\NumberUtils;
	use app\components\yii2grid\DataColumn;
	
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
			'headerRowOptions'=>['class'=>'warning'],
			'footerRowOptions'=>['class'=>'warning', 'style'=>'font-weight:bold'],
			'dataProvider'=>new ActiveDataProvider([
				'query'=>$dataQuery,
				'pagination'=>['pagesize'=>10]
			]),
			'columns'=>[
				[
					'label'=>Yii::t('fin.grid', 'No.'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right', 'colspan'=>3],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model, $key) {
						return $key;
					},
					'footer'=>Yii::t('fin.grid', 'Total')
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Reference'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) {
						return str_pad($model->entry_id, 6, '0', STR_PAD_LEFT);
					},
					'footer'=>false
				],
				[
					'class'=>DataColumn::className(),
					'attribute'=>'entry_date',
					'label'=>Yii::t('fin.grid', 'Transaction Date'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'format'=>['date', $phpFmShortDateGui]
				],
				[
					'label'=>Yii::t('fin.grid', 'Credit Account'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right', 'colspan'=>2],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) use ($arrFinAccount) {
						return isset($arrFinAccount[$model->account_target]) ? $arrFinAccount[$model->account_target] : '';
					},
					'footer'=>$phpFmShortDateGui
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Credit Amount'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) {
						$amount = $model->account_target == 0 ? '' : NumberUtils::format($model->entry_value);
						return $amount;
					}
				],
				[
					'label'=>Yii::t('fin.grid', 'Debit Account'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right', 'colspan'=>2],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) use ($arrFinAccount) {
						return isset($arrFinAccount[$model->account_source]) ? $arrFinAccount[$model->account_source] : '';
					}
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Debit Amount'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) {
						$amount = $model->account_source == 0 ? '' : NumberUtils::format($model->entry_value);
						return $amount;
					}
				],
				[
					'attribute'=>'description',
					'label'=>Yii::t('fin.grid', 'Description'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right', 'colspan'=>2],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'enableSorting'=>false
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Action'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'format'=>'raw',
					'value'=>function($model, $key, $index) {
						$btnClass = MasterValueUtils::getColorRow($index);
						$html = '<div class="btn-group">';
						$html .= '<button type="button" class="btn btn-' . $btnClass . '">' . Yii::t('button', 'View') . '</button>';
						$html .= '<button type="button" class="btn btn-' . $btnClass . ' dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li><a href="#">Separated link</a></li>
                      </ul>
                    </div>';
						return $html;
					}
				]
			]
		]); ?><?php Pjax::end(); ?></div>
	<?php ActiveForm::end(); ?></div>
</div></div>