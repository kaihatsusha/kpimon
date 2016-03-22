<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use app\modules\oef\views\PurchaseAssetSP;
    use kartik\datetime\DateTimePicker;

    // css & js
    PurchaseAssetSP::register($this);

    $this->title = Yii::t('oef.purchase', 'Create Purchase');
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div class="box-footer" id="oefPurchaseCreateForm"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <?= $form->field($model, 'purchase_date')->widget(DateTimePicker::className(), ['type'=>1,
            'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
        ]); ?>
        <?= $form->field($model, 'purchase_type')->dropDownList($arrPurchaseType, ['prompt'=>'']); ?>
        <?= $form->field($model, 'sip_date')->widget(DateTimePicker::className(), ['type'=>1, 'options' => ['data-backup'=>$model->sip_date],
            'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
        ]); ?>
        <?= $form->field($model, 'nav')->textInput(['type'=>'number', 'step'=>'any']); ?>
        <?= $form->field($model, 'purchase')->textInput(['type'=>'number']); ?>
        <?= $form->field($model, 'transfer_fee')->textInput(['type'=>'number']); ?>
        <?= $form->field($model, 'other_fee')->textInput(['type'=>'number']); ?>
        <div class="form-group">
            <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default btn-lg btn-block']); ?>
            <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
        </div>
    <?php ActiveForm::end(); ?></div>
</div></div></div>