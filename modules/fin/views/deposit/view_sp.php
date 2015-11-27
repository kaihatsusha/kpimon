<?php
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('fin.payment', 'Details of Fixed Deposit');

    $openingDate = DateTimeUtils::getDateFromDB($model->opening_date);
    $closingDate = DateTimeUtils::getDateFromDB($model->closing_date);
    $dateDiff = $closingDate->diff($openingDate);
?>

<?php if($model): ?><div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('saving_account'); ?>
                <span class="pull-right"><?= isset($arrSavingAccount[$model->saving_account]) ? $arrSavingAccount[$model->saving_account] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_rate'); ?>
                <span class="pull-right badge bg-green"><?= NumberUtils::format($model->interest_rate, 4); ?> %</span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_unit'); ?>
                <span class="pull-right badge bg-green"><?= NumberUtils::format($model->interest_unit, 4); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('opening_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->opening_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('closing_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->closing_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_days'); ?>
                <span class="pull-right"><?= $dateDiff->days; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_add'); ?>
                <span class="pull-right badge bg-aqua"><?= NumberUtils::format($model->interest_add); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_value'); ?>
                <?php if ($model->add_flag == MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING): ?>
                    <span class="pull-right badge bg-aqua"><?= NumberUtils::format($model->entry_value); ?></span>
                <?php else: ?>
                    <span class="pull-right badge bg-red"><?= NumberUtils::format($model->entry_value); ?></span>
                <?php endif; ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('add_flag'); ?>
                <span class="pull-right"><?= isset($arrTimedepositTrantype[$model->add_flag]) ? $arrTimedepositTrantype[$model->add_flag] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('current_assets'); ?>
                <span class="pull-right"><?= isset($arrCurrentAssets[$model->current_assets]) ? $arrCurrentAssets[$model->current_assets] : ''; ?></span>
            </a></li>
        </ul>
    </div>
</div></div></div><?php endif; ?>
