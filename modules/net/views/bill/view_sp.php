<?php
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('net.bill', 'Details of Bill');
    $rowindex = 0;
?>

<?php if($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->id; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('bill_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->bill_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('total'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->total); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= Yii::t('common', 'Price Per Member'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format(NumberUtils::rounds($model->total / $model->member_num, NumberUtils::NUM_CEIL)); ?></span>
            </a></li>
        </ul>
    </div>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Customers List'); ?></h3></div>
    <div class="box-footer"><div class="row"><div id="w0"><div id="w1" class="grid-view col-xs-12"><div class="table-responsive">
        <?php $rowindex = 0; ?>
        <table class="table table-bordered">
            <thead><tr class="warning">
                <th style="text-align: center; width: 70px"><?= Yii::t('fin.grid', 'No.'); ?></th>
                <th style="text-align: center"><?= Yii::t('fin.grid', 'Name'); ?></th>
            </tr></thead>
            <tbody><?php foreach($model->arr_member_list as $memberId): ?>
                <?php
                    $class = MasterValueUtils::getColorRow($rowindex);
                    $memberName = isset($arrNetCustomer[$memberId]) ? $arrNetCustomer[$memberId] : '';
                    $rowindex++;
                ?>
                <tr class="<?= $class; ?>">
                    <td style="vertical-align: middle; text-align: center"><?= $rowindex; ?></td>
                    <td style="vertical-align: middle; text-align: left"><?= $memberName; ?></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
    </div></div></div></div></div>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3></div>
    <div class="box-footer"><div class="row"><div id="w0"><div id="w1" class="grid-view col-xs-12"><div class="table-responsive">
        <?php $rowindex = 0; ?>
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
                    <td style="vertical-align: middle; text-align: left"><?= DateTimeUtils::htmlDateFormat($item->pay_date, DateTimeUtils::FM_VIEW_DATE, DateTimeUtils::FM_DB_DATE, true); ?></td>
                    <td style="vertical-align: middle; text-align: left"><?= $item->description; ?></td>
                </tr>
            <?php endforeach; ?></tbody>
        </table>
    </div></div></div></div></div>
</div></div></div><?php endif; ?>