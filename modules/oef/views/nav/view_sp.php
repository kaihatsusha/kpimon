<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('oef.nav', 'Details of Nav');
?>

<?php if ($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->nav_id; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('trade_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->trade_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('decide_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->decide_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('nav_value'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->nav_value, 2); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('nav_value_prev'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->nav_value_prev, 2); ?></span>
            </a></li>
        </ul>
    </div>
</div></div></div><?php endif; ?>