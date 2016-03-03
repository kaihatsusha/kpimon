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
            <th style="text-align: center"><?= Yii::t('fin.grid', 'No.'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'JAR'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Share'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Credit'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Debit'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Balance'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Description'); ?></th>
        </tr>
        <?php foreach($arrAccount as $account): ?>
            <?php
                $rowClass = $account->status == MasterValueUtils::MV_JAR_ACCOUNT_STATUS_ON ? MasterValueUtils::getColorRow($rowindex) : 'danger';
                $rowindex++;
            ?>
            <tr class="<?= $rowClass; ?>">
                <td style="text-align: center"><?= $rowindex; ?></td>
                <td style="text-align: left"><?= $account->account_name; ?></td>
                <td style="text-align: right"><?= $account->share_unit; ?> %</td>
                <td style="text-align: right"><?= NumberUtils::format($account->credit); ?></td>
                <td style="text-align: right"><?= NumberUtils::format($account->debit); ?></td>
                <td style="text-align: right"><?= NumberUtils::getIncDecNumber($account->credit - $account->debit, $numberHtmlConfig); ?></td>
                <td style="text-align: left"><?= $account->description; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="warning">
            <th colspan="2" style="text-align: center"><?= Yii::t('fin.grid', 'Total'); ?></th>
            <th style="text-align: right"><?= $sumAccountValue['share']; ?> %</th>
            <th style="text-align: right"><?= NumberUtils::format($sumAccountValue['credit']); ?></th>
            <th style="text-align: right"><?= NumberUtils::format($sumAccountValue['debit']); ?></th>
            <th style="text-align: right"><?= NumberUtils::getIncDecNumber($sumAccountValue['credit'] - $sumAccountValue['debit'], $numberHtmlConfig); ?></th>
            <th></th>
        </tr>
    </tbody></table></div>
</div></div></div>