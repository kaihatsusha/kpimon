<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\modules\oef\views\PurchaseAsset;
    use kartik\datetime\DateTimePicker;

    // css & js
    PurchaseAsset::register($this);

    $this->title = Yii::t('oef.purchase', 'Create Purchase');
?>

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div id="oefPurchaseCreateForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($model, 'purchase_date')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($model, 'purchase_type')->dropDownList($arrPurchaseType, ['prompt'=>'']); ?>
            <?= $form->field($model, 'sip_date')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($model, 'nav')->textInput(['type'=>'number', 'step'=>'any']); ?>
            <?= $form->field($model, 'purchase')->textInput(['type'=>'number']); ?>
            <?= $form->field($model, 'transfer_fee')->textInput(['type'=>'number']); ?>
            <div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
                <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div>