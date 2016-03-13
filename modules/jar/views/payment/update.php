<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use kartik\datetime\DateTimePicker;

    $this->title = Yii::t('jar.payment', 'Edit Payment');
?>

<?php if ($model): ?><div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Input Values'); ?></h3></div>
    <div id="jarPaymentUpdateForm" class="box-body"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
        <div class="row"><div class="col-md-12">
            <?= $form->field($model, 'entry_date')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($model, 'account_source', ['inputOptions'=>['disabled'=>'disabled', 'name'=>'']])->dropDownList($arrAccount, ['prompt'=>'']); ?>
            <?= $form->field($model, 'account_target', ['inputOptions'=>['disabled'=>'disabled', 'name'=>'']])->dropDownList($arrAccount, ['prompt'=>'']); ?>
            <?= $form->field($model, 'entry_value')->textInput(['type'=>'number', 'disabled'=>'disabled', 'name'=>'']); ?>
            <?= $form->field($model, 'entry_adjust')->textInput(['type'=>'number']); ?>
            <?= $form->field($model, 'description')->textInput(); ?>
            <div class="form-group">
                <?= Html::resetButton(Yii::t('button', 'Reset'), ['class'=>'btn btn-default']); ?>
                <?= Html::submitButton(Yii::t('button', 'Confirm'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_INPUT]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div><?php endif; ?>