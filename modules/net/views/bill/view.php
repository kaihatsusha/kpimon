<?php
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('net.bill', 'Details of Bill');
    $rowindex = 0;
?>

<?php if ($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('id'); ?></th>
                <td class="info"><?= $model->id; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('bill_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->bill_date, DateTimeUtils::FM_VIEW_DATE_WD, DateTimeUtils::FM_DB_DATE, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('total'); ?></th>
                <td class="info"><?= NumberUtils::format($model->total); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('member_list'); ?></th>
                <td class="info"><?= $model->member_list; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= Yii::t('common', 'Number Of Member'); ?></th>
                <td class="info"><?= $model->member_num; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= Yii::t('common', 'Price Per Member'); ?></th>
                <td class="info"><?= NumberUtils::format(NumberUtils::rounds($model->total / $model->member_num, NumberUtils::NUM_CEIL)); ?></td>
            </tr>
        </table>
    </div></div></div>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3></div>
    <div class="box-body">
        <div class="row"><div id="w1"><div class="grid-view col-xs-12 table-responsive" id="w2">
            <table class="table table-bordered">
                <thead><tr class="warning">
                    <th style="text-align: center; width: 70px"><?= Yii::t('fin.grid', 'No.'); ?></th>
                    <th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
                    <th style="text-align: center; width: 120px"><?= Yii::t('fin.grid', 'Price'); ?></th>
                    <th style="text-align: center; width: 160px"><?= Yii::t('fin.grid', 'Pay Date'); ?></th>
                    <th style="text-align: center"><?= Yii::t('fin.grid', 'Description'); ?></th>
                </tr></thead>
                <tbody><?php foreach($arrBillDetail as $i=>$item): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $rowindex++;
                    ?>
                    <tr class="<?= $class; ?>">
                        <td style="vertical-align: middle; text-align: center"><?= $item->item_no; ?></td>
                        <td style="vertical-align: middle; text-align: left"><?= $item->item_name; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= NumberUtils::format($item->price); ?></td>
                        <td style="vertical-align: middle; text-align: left"><?= DateTimeUtils::htmlDateFormat($item->pay_date, DateTimeUtils::FM_VIEW_DATE_WD, DateTimeUtils::FM_DB_DATE, true); ?></td>
                        <td style="vertical-align: middle; text-align: left"><?= $item->description; ?></td>
                    </tr>
                <?php endforeach; ?></tbody>
            </table>
        </div></div></div>
    </div>
</div><?php endif; ?>