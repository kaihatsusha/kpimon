<?php
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('jar.distribute', 'Details of Distribution');
    $rowindex = 0;
    $sumUnit = 0;
    $sumShared = 0;
?>

<?php if($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('share_id'); ?>
                <span class="pull-right"><?= $model->share_id; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('share_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->share_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('share_value'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->share_value); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('description'); ?>
                <span class="pull-right"><?= $model->description; ?></span>
            </a></li>
        </ul>
    </div>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3></div>
    <div class="box-footer"><div class="row"><div id="w0"><div id="w1" class="grid-view col-xs-12"><div class="table-responsive">
        <table class="table table-bordered">
            <thead><tr class="warning">
                <th style="text-align: center"><?= Yii::t('fin.grid', 'No.'); ?></th>
                <th style="text-align: center"><?= Yii::t('fin.grid', 'Account Name'); ?></th>
                <th style="text-align: center"><?= Yii::t('fin.grid', 'Unit'); ?></th>
                <th style="text-align: center"><?= Yii::t('fin.grid', 'Shared'); ?></th>
            </tr></thead>
            <tbody>
                <?php foreach($arrShareDetail as $item): ?>
                    <?php
                        $class = MasterValueUtils::getColorRow($rowindex);
                        $sumUnit += $item->share_unit;
                        $sumShared += $item->share_value;
                        $rowindex++;
                    ?>
                    <tr class="<?= $class; ?>">
                        <td style="vertical-align: middle; text-align: center"><?= $rowindex; ?></td>
                        <td style="vertical-align: middle; text-align: left"><?= $item->account_name; ?></td>
                        <td style="vertical-align: middle; text-align: right"><?= $item->share_unit; ?> %</td>
                        <td style="vertical-align: middle; text-align: right"><?= NumberUtils::format($item->share_value); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="warning">
                    <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Total'); ?></th>
                    <th style="text-align: right"><?= $sumUnit; ?> %</th>
                    <th style="text-align: right"><?= NumberUtils::format($sumShared); ?></th>
                </tr>
            </tbody>
        </table>
    </div></div></div></div></div>
</div></div></div><?php endif; ?>