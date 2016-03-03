<?php
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('jar.account', 'Jars List');
    // for render
    $rowindex = 0;
    $numberHtmlConfig = ['template'=>'<span class="{color}">{number}</span>', 'incColor'=>'text-blue', 'decColor'=>'text-red'];
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header">
        <h3 class="box-title"><?= Yii::t('jar.account', 'JARS'); ?></h3>
    </div>
    <div class="box-body table-responsive no-padding"><table class="table table-bordered"><tbody>
        <tr class="warning">
            <th style="text-align: center; vertical-align: middle"><?= Yii::t('fin.grid', 'JAR'); ?></th>
            <th style="text-align: center; vertical-align: middle"><?= Yii::t('fin.grid', 'Credit'); ?><br/><?= Yii::t('fin.grid', 'Debit'); ?></th>
            <th style="text-align: center; vertical-align: middle"><?= Yii::t('fin.grid', 'Share'); ?><br/><?= Yii::t('fin.grid', 'Balance'); ?></th>
            <th style="text-align: center; vertical-align: middle"><?= Yii::t('fin.grid', 'Description'); ?></th>
        </tr>
        <?php foreach($arrAccount as $account): ?>
            <?php
                $rowClass = $account->status == MasterValueUtils::MV_JAR_ACCOUNT_STATUS_ON ? MasterValueUtils::getColorRow($rowindex) : 'danger';
                $rowindex++;
            ?>
            <tr class="<?= $rowClass; ?>">
                <td style="text-align: left; vertical-align: middle"><?= $account->account_name; ?></td>
                <td style="text-align: right; vertical-align: middle"><?= NumberUtils::format($account->credit); ?><br/><?= NumberUtils::format($account->debit); ?></td>
                <td style="text-align: right; vertical-align: middle"><?= $account->share_unit; ?> %<br/><?= NumberUtils::getIncDecNumber($account->credit - $account->debit, $numberHtmlConfig); ?></td>
                <td style="text-align: left; vertical-align: middle"><?= $account->description; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="warning">
            <th style="text-align: center; vertical-align: middle"><?= Yii::t('fin.grid', 'Total'); ?></th>
            <th style="text-align: right; vertical-align: middle"><?= NumberUtils::format($sumAccountValue['credit']); ?><br/><?= NumberUtils::format($sumAccountValue['debit']); ?></th>
            <th style="text-align: right; vertical-align: middle"><?= $sumAccountValue['share']; ?> %<br/><?= NumberUtils::getIncDecNumber($sumAccountValue['credit'] - $sumAccountValue['debit'], $numberHtmlConfig); ?></th>
            <th></th>
        </tr>
    </tbody></table></div>
</div></div></div>