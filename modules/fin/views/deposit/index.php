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
	use app\components\StringUtils;
	use app\components\yii2grid\DataColumn;
	
	$this->title = Yii::t('fin.deposit', 'Time Deposit Accounts List');
	$phpFmShortDateGui = 'php:' . $phpFmShortDate;

	$htmlFooterInterest = '<span class="text-info">' . NumberUtils::format($sumTimeDepositValue['interest_add']) . '</span>';
	$htmlFooterInterest .= '<br/><span class="text-success">' . NumberUtils::format($sumTimeDepositValue['adding_value'] - $sumTimeDepositValue['withdrawal_value']) . '</span>';
	$htmlFooterAdding = '<span class="text-danger">' . NumberUtils::format($sumTimeDepositValue['withdrawal_value']) . '</span>';
	$htmlFooterAdding .= '<br/><span class="text-info">' . NumberUtils::format($sumTimeDepositValue['adding_value']) . '</span>';
?>

<div class="row"><div class="col-md-12"><div class="box">
	<div class="box-header with-border">
		<h3 class="box-title"><?= Yii::t('fin.deposit', 'Transaction'); ?></h3>
	</div>
	<div class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
		<div class="row">
			<div class="col-md-6">
				<?= $form->field($searchModel, 'opening_date_from')->widget(DatePicker::className(), [
					'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
						'class'=>'form-control'
					]
				]); ?>
				<?= $form->field($searchModel, 'saving_account')->dropDownList($arrSavingAccount, ['prompt'=>'']); ?>
			</div>
			<div class="col-md-6">
				<?= $form->field($searchModel, 'opening_date_to')->widget(DatePicker::className(), [
					'inline'=>false, 'dateFormat'=>$phpFmShortDateGui, 'options'=>[
						'class'=>'form-control'
					]
				]); ?>
				<?= $form->field($searchModel, 'current_assets')->dropDownList($arrCurrentAssets, ['prompt'=>'']); ?>
			</div>
			<div class="col-md-12"><div class="form-group">
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
					'footerOptions'=>['style'=>'text-align: right', 'colspan'=>5],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model, $key, $index, $column) {
						$pagination = $column->grid->dataProvider->pagination;
						return $pagination->page * $pagination->pageSize + $index + 1;
					},
					'footer'=>Yii::t('fin.grid', 'Total')
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Reference'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) {
						return str_pad($model->transactions_id, 6, '0', STR_PAD_LEFT);
					}
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Saving Account'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) use ($arrSavingAccount) {
						return isset($arrSavingAccount[$model->saving_account]) ? $arrSavingAccount[$model->saving_account] : '';
					}
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Unit'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'format'=>'raw',
					'value'=>function($model) {
						$interestRate = NumberUtils::format($model->interest_rate, 4) . ' %';
						$interestUnit = NumberUtils::format($model->interest_unit, 4) . ' d';
						return $interestRate . '<br/>' . $interestUnit;
					}
				],
				[
					'class'=>DataColumn::className(),
					'label'=>Yii::t('fin.grid', 'Term'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['colspan'=>0],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'format'=>'raw',
					'value'=>function($model) use ($phpFmShortDate) {
						$openingDate = DateTimeUtils::htmlDateFormatFromDB($model->opening_date, DateTimeUtils::FM_VIEW_DATE, true);
						$closingDate = DateTimeUtils::htmlDateFormatFromDB($model->closing_date, DateTimeUtils::FM_VIEW_DATE, true);
						return $openingDate . '<br/>' . $closingDate;
					}
				],
				[
					'label'=>Yii::t('fin.grid', 'Principal'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right'],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: right', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'format'=>'raw',
					'value'=>function($model) {
						$interestAdd = NumberUtils::format($model->interest_add);
						$entryValue = NumberUtils::format($model->entry_value);
						return $entryValue . '<br/>' . $interestAdd;
					},
					'footer'=>$htmlFooterInterest
				],
				[
					'label'=>Yii::t('fin.grid', 'Amount Type'),
					'headerOptions'=>['style'=>'text-align: center'],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) use ($arrTimedepositTrantype) {
						return isset($arrTimedepositTrantype[$model->add_flag]) ? $arrTimedepositTrantype[$model->add_flag] : '';
					}
				],
				[
					'label'=>Yii::t('fin.grid', 'Current Assets'),
					'headerOptions'=>['style'=>'text-align: center'],
					'footerOptions'=>['style'=>'text-align: right'],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: left', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'value'=>function($model) use ($arrCurrentAssets) {
						return isset($arrCurrentAssets[$model->current_assets]) ? $arrCurrentAssets[$model->current_assets] : '';
					},
					'footer'=>$htmlFooterAdding
				],
				[
					'label'=>Yii::t('fin.grid', 'Action'),
					'headerOptions'=>['style'=>'text-align: center; width: 100px;'],
					'contentOptions'=>function($model, $key, $index) {
						return ['style'=>'vertical-align: middle; text-align: center', 'class'=>MasterValueUtils::getColorRow($index)];
					},
					'format'=>'raw',
					'value'=>function($model, $key, $index) {
						$btnClass = MasterValueUtils::getColorRow($index);
						$lblView = Yii::t('button', 'View');
						$lblEdit = Yii::t('button', 'Edit');
						$lblCopy = Yii::t('button', 'Copy');
						$arrBtns = [];

						$entryId = $model->transactions_id;
						$urlEdit = BaseUrl::toRoute(['deposit/update', 'id'=>$entryId]);
						$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

						$urlView = BaseUrl::toRoute(['deposit/view', 'id'=>$entryId]);
						$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlView, $lblView]);

						$urlCopy = BaseUrl::toRoute(['deposit/copy', 'id'=>$entryId]);
						$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlCopy, $lblCopy]);

						$html = '<div class="btn-group">';
						$html .= Html::a($lblEdit, [$urlEdit], ['class'=>'btn btn-' . $btnClass]);
						$html .= '<button type="button" class="btn btn-' . $btnClass . ' dropdown-toggle" data-toggle="dropdown">';
						$html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
						$html .= '</button>';
						$html .= '<ul class="dropdown-menu" role="menu">';
						$html .= implode('', $arrBtns);
						$html .= '</ul></div>';

						return $html;
					}
				]
			]
		]); ?><?php Pjax::end(); ?></div>
	<?php ActiveForm::end(); ?></div>
</div></div></div>