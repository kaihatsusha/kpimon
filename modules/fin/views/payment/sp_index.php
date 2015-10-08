<?php
	use yii\bootstrap\ActiveForm;
	use yii\data\ActiveDataProvider;
	use yii\grid\GridView;
	use yii\helpers\BaseUrl;
	use yii\helpers\Html;
	use yii\jui\DatePicker;
	use yii\widgets\Pjax;
	use app\components\DateTimeUtils;
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

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
	<div class="box-header">
		<h3 class="box-title"><?= Yii::t('fin.payment', 'Transaction'); ?></h3>
		<div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
		</div>
	</div>
	<div class="box-body" style="padding-bottom: 0;"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
		<div class="row">
			<div class="col-md-12">
				<?= $form->field($searchModel, 'entry_date_from')->widget(DatePicker::className(), [
					'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
						'class'=>'form-control'
					]
				]); ?>
				<?= $form->field($searchModel, 'entry_date_to')->widget(DatePicker::className(), [
					'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
						'class'=>'form-control'
					]
				]); ?>
				<?= $form->field($searchModel, 'account_source')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
				<?= $form->field($searchModel, 'account_target')->dropDownList($arrFinAccount, ['prompt'=>'']); ?>
				<div class="form-group">
					<?= Html::submitButton(Yii::t('button', 'Search'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
				</div>
			</div>
		</div>
	<?php ActiveForm::end(); ?></div>	
	<div class="box-body-notool">
		<div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
			'layout'=>'{summary}<div class="table-responsive">{items}</div>{pager}',
			'options'=>['class'=>'grid-view col-xs-12'],
			'tableOptions'=>['class'=>'table table-bordered'],
			'showFooter'=>true,
			'footerRowOptions'=>['style'=>'font-weight:bold'],
			'pager'=>['options'=>['class'=>'pagination pagination-bottom'], 'maxButtonCount'=>6],
			'dataProvider'=>new ActiveDataProvider([
				'query'=>$dataQuery,
				'pagination'=>['pagesize'=>10]
			]),
			'columns'=>[
				[
					'label'=>Yii::t('fin.grid', 'Ref'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right'],
					'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
					'format'=>'raw',
					'value'=>function($model, $key, $index, $column) {
						$pagination = $column->grid->dataProvider->pagination;
						$html = $pagination->page * $pagination->pageSize + $index + 1;
						$html .= '<br/>' . str_pad($model->entry_id, 6, '0', STR_PAD_LEFT);
						
						return $html;
					},
					'footer'=>Yii::t('fin.grid', 'Total')
				],
				[
					'attribute'=>'entry_date',
					'label'=>Yii::t('fin.grid', 'Date'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right'],
					'contentOptions'=>['style'=>'vertical-align: middle; text-align: center'],
					'format'=>'raw',
					'value'=>function($model) use ($phpFmShortDate) {
						$html = DateTimeUtils::formatDateFromDB($model->entry_date, $phpFmShortDate);
						
						$lblView = Yii::t('button', 'View');
						$lblEdit = Yii::t('button', 'Edit');
						$lblCopy = Yii::t('button', 'Copy');
						$urlView = $urlEdit = '#';
						$urlCopy = false;
						if ($model->entry_status == MasterValueUtils::MV_FIN_ENTRY_TYPE_SIMPLE) {
							$urlView = BaseUrl::toRoute(['payment/view', 'id'=>$model->entry_id]);
							$urlEdit = BaseUrl::toRoute(['payment/update', 'id'=>$model->entry_id]);
							$urlCopy = BaseUrl::toRoute(['payment/copy', 'id'=>$model->entry_id]);
						}
						
						$html .= '<br/><div class="btn-group">';
						$html .= Html::a($lblEdit, [$urlEdit], ['class'=>'btn btn-xs btn-info']);
						$html .= '<button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">';
						$html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
						$html .= '</button>';
						$html .= '<ul class="dropdown-menu" role="menu">';
						$html .= '<li><a href="' . $urlView . '">' . $lblView . '</a></li>';
						if ($urlCopy) {
							$html .= '<li><a href="' . $urlCopy . '">' . $lblCopy . '</a></li>';
						}
						$html .= '</ul></div>';
						
						return $html;
					},
					'footer'=>NumberUtils::format($sumEntryValue['entry_target'] - $sumEntryValue['entry_source'])
				],
				[
					'label'=>Yii::t('fin.grid', 'Debit'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right'],
					'contentOptions'=>['style'=>'vertical-align: middle; text-align: left; min-width:162px'],
					'format'=>'raw',
					'value'=>function($model) use ($arrFinAccount) {
						$html = isset($arrFinAccount[$model->account_source]) ? $arrFinAccount[$model->account_source] : '';
						//if (!empty($html)) {
						$amount = $model->account_source == 0 ? '0' : NumberUtils::format($model->entry_value);
						$html .= '<span class="label label-danger pull-right">' . $amount . '</span>';
						//}
						return $html;
					},
					'footer'=>NumberUtils::format($sumEntryValue['entry_source'])
				],
				[
					'label'=>Yii::t('fin.grid', 'Credit'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right'],
					'contentOptions'=>['style'=>'vertical-align: middle; text-align: left; min-width:162px'],
					'format'=>'raw',
					'value'=>function($model) use ($arrFinAccount) {
						$html = isset($arrFinAccount[$model->account_target]) ? $arrFinAccount[$model->account_target] : '';
						//if (!empty($html)) {
						$amount = $model->account_target == 0 ? '0' : NumberUtils::format($model->entry_value);
						$html .= '<span class="label label-info pull-right">' . $amount . '</span>';
						//}
						return $html;
					},
					'footer'=>NumberUtils::format($sumEntryValue['entry_target'])
				],
				[
					'attribute'=>'description',
					'label'=>Yii::t('fin.grid', 'Description'),
					'headerOptions'=>['style'=>'text-align: center'],
					'contentOptions'=>['style'=>'vertical-align: middle; text-align: left'],
					'enableSorting'=>false
				]
			]
		]); ?><?php Pjax::end(); ?></div>
	</div>
</div></div></div>
