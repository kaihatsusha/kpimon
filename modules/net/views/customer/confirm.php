<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\MasterValueUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('net.customer', 'Create Customer');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('net.customer', 'Edit Customer');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('net.customer', 'Copy Customer');
    }
?>

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div id="netCustomerConfirmForm" class="box-body"><?php $form = ActiveForm::begin(); ?>
        <div class="row"><div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('name'); ?></th>
                    <td class="info"><?= $model->name; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('status'); ?></th>
                    <td class="info"><?= isset($arrCustomerStatus[$model->status]) ? $arrCustomerStatus[$model->status] : ''; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('description'); ?></th>
                    <td class="info"><?= $model->description; ?></td>
                </tr>
            </table>
            <div style="display: none">
                <?= $form->field($model, 'name')->hiddenInput(); ?>
                <?= $form->field($model, 'status')->hiddenInput(); ?>
                <?= $form->field($model, 'description')->hiddenInput(); ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
                <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div>