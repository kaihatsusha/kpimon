<?php
	$this->title = Yii::t('fin.account', 'Personal Accounts List');
	// for render
	$rowindex = 0;
?>

<div class="row"><div class="col-xs-12"><div class="box">
	<div class="box-header">
		<h3 class="box-title"><?= Yii::t('fin.account', 'Time Deposits'); ?></h3>
	</div>
	<div class="box-body table-responsive no-padding"><table class="table table-bordered"><tbody>
		<tr class="warning">
			<th>#</th>
			<th><?= Yii::t('fin.account', 'Name'); ?></th>
		</tr>
		<?php foreach($arrDeposits as $deposits): ?>
			<?php
				$rowindex++;
			?>
			<tr class="info">
				<td><?= $rowindex; ?></td>
				<td><?= $deposits->account_name; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody></table></div>
</div></div></div>