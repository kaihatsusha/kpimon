<?php
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('jar.distribute', 'Details of Distribution');
    $rowindex = 0;
    $sumUnit = 0;
    $sumShared = 0;
?>

<?php if ($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Basic Info'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('share_id'); ?></th>
                <td class="info"><?= $model->share_id; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('share_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->share_date, DateTimeUtils::FM_VIEW_DATE_WD, DateTimeUtils::FM_DB_DATE, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('share_value'); ?></th>
                <td class="info"><?= NumberUtils::format($model->share_value); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('description'); ?></th>
                <td class="info"><?= $model->description; ?></td>
            </tr>
        </table>
    </div></div></div>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Detail Items'); ?></h3></div>
    <div class="box-body">
        <div class="row"><div id="w1"><div class="grid-view col-xs-12 table-responsive" id="w2">
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
        </div></div></div>
    </div>
</div><?php endif; ?>