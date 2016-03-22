<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('oef.purchase', 'Details of Purchase');
?>

<?php if ($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('id'); ?></th>
                <td class="info"><?= $model->id; ?></td>
            </tr>
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('purchase_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->purchase_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase_type'); ?></th>
                <td class="info"><?= $arrPurchaseType[$model->purchase_type]; ?></td>
            </tr>
            <?php if (!empty($model->sip_date)): ?><tr>
                <th class="warning"><?= $model->getAttributeLabel('sip_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->sip_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr><?php endif; ?>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase'); ?></th>
                <td class="info"><?= NumberUtils::format($model->purchase); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase_fee_rate'); ?></th>
                <td class="info"><?= NumberUtils::format($model->purchase_fee_rate, 2); ?> %</td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('discount_rate'); ?></th>
                <td class="info"><?= NumberUtils::format($model->discount_rate, 2); ?> %</td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('total_fee_rate'); ?></th>
                <td class="info"><?= NumberUtils::format($model->total_fee_rate, 2); ?> %</td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase_fee'); ?></th>
                <td class="info"><?= NumberUtils::format($model->purchase_fee); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('real_purchase'); ?></th>
                <td class="info"><?= NumberUtils::format($model->real_purchase); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('nav'); ?></th>
                <td class="info"><?= NumberUtils::format($model->nav, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('found_stock'); ?></th>
                <td class="info"><?= NumberUtils::format($model->found_stock, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('found_stock_sold'); ?></th>
                <td class="info"><?= NumberUtils::format($model->found_stock_sold, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('transfer_fee'); ?></th>
                <td class="info"><?= NumberUtils::format($model->transfer_fee); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('other_fee'); ?></th>
                <td class="info"><?= NumberUtils::format($model->other_fee); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('investment'); ?></th>
                <td class="info"><?= NumberUtils::format($model->investment); ?></td>
            </tr>
        </table>
    </div></div></div>
</div><?php endif; ?>