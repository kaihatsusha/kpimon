<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('fin.interest', 'Details of Interest Unit');
?>

<?php if($model): ?><div class="box box-default">
    <?php
        $startDate = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE);

        $endDateHtml = null;
        $endDate = null;
        if (empty($model->end_date)) {
            $endDate = DateTimeUtils::getNow();
            $endDateHtml = '<span class="text-fuchsia">' . $endDate->format(DateTimeUtils::FM_VIEW_DATE_WD) . '</span>';
        } else {
            $endDate = DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE);
            $endDateHtml = DateTimeUtils::htmlDateFormatFromDB($model->end_date, DateTimeUtils::FM_VIEW_DATE_WD, true);
        }
        $interval = $endDate->diff($startDate);
        $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;
    ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('id'); ?></th>
                <td class="info"><?= $model->id; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('start_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->start_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('end_date'); ?></th>
                <td class="info"><?= $endDateHtml; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= Yii::t('fin.grid', 'Days'); ?></th>
                <td class="info"><?= $days; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('interest_unit'); ?></th>
                <td class="info"><?= NumberUtils::format($model->interest_unit, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= Yii::t('fin.grid', 'Interest'); ?></th>
                <td class="info"><?= NumberUtils::format($model->interest_unit * $days, 2); ?></td>
            </tr>
        </table>
    </div></div></div>
</div><?php endif; ?>
