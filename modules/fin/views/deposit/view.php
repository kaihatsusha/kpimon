<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('fin.payment', 'Details of Fixed Deposit');

    $openingDate = DateTimeUtils::getDateFromDB($model->opening_date);
    $closingDate = DateTimeUtils::getDateFromDB($model->closing_date);
    $dateDiff = $closingDate->diff($openingDate);
?>

<?php if($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('transactions_id'); ?></th>
                <td class="info"><?= $model->transactions_id; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('saving_account'); ?></th>
                <td class="info"><?= isset($arrSavingAccount[$model->saving_account]) ? $arrSavingAccount[$model->saving_account] : ''; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('interest_rate'); ?></th>
                <td class="info"><?= NumberUtils::format($model->interest_rate, 4); ?> %</td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('interest_unit'); ?></th>
                <td class="info"><?= NumberUtils::format($model->interest_unit, 4); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('opening_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->opening_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('closing_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->closing_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('interest_days'); ?></th>
                <td class="info"><?= $dateDiff->days; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('interest_add'); ?></th>
                <td class="info"><?= NumberUtils::format($model->interest_add); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('entry_value'); ?></th>
                <td class="info"><?= NumberUtils::format($model->entry_value); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('add_flag'); ?></th>
                <td class="info"><?= isset($arrTimedepositTrantype[$model->add_flag]) ? $arrTimedepositTrantype[$model->add_flag] : ''; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('current_assets'); ?></th>
                <td class="info"><?= isset($arrCurrentAssets[$model->current_assets]) ? $arrCurrentAssets[$model->current_assets] : ''; ?></td>
            </tr>
        </table>
    </div></div></div>
</div><?php endif; ?>