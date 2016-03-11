<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('jar.payment', 'Details of Payment');
?>

<?php if($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->id; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('account_source'); ?>
                <span class="pull-right"><?= isset($arrAccount[$model->account_source]) ? $arrAccount[$model->account_source] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('account_target'); ?>
                <span class="pull-right"><?= isset($arrAccount[$model->account_target]) ? $arrAccount[$model->account_target] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_value'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->entry_value); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('description'); ?>
                <span class="pull-right"><?= $model->description; ?></span>
            </a></li>
        </ul>
    </div>
</div></div></div><?php endif; ?>