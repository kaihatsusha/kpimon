<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('fin.interest', 'Details of Interest Unit');
?>

<?php if($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <?php
        $startDate = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE);

        $endDateHtml = null;
        $endDate = null;
        if (empty($model->end_date)) {
            $endDate = DateTimeUtils::getNow();
            $endDateHtml = '<span class="text-fuchsia pull-right">' . $endDate->format(DateTimeUtils::FM_VIEW_DATE_WD) . '</span>';
        } else {
            $endDate = DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE);
            $endDateHtml = DateTimeUtils::htmlDateFormatFromDB($model->end_date, DateTimeUtils::FM_VIEW_DATE_WD, true);
        }
        $interval = $endDate->diff($startDate);
        $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;
    ?>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->id; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('start_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->start_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('end_date'); ?>
                <?= $endDateHtml; ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= Yii::t('fin.grid', 'Days'); ?>
                <span class="pull-right"><?= $days; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_unit'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->interest_unit, 2); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= Yii::t('fin.grid', 'Interest'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->interest_unit * $days, 2); ?></span>
            </a></li>
        </ul>
    </div>
</div></div></div><?php endif; ?>
