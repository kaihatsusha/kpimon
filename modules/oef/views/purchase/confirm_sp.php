<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('oef.nav', 'Create Purchase');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('oef.nav', 'Edit Purchase');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('oef.nav', 'Copy Purchase');
    }
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <?php if (!is_null($model->id)): ?><li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->id; ?></span>
            </a></li><?php endif; ?>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('purchase_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->purchase_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('purchase_type'); ?>
                <span class="pull-right"><?= $arrPurchaseType[$model->purchase_type]; ?></span>
            </a></li>
            <?php if (!empty($model->sip_date)): ?><li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('sip_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->sip_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li><?php endif; ?>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('purchase'); ?>
                <span class="pull-right badge bg-blue"><?= NumberUtils::format($model->purchase); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('purchase_fee_rate'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->purchase_fee_rate, 2); ?> %</span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('discount_rate'); ?>
                <span class="pull-right badge bg-orange"><?= NumberUtils::format($model->discount_rate, 2); ?> %</span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('total_fee_rate'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->total_fee_rate, 2); ?> %</span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('purchase_fee'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->purchase_fee); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('real_purchase'); ?>
                <span class="pull-right badge bg-blue"><?= NumberUtils::format($model->real_purchase); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('nav'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->nav, 2); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('found_stock'); ?>
                <span class="pull-right badge bg-green"><?= NumberUtils::format($model->found_stock, 2); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('transfer_fee'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->transfer_fee); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('other_fee'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->other_fee); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('investment'); ?>
                <span class="pull-right badge bg-blue"><?= NumberUtils::format($model->investment); ?></span>
            </a></li>
        </ul>
        <div style="display: none">
            <?= $form->field($model, 'purchase_date')->hiddenInput(); ?>
            <?= $form->field($model, 'purchase_type')->hiddenInput(); ?>
            <?= $form->field($model, 'sip_date')->hiddenInput(); ?>
            <?= $form->field($model, 'nav')->hiddenInput(); ?>
            <?= $form->field($model, 'purchase')->hiddenInput(); ?>
            <?= $form->field($model, 'transfer_fee')->hiddenInput(); ?>
            <?= $form->field($model, 'other_fee')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?></div></div></div>