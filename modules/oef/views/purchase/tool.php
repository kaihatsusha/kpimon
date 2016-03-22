<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $this->title = Yii::t('oef.purchase', 'Create Purchase');
?>

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div id="oefPurchaseToolForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($model, 'purchase_type')->dropDownList($arrPurchaseType, ['prompt'=>'']); ?>
            <?= $form->field($model, 'nav')->textInput(['type'=>'number', 'step'=>'any']); ?>
            <?= $form->field($model, 'purchase')->textInput(['type'=>'number']); ?>
            <div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
                <?= Html::submitButton(Yii::t('button', 'Calculate'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
        <div class="row"><div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('purchase'); ?></th>
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
            </table>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div>