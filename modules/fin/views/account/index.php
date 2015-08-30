<?php
	use app\components\DateTimeUtils;
	use app\components\NumberUtils;
	
	$this->title = Yii::t('fin.account', 'Personal Accounts List');
	// for render
	$rowindex = 0;
	new Exception(\Yii::t('DateTimeUtils', 'No date format is supported'))
?>

<div class="row"><div class="col-xs-12"><div class="box">
	<div class="box-header">
		<h3 class="box-title"><?= Yii::t('fin.account', 'Time Deposits'); ?></h3>
	</div>
	<div class="box-body table-responsive no-padding"><table class="table table-bordered"><tbody>
		<tr class="warning">
			<th style="text-align: center">#</th>
			<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
			<th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Opening Deposit'); ?></th>
			<th style="text-align: center"><?= Yii::t('fin.grid', 'Date'); ?></th>
			<th style="text-align: center"><?= Yii::t('fin.grid', 'Days'); ?></th>
			<th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Closing Deposit'); ?></th>
			<th style="text-align: center" colspan="2"><?= DateTimeUtils::formatNow(DateTimeUtils::FM_VIEW_DATE); ?></th>
			<th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Interest'); ?></th>
		</tr>
		<?php foreach($arrDeposits as $deposits): ?>
			<?php
				$rowindex++;
			?>
			<tr>
				<td class="info" style="text-align: center"><?= $rowindex; ?></td>
				<td class="info" style="text-align: left"><?= $deposits->account_name; ?></td>
				<td class="danger" style="text-align: center"><?= DateTimeUtils::formatDateTimeFromDB($deposits->opening_date, DateTimeUtils::FM_VIEW_DATE); ?></td>
				<td class="danger" style="text-align: right"><?= NumberUtils::format($deposits->opening_balance); ?></td>
				<td class="success" style="text-align: center"><?= DateTimeUtils::formatDateTimeFromDB($deposits->closing_date, DateTimeUtils::FM_VIEW_DATE); ?></td>
				<td class="success" style="text-align: center"><?= $deposits->closing_diff; ?></td>
				<td class="success" style="text-align: right">*</td>
				<td class="success" style="text-align: right">*</td>
				<td class="danger" style="text-align: center"><?= $deposits->now_diff; ?></td>
				<td class="danger" style="text-align: center"><?= ($deposits->closing_diff - $deposits->now_diff); ?></td>
				<td class="danger" style="text-align: right">*</td>
				<td class="danger" style="text-align: right">*</td>
			</tr>
		<?php endforeach; ?>
		<tr class="warning">
			<th style="text-align: center">#</th>
			<th></th>
			<th></th>
			<th style="text-align: right"><?= Yii::t('fin.grid', 'Opening Deposit'); ?></th>
			<th style="text-align: center"><?= Yii::t('fin.grid', 'Date'); ?></th>
			<th></th>
			<th style="text-align: right"><?= Yii::t('fin.grid', 'Closing Deposit'); ?></th>
			<th style="text-align: right"><?= Yii::t('fin.grid', 'Closing Deposit'); ?></th>
			<th></th>
			<th></th>
			<th style="text-align: right"><?= Yii::t('fin.grid', 'Interest'); ?></th>
			<th style="text-align: right"><?= Yii::t('fin.grid', 'Interest'); ?></th>
		</tr>
	</tbody></table></div>
</div></div></div>