<?php
	use app\components\DateTimeUtils;
	use app\components\NumberUtils;
	
	$this->title = Yii::t('fin.account', 'Personal Accounts List');
	// for render
	$rowindex = 0;
	$viewToday = DateTimeUtils::formatNow(DateTimeUtils::FM_VIEW_DATE);
?>

<div class="row"><div class="col-md-12"><div class="box">
	<div class="box-header">
		<h3 class="box-title"><?= Yii::t('fin.account', 'Time Deposits'); ?></h3>
	</div>
	<div class="box-body table-responsive no-padding"><table class="table table-bordered"><tbody>
		<tr>
			<th class="info" style="text-align: center">#</th>
			<th class="info" style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
			<th class="danger" style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Opening Deposit'); ?></th>
			<th class="success" style="text-align: center" colspan="2"><span class="label label-info"><?= DateTimeUtils::createFromTimestamp($minClosingTimestamp, DateTimeUtils::FM_VIEW_DATE); ?></span></th>
			<th class="success" style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Closing Deposit'); ?></th>
			<th class="danger" style="text-align: center" colspan="2"><span class="label label-info"><?= $viewToday; ?></span></th>
			<th class="danger" style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Interest'); ?></th>
			<th class="success" style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Result'); ?></th>
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
				<td class="success" style="text-align: right"><?= NumberUtils::format($deposits->closing_interest); ?></td>
				<td class="success" style="text-align: right"><?= NumberUtils::format($deposits->closing_balance); ?></td>
				<td class="danger" style="text-align: center"><?= $deposits->now_diff; ?></td>
				<td class="danger" style="text-align: center"><?= ($deposits->closing_diff - $deposits->now_diff); ?></td>
				<td class="danger" style="text-align: right"><?= NumberUtils::format($deposits->now_interest); ?></td>
				<td class="danger" style="text-align: right"><?= NumberUtils::format($deposits->now_interest + $deposits->opening_balance); ?></td>
				<td class="success" style="text-align: right"><?= NumberUtils::format($deposits->capital); ?></td>
				<td class="success" style="text-align: right"><?= NumberUtils::format($deposits->result_interest); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<th class="info" style="text-align: center">#</th>
			<th class="info"></th>
			<th class="danger" style="text-align: right" colspan="2"><?= NumberUtils::format($sumDeposits['opening_balance']); ?></th>
			<th class="success" style="text-align: right" colspan="2"><?= NumberUtils::format($sumDeposits['closing_interest_unit'], 4); ?></th>
			<th class="success" style="text-align: right"><?= NumberUtils::format($sumDeposits['closing_interest']); ?></th>
			<th class="success" style="text-align: right"><?= NumberUtils::format($sumDeposits['closing_balance']); ?></th>
			<th class="danger" style="text-align: right" colspan="2"><?= NumberUtils::format($sumDeposits['now_interest_unit']); ?></th>
			<th class="danger" style="text-align: right"><?= NumberUtils::format($sumDeposits['now_interest']); ?></th>
			<th class="danger" style="text-align: right"><?= NumberUtils::format($sumDeposits['now_interest'] + $sumDeposits['opening_balance']); ?></th>
			<th class="success" style="text-align: right"><?= NumberUtils::format($sumDeposits['capital']); ?></th>
			<th class="success" style="text-align: right"><?= NumberUtils::format($sumDeposits['result_interest']); ?></th>
		</tr>
	</tbody></table></div>
</div></div></div>

<div class="row">
	<div class="col-md-6"><div class="box">
		<div class="box-header">
			<h3 class="box-title"><?= Yii::t('fin.account', 'High Liquidity'); ?></h3>
		</div>
		<div class="box-body no-padding"><table class="table table-bordered"><tbody>
			<tr>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Opening'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Closing'); ?></th>
				<th style="text-align: center"><?= $viewToday; ?></th>
			</tr>
			<?php foreach($arrTmAtm as $tmAtm): ?><tr class="info">
				<td style="text-align: left"><?= $tmAtm->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($tmAtm->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($tmAtm->closing_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($tmAtm->now_balance); ?></td>
			</tr><?php endforeach; ?>
			<tr class="success">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'TM-ATM'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtm['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtm['closing_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtm['now_balance']); ?></td>
			</tr>
			<tr class="success">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'DPS'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumDeposits['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumDeposits['closing_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumDeposits['opening_balance'] + $sumDeposits['now_interest']); ?></td>
			</tr>
			<tr class="warning">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'TM-ATM-DPS'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtmDeposit['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtmDeposit['closing_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtmDeposit['now_balance']); ?></td>
			</tr>
		</tbody></table></div>
	</div></div>
	<div class="col-md-6"><div class="box">
		<div class="box-header">
			<h3 class="box-title"><?= Yii::t('fin.account', 'Low Liquidity'); ?></h3>
		</div>
		<div class="box-body no-padding"><table class="table table-bordered"><tbody>
			<tr>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Opening'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Closing'); ?></th>
				<th style="text-align: center"><?= $viewToday; ?></th>
			</tr>
			<?php foreach($arrLunchFound as $lunchFound): ?><tr class="info">
				<td style="text-align: left"><?= $lunchFound->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($lunchFound->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($lunchFound->closing_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($lunchFound->now_balance); ?></td>
			</tr><?php endforeach; ?>
			<?php foreach($arrCredit as $credit): ?><tr class="success">
				<td style="text-align: left"><?= $credit->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($credit->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($credit->closing_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($credit->now_balance); ?></td>
			</tr><?php endforeach; ?>
			<?php foreach($arrOtherFound as $otherFound): ?><tr class="warning">
				<td style="text-align: left"><?= $otherFound->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($otherFound->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($otherFound->closing_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($otherFound->now_balance); ?></td>
			</tr><?php endforeach; ?>
			<tr class="danger">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'Total'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTotal['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTotal['closing_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTotal['now_balance']); ?></td>
			</tr>
		</tbody></table></div>
	</div></div>
</div>