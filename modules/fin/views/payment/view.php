<?php
	use app\components\DateTimeUtils;
	use app\components\NumberUtils;
	
	$this->title = Yii::t('fin.payment', 'Details of Payment');
?>

<?php if($model): ?><div class="box box-default">
	<div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
	<div class="box-body"><div class="row"><div class="col-md-12">
		<table class="table table-bordered">
			<tr>
				<th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('entry_id'); ?></th>
				<td class="info"><?= $model->entry_id; ?></td>
			</tr>
			<tr>
				<th class="warning"><?= $model->getAttributeLabel('entry_date'); ?></th>
				<td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
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
			<tr>
				<th class="warning"><?= $model->getAttributeLabel('description'); ?></th>
				<td class="info"><?= $model->description; ?></td>
			</tr>
		</table>
	</div></div></div>
</div><?php endif; ?>