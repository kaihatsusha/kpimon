<?php
    use app\components\DateTimeUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('net.payment', 'Details of Payment');
?>

<?php if($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                    <?= $model->getAttributeLabel('customer_id'); ?>
                <span class="pull-right"><?= isset($arrNetCustomer[$model->customer_id]) ? $arrNetCustomer[$model->customer_id] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <?php if($model->credit > 0): ?><li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('credit'); ?>
                <span class="pull-right badge bg-aqua"><?= NumberUtils::format($model->credit); ?></span>
            </a></li><?php endif; ?>
            <?php if($model->debit > 0): ?><li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('debit'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->debit); ?></span>
            </a></li><?php endif; ?>
        </ul>
    </div>
</div></div></div><?php endif; ?>