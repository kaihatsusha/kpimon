<?php
	use app\components\DateTimeUtils;
	use app\components\NumberUtils;
	
	$this->title = Yii::t('fin.account', 'Personal Accounts List');
	// for render
	$rowindex = 0;
	$viewToday = DateTimeUtils::formatNow(DateTimeUtils::FM_VIEW_DATE);
?>
<div class="row">
	<div class="col-md-12"><div class="box">
		<div class="box-header with-border">
			<h3 class="box-title"><?= Yii::t('fin.account', 'Time Deposits'); ?></h3>
		</div>
		<div class="box-body"><div id="timeDepositsBoxgroup" class="box-group">
			<div class="panel box box-primary">
				<div class="box-header with-border">
					<h4 class="box-title">
						<a href="#timeDepositsOpening" data-parent="#timeDepositsBoxgroup" data-toggle="collapse">
							<?= Yii::t('fin.grid', 'Opening Deposit'); ?>
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse in" id="timeDepositsOpening">
					<div class="box-body"><div class="table-responsive"><table class="table table-bordered no-margin">
						<tr>
							<th style="text-align: center">#</th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Date'); ?></th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Deposit'); ?></th>
                        </tr>
                        <?php $rowindex = 0; ?>
                        <?php foreach($arrDeposits as $deposits): ?>
                        	<?php $rowindex++; ?>
                        	<tr>
								<td style="text-align: center"><?= $rowindex; ?></td>
								<td style="text-align: left"><?= $deposits->account_name; ?></td>
								<td style="text-align: center"><?= DateTimeUtils::formatDateTimeFromDB($deposits->opening_date, DateTimeUtils::FM_VIEW_DATE); ?></td>
								<td style="text-align: right"><?= NumberUtils::format($deposits->opening_balance); ?></td>
							</tr>
                        <?php endforeach; ?>
                        <tr>
							<th style="text-align: right" colspan="4"><?= NumberUtils::format($sumDeposits['opening_balance']); ?></th>
                        </tr>
					</table></div></div>
				</div>
			</div>
			<div class="panel box box-danger">
				<div class="box-header with-border">
					<h4 class="box-title">
						<a href="#timeDepositsClosing" data-parent="#timeDepositsBoxgroup" data-toggle="collapse">
							<?= Yii::t('fin.grid', 'Closing Deposit'); ?>
						</a>
					</h4>
					<div class="pull-right"><span class="label label-danger"><?= DateTimeUtils::createFromTimestamp($minClosingTimestamp, DateTimeUtils::FM_VIEW_DATE); ?></span></div>
				</div>
				<div class="panel-collapse collapse" id="timeDepositsClosing">
					<div class="box-body"><div class="table-responsive"><table class="table table-bordered no-margin">
						<tr>
							<th style="text-align: center">#</th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Date'); ?></th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Deposit'); ?></th>
                        </tr>
                        <?php $rowindex = 0; ?>
                        <?php foreach($arrDeposits as $deposits): ?>
                        	<?php $rowindex++; ?>
                        	<tr>
								<td style="text-align: center; vertical-align: middle;"><?= $rowindex; ?></td>
								<td style="text-align: right; vertical-align: middle;">
									<?= NumberUtils::format($deposits->closing_interest_unit); ?><br/>
									<?= $deposits->account_name; ?>
								</td>
								<td style="text-align: right; vertical-align: middle;">
									<?= $deposits->closing_diff; ?> <?= Yii::t('fin.grid', '(days)'); ?><br/>
									<?= DateTimeUtils::formatDateTimeFromDB($deposits->closing_date, DateTimeUtils::FM_VIEW_DATE); ?></td>
								<td style="text-align: right; vertical-align: middle;">
									<?= NumberUtils::format($deposits->closing_interest); ?><br/>
									<?= NumberUtils::format($deposits->closing_balance); ?>
								</td>
							</tr>
                        <?php endforeach; ?>
                        <tr>
							<th style="text-align: right" colspan="2"><?= NumberUtils::format($sumDeposits['closing_interest_unit']); ?></th>
							<th style="text-align: right"><?= NumberUtils::format($sumDeposits['closing_interest']); ?></th>
							<th style="text-align: right"><?= NumberUtils::format($sumDeposits['closing_balance']); ?></th>
                        </tr>
					</table></div></div>
				</div>
			</div>
			<div class="panel box box-success">
				<div class="box-header with-border">
					<h4 class="box-title">
						<a href="#timeDepositsInterest" data-parent="#timeDepositsBoxgroup" data-toggle="collapse">
							<?= Yii::t('fin.grid', 'Interest'); ?>
						</a>
					</h4>
					<div class="pull-right"><span class="label label-success"><?= $viewToday; ?></span></div>
				</div>
				<div class="panel-collapse collapse" id="timeDepositsInterest">
					<div class="box-body"><div class="table-responsive"><table class="table table-bordered no-margin">
						<tr>
							<th style="text-align: center">#</th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Days'); ?></th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Interest'); ?></th>
                        </tr>
                        <?php $rowindex = 0; ?>
                        <?php foreach($arrDeposits as $deposits): ?>
                        	<?php $rowindex++; ?>
                        	<tr>
								<td style="text-align: center; vertical-align: middle;"><?= $rowindex; ?></td>
								<td style="text-align: right; vertical-align: middle;">
									<?= NumberUtils::format($deposits->now_interest_unit); ?><br/>
									<?= $deposits->account_name; ?>
								</td>
								<td style="text-align: right; vertical-align: middle;">
									<?= Yii::t('fin.grid', 'Left'); ?> : <?= $deposits->now_diff; ?><br/>
									<?= Yii::t('fin.grid', 'Remain'); ?> : <?= ($deposits->closing_diff - $deposits->now_diff); ?>
								</td>
								<td style="text-align: right; vertical-align: middle;">
									<?= NumberUtils::format($deposits->now_interest); ?><br/>
									<?= NumberUtils::format($deposits->now_interest + $deposits->opening_balance); ?>
								</td>
							</tr>
                        <?php endforeach; ?>
                        <tr>
							<th style="text-align: right" colspan="2"><?= NumberUtils::format($sumDeposits['now_interest_unit']); ?></th>
							<th style="text-align: right"><?= NumberUtils::format($sumDeposits['now_interest']); ?></th>
							<th style="text-align: right"><?= NumberUtils::format($sumDeposits['now_interest'] + $sumDeposits['opening_balance']); ?></th>
                        </tr>
					</table></div></div>
				</div>
			</div>
			<div class="panel box box-info">
				<div class="box-header with-border">
					<h4 class="box-title">
						<a href="#timeDepositsResult" data-parent="#timeDepositsBoxgroup" data-toggle="collapse">
							<?= Yii::t('fin.grid', 'Result'); ?>
						</a>
					</h4>
				</div>
				<div class="panel-collapse collapse" id="timeDepositsResult">
					<div class="box-body"><div class="table-responsive"><table class="table table-bordered no-margin">
						<tr>
							<th style="text-align: center">#</th>
							<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
							<th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Result'); ?></th>
                        </tr>
                        <?php $rowindex = 0; ?>
                        <?php foreach($arrDeposits as $deposits): ?>
                        	<?php $rowindex++; ?>
                        	<tr>
								<td style="text-align: center"><?= $rowindex; ?></td>
								<td style="text-align: left"><?= $deposits->account_name; ?></td>
								<td style="text-align: right"><?= NumberUtils::format($deposits->capital); ?></td>
								<td style="text-align: right"><?= NumberUtils::format($deposits->result_interest); ?></td>
							</tr>
                        <?php endforeach; ?>
                        <tr>
							<th style="text-align: right" colspan="3"><?= NumberUtils::format($sumDeposits['capital']); ?></th>
							<th style="text-align: right"><?= NumberUtils::format($sumDeposits['result_interest']); ?></th>
                        </tr>
					</table></div></div>
				</div>
			</div>
		</div></div>
	</div></div>
