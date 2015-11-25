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
	
	$this->title = Yii::t('fin.payment', 'Payments List');
	$phpFmShortDateGui = 'php:' . $phpFmShortDate;
	
	$htmlFooterCreditDebit = '';
	$htmlFooterCreditDebit .= '<span class="label label-danger pull-left">' . NumberUtils::format($sumEntryValue['entry_source']) . '</span>';
	$htmlFooterCreditDebit .= '<span class="label label-success pull-right">' . NumberUtils::format($sumEntryValue['entry_target'] - $sumEntryValue['entry_source']) . '</span>';
	
	$htmlFooterDate = '<span class="label label-info pull-left">' . NumberUtils::format($sumEntryValue['entry_target']) . '</span>';
?>

<div class="row"><div class="col-md-12"><div class="box box-default collapsed-box">
	<div class="box-header">
		<h3 class="box-title"><?= Yii::t('fin.payment', 'Transaction'); ?></h3>
		<div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
		</div>
	</div>
	<div class="box-body" style="padding-bottom: 0;"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
		<div class="row"><div class="col-md-12">
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
		</div></div>
	<?php ActiveForm::end(); ?></div>	
	<div class="box-body-notool"><div class="row"><?php Pjax::begin(); ?><?= GridView::widget([
		'layout'=>'{summary}<div class="table-responsive">{items}</div>{pager}',
		'options'=>['class'=>'grid-view col-xs-12'],
		'tableOptions'=>['class'=>'table table-bordered'],
		'showFooter'=>true,
		'footerRowOptions'=>['style'=>'font-weight:bold'],
		'pager'=>['options'=>['class'=>'pagination pagination-bottom'], 'maxButtonCount'=>6],
		'dataProvider'=>new ActiveDataProvider([
			'query'=>$dataQuery,
			'pagination'=>['pagesize'=>20]
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
					$html = DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE, true);

					$lblView = Yii::t('button', 'View');
					$lblEdit = Yii::t('button', 'Edit');
					$lblCopy = Yii::t('button', 'Copy');
					$urlEdit = false;
					$arrBtns = [];

					$entryId = $model->entry_id;
					$timeDepositTranId = $model->time_deposit_tran_id;
					switch ($model->entry_status) {
						case MasterValueUtils::MV_FIN_ENTRY_TYPE_SIMPLE:
							$urlEdit = BaseUrl::toRoute(['payment/update', 'id'=>$entryId]);
							$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

							$urlView = BaseUrl::toRoute(['payment/view', 'id'=>$entryId]);
							$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlView, $lblView]);

							$urlCopy = BaseUrl::toRoute(['payment/copy', 'id'=>$entryId]);
							$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlCopy, $lblCopy]);
							break;
						case MasterValueUtils::MV_FIN_ENTRY_TYPE_DEPOSIT:
						case MasterValueUtils::MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT:
							$urlEdit = BaseUrl::toRoute(['deposit/update', 'id'=>$timeDepositTranId]);
							$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlEdit, $lblEdit]);

							$urlView = BaseUrl::toRoute(['deposit/view', 'id'=>$timeDepositTranId]);
							$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlView, $lblView]);

							$urlCopy = BaseUrl::toRoute(['deposit/copy', 'id'=>$timeDepositTranId]);
							$arrBtns[] = StringUtils::format('<li><a href="{0}">{1}</a></li>', [$urlCopy, $lblCopy]);
							break;
						default;
							break;
					}

					$html .= '<br/><div class="btn-group">';
					$html .= Html::a($lblEdit, [$urlEdit], ['class'=>'btn btn-xs btn-info']);
					$html .= '<button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">';
					$html .= '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
					$html .= '</button>';
					$html .= '<ul class="dropdown-menu" role="menu">';
					$html .= implode('', $arrBtns);
					$html .= '</ul></div>';

					return $html;
				},
				'footer'=>$htmlFooterDate
			],
			[
				'label'=>Yii::t('fin.grid', 'Credit / Debit'),
				'headerOptions'=>['style'=>'text-align: center'],
				'footerOptions'=>['style'=>'text-align: right'],
				'contentOptions'=>['style'=>'vertical-align: middle; text-align: left; min-width:162px'],
				'format'=>'raw',
				'value'=>function($model) use ($arrFinAccount) {
					$htmls = [];

					$htmlCredit = isset($arrFinAccount[$model->account_target]) ? $arrFinAccount[$model->account_target] : '';
					if (!empty($htmlCredit)) {
						$amount = $model->account_target == 0 ? '' : NumberUtils::format($model->entry_value);
						$htmlCredit .= '<span class="label label-info pull-right">' . $amount . '</span>';
						$htmls[] = $htmlCredit;
					}

					$htmlDebit = isset($arrFinAccount[$model->account_source]) ? $arrFinAccount[$model->account_source] : '';
					if (!empty($htmlDebit)) {
						$amount = $model->account_source == 0 ? '' : NumberUtils::format($model->entry_value);
						$htmlDebit .= '<span class="label label-danger pull-right">' . $amount . '</span>';
						$htmls[] = $htmlDebit;
					}

					return implode('<br/>', $htmls);
				},
				'footer'=>$htmlFooterCreditDebit
			],
			[
				'attribute'=>'description',
				'label'=>Yii::t('fin.grid', 'Description'),
				'headerOptions'=>['style'=>'text-align: center'],
				'contentOptions'=>['style'=>'vertical-align: middle; text-align: left'],
				'enableSorting'=>false,
				'value'=>function($model) use ($arrEntryLog) {
					$arrEntryLogVal = StringUtils::unserializeArr($model->description);
					return StringUtils::showArrValueAsString($arrEntryLogVal, $arrEntryLog);
				}
			]
		]
	]); ?><?php Pjax::end(); ?></div></div>
</div></div></div>