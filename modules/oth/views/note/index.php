<?php
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;
    use app\components\StringUtils;

    $this->title = Yii::t('oth.note', 'Notes List');
    $currentDate = DateTimeUtils::getNow();
    $currentDateStr = DateTimeUtils::htmlDateFormat($currentDate, DateTimeUtils::FM_VIEW_DATE, null, true);

    $rowindex = 0;
?>

<div class="row"><div class="col-md-12"><div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::t('oth.note', 'Items'); ?></h3>
    </div>
    <div class="box-body table-responsive no-padding"><table class="table table-bordered"><tbody>
        <tr class="warning">
            <th style="text-align: center"><?= Yii::t('fin.grid', 'No.'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Note'); ?></th>
            <th style="text-align: center"><?= Yii::t('fin.grid', 'Start Date'); ?></th>
            <th style="text-align: center" colspan="2"><?= $currentDateStr; ?></th>
            <th style="text-align: center" colspan="2"><?= Yii::t('fin.grid', 'Cost'); ?></th>
        </tr>
        <?php foreach($arrModel as $model): ?>
            <?php
            $rowClass = MasterValueUtils::getColorRow($rowindex);
            $rowindex++;

            $startDateStr = '';
            $diffDays = '';
            $diffFull = '';
            if (!is_null($model->start_date)) {
                $startDate = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATETIME);
                $startDateStr = DateTimeUtils::htmlDateTimeFormatFromDB($model->start_date, DateTimeUtils::FM_VIEW_DATE, true);
                $interval = $currentDate->diff($startDate);
                $diffDays = $interval->days;
                $diffFull = $interval->format('%Y-%M-%D %H:%I:%S');
            }

            $costAll = '';
            $costDay = '';
            if (!is_null($model->costs)) {
                $costAll = NumberUtils::format($model->costs);
                if ($diffDays > 0) {
                    $costDay = NumberUtils::format($model->costs / $diffDays, 2);
                }
            }
            ?>
            <tr class="<?= $rowClass; ?>">
                <td style="vertical-align: middle; text-align: center; width: 80px"><?= $rowindex; ?></td>
                <td style="vertical-align: middle; text-align: left"><?= $model->name; ?></td>
                <td style="vertical-align: middle; text-align: center; width: 100px"><?= $startDateStr; ?></td>
                <td style="vertical-align: middle; text-align: center; width: 140px"><?= $diffFull; ?></td>
                <td style="vertical-align: middle; text-align: right"><?= $diffDays; ?></td>
                <td style="vertical-align: middle; text-align: right"><?= $costAll; ?></td>
                <td style="vertical-align: middle; text-align: right"><?= $costDay; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody></table></div>
</div></div></div>