</div>

<div class="row">
	<div class="col-md-12"><div class="box">
		<div class="box-header with-border">
			<h3 class="box-title"><?= Yii::t('fin.account', 'High Liquidity'); ?></h3>
		</div>
		<div class="box-body"><div class="table-responsive"><table class="table table-bordered no-margin">
			<tr>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Opening'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Closing'); ?></th>
			</tr>
			<?php foreach($arrTmAtm as $tmAtm): ?><tr class="info">
				<td style="text-align: left"><?= $tmAtm->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($tmAtm->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($tmAtm->closing_balance); ?></td>
			</tr><?php endforeach; ?>
			<tr class="success">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'TM-ATM'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtm['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtm['closing_balance']); ?></td>
			</tr>
			<tr class="success">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'DPS'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumDeposits['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumDeposits['closing_balance']); ?></td>
			</tr>
			<tr class="warning">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'TM-ATM-DPS'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtmDeposit['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTmAtmDeposit['closing_balance']); ?></td>
			</tr>
		</table></div></div>
	</div></div>
	<div class="col-md-12"><div class="box">
		<div class="box-header with-border">
			<h3 class="box-title"><?= Yii::t('fin.account', 'Low Liquidity'); ?></h3>
		</div>
		<div class="box-body"><div class="table-responsive"><table class="table table-bordered no-margin">
			<tr>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Opening'); ?></th>
				<th style="text-align: center"><?= Yii::t('fin.grid', 'Closing'); ?></th>
			</tr>
			<?php foreach($arrLunchFound as $lunchFound): ?><tr class="info">
				<td style="text-align: left"><?= $lunchFound->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($lunchFound->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($lunchFound->closing_balance); ?></td>
			</tr><?php endforeach; ?>
			<?php foreach($arrCredit as $credit): ?><tr class="success">
				<td style="text-align: left"><?= $credit->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($credit->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($credit->closing_balance); ?></td>
			</tr><?php endforeach; ?>
			<?php foreach($arrOtherFound as $otherFound): ?><tr class="warning">
				<td style="text-align: left"><?= $otherFound->account_name; ?></td>
				<td style="text-align: right"><?= NumberUtils::format($otherFound->opening_balance); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($otherFound->closing_balance); ?></td>
			</tr><?php endforeach; ?>
			<tr class="danger">
				<td style="text-align: left"><?= Yii::t('fin.grid', 'Total'); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTotal['opening_balance']); ?></td>
				<td style="text-align: right"><?= NumberUtils::format($sumTotal['closing_balance']); ?></td>
			</tr>
		</table></div></div>
	</div></div>
</div>

