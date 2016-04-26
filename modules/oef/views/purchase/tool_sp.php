<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('oef.purchase', 'Create Purchase');
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div class="box-footer" id="oefPurchaseToolForm"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <?= $form->field($model, 'purchase_type')->dropDownList($arrPurchaseType, ['prompt'=>'']); ?>
        <?= $form->field($model, 'nav')->textInput(['type'=>'number', 'step'=>'any']); ?>
        <?= $form->field($model, 'purchase')->textInput(['type'=>'number']); ?>
        <div class="form-group">
            <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default btn-lg btn-block']); ?>
            <?= Html::submitButton(Yii::t('button', 'Calculate'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
        </div>
    <?php ActiveForm::end(); ?></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
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
        </ul>
    </div>
</div></div></div>