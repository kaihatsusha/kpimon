<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('jar.payment', 'Create Payment');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('jar.payment', 'Edit Payment');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('jar.payment', 'Copy Payment');
    }
?>

<div class="box box-default"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <?php if (!is_null($model->share_id)): ?><tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('id'); ?></th>
                <td class="info"><?= $model->id; ?></td>
            </tr><?php endif; ?>
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('entry_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('account_source'); ?></th>
                <td class="info"><?= isset($arrAccount[$model->account_source]) ? $arrAccount[$model->account_source] : ''; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('account_target'); ?></th>
                <td class="info"><?= isset($arrAccount[$model->account_target]) ? $arrAccount[$model->account_target] : ''; ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('entry_value'); ?></th>
                <td class="info"><?= NumberUtils::format($model->entry_value); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('description'); ?></th>
                <td class="info"><?= $model->description; ?></td>
            </tr>
        </table>
        <div style="display: none">
            <?= $form->field($model, 'entry_date')->hiddenInput(); ?>
            <?= $form->field($model, 'account_source')->hiddenInput(); ?>
            <?= $form->field($model, 'account_target')->hiddenInput(); ?>
            <?= $form->field($model, 'entry_value')->hiddenInput(); ?>
            <?= $form->field($model, 'description')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    </div></div></div>
<?php ActiveForm::end(); ?></div>