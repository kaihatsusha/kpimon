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

<div class="box box-default"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <?php if (!is_null($model->id)): ?><tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('id'); ?></th>
                <td class="info"><?= $model->id; ?></td>
            </tr><?php endif; ?>
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('purchase_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->purchase_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase_type'); ?></th>
                <td class="info"><?= $arrPurchaseType[$model->purchase_type]; ?></td>
            </tr>
            <?php if (!empty($model->sip_date)): ?><tr>
                <th class="warning"><?= $model->getAttributeLabel('sip_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->sip_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
            </tr><?php endif; ?>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase'); ?></th>
                <td class="info"><?= NumberUtils::format($model->purchase); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase_fee_rate'); ?></th>
                <td class="info"><?= NumberUtils::format($model->purchase_fee_rate, 2); ?> %</td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('discount_rate'); ?></th>
                <td class="info"><?= NumberUtils::format($model->discount_rate, 2); ?> %</td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('total_fee_rate'); ?></th>
                <td class="info"><?= NumberUtils::format($model->total_fee_rate, 2); ?> %</td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('purchase_fee'); ?></th>
                <td class="info"><?= NumberUtils::format($model->purchase_fee); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('real_purchase'); ?></th>
                <td class="info"><?= NumberUtils::format($model->real_purchase); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('nav'); ?></th>
                <td class="info"><?= NumberUtils::format($model->nav, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('found_stock'); ?></th>
                <td class="info"><?= NumberUtils::format($model->found_stock, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('transfer_fee'); ?></th>
                <td class="info"><?= NumberUtils::format($model->transfer_fee); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('other_fee'); ?></th>
                <td class="info"><?= NumberUtils::format($model->other_fee); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('investment'); ?></th>
                <td class="info"><?= NumberUtils::format($model->investment); ?></td>
            </tr>
        </table>
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
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    </div></div></div>
<?php ActiveForm::end(); ?></div>