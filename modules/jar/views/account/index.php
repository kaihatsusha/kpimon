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
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Useable'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Real'); ?></th>
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
                <td style="text-align: right"><?= NumberUtils::getIncDecNumber($account->useable_balance, $numberHtmlConfig); ?></td>
                <td style="text-align: right"><?= NumberUtils::getIncDecNumber($account->real_balance, $numberHtmlConfig); ?></td>
                <td style="text-align: left"><?= $account->description; ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="warning">
            <th colspan="2" style="text-align: center"><?= Yii::t('fin.grid', 'Total'); ?></th>
            <th style="text-align: right"><?= $sumAccountValue['share']; ?> %</th>
            <th style="text-align: right"><?= NumberUtils::getIncDecNumber($sumAccountValue['useable_balance'], $numberHtmlConfig); ?></th>
            <th style="text-align: right"><?= NumberUtils::getIncDecNumber($sumAccountValue['real_balance'], $numberHtmlConfig); ?></th>
            <th>
                <?= $tempAccount->account_name; ?>
                <?= NumberUtils::getIncDecNumber($tempAccount->useable_balance, $numberHtmlConfig); ?> +
                <?= NumberUtils::getIncDecNumber($sumAccountValue['useable_balance'], $numberHtmlConfig); ?> =
                <?= NumberUtils::getIncDecNumber($tempAccount->useable_balance + $sumAccountValue['useable_balance'], $numberHtmlConfig); ?>
            </th>
        </tr>
    </tbody></table></div>
</div></div></div>