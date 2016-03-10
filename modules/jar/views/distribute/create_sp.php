<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use kartik\datetime\DateTimePicker;

    $this->title = Yii::t('jar.distribute', 'Create Distribute');
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div class="box-footer" id="jarDistributeCreateForm">
        <?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
            <?= $form->field($model, 'share_date')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($model, 'share_value')->textInput(['type'=>'number']); ?>
            <?= $form->field($model, 'description')->textInput(); ?>
            <div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default btn-lg btn-block']); ?>
                <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div></div></div>