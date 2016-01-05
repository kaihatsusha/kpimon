<?php
    use yii\helpers\Html;
    use app\components\MasterValueUtils;
    use kartik\datetime\DateTimePicker;
?>

<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?= $title; ?></h4>
        </div>
        <div class="modal-body">
            <?= $form->field($model, 'item_name')->textInput(); ?>
            <?= $form->field($model, 'price')->textInput(); ?>
            <?= $form->field($model, 'pay_date')->widget(DateTimePicker::className(), ['type'=>1,
                'pluginOptions'=>['autoclose'=>true, 'format'=>$fmShortDateJui, 'startView'=>2, 'minView'=>2, 'todayHighlight'=>true]
            ]); ?>
            <?= $form->field($model, 'description')->textInput(); ?>
        </div>
        <div style="display: none">
            <?= $form->field($model, 'item_no')->hiddenInput(); ?>
            <?= $form->field($model, 'delete_flag')->hiddenInput(); ?>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('button', 'Close'); ?></button>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['id'=>'btnEditModal', 'class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_ADD_ITEM]); ?>
            <?= Html::submitButton(Yii::t('button', 'Delete'), ['id'=>'btnDeleteModal', 'class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_DEL_ITEM]); ?>
        </div>
    </div></div>
</div>