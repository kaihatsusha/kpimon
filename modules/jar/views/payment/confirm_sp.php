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

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <?php if (!is_null($model->share_id)): ?><li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->id; ?></span>
            </a></li><?php endif; ?>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_date'); ?>
                <?= DateTimeUtils::htmlDateFormat($model->entry_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('account_source'); ?>
                <span class="pull-right"><?= isset($arrAccount[$model->account_source]) ? $arrAccount[$model->account_source] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('account_target'); ?>
                <span class="pull-right"><?= isset($arrAccount[$model->account_target]) ? $arrAccount[$model->account_target] : ''; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_value'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->entry_value); ?></span>
            </a></li>
            <?php if ($formModeValue === MasterValueUtils::PG_MODE_EDIT): ?>
                <li><a href="javascript:void(0);">
                    <?= $model->getAttributeLabel('entry_adjust'); ?>
                    <span class="pull-right badge bg-red"><?= NumberUtils::format($model->entry_adjust); ?></span>
                </a></li>
                <li><a href="javascript:void(0);">
                    <?= $model->getAttributeLabel('entry_update'); ?>
                    <span class="pull-right badge bg-red"><?= NumberUtils::format($model->entry_value + $model->entry_adjust); ?></span>
                </a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('description'); ?>
                <span class="pull-right"><?= $model->description; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('entry_status'); ?>
                <span class="pull-right"><?= isset($arrEntryLog[$model->entry_status]) ? $arrEntryLog[$model->entry_status] : ''; ?></span>
            </a></li>
        </ul>
        <div style="display: none">
            <?= $form->field($model, 'entry_date')->hiddenInput(); ?>
            <?= $form->field($model, 'account_source')->hiddenInput(); ?>
            <?= $form->field($model, 'account_target')->hiddenInput(); ?>
            <?= $form->field($model, 'entry_value')->hiddenInput(); ?>
            <?= $form->field($model, 'entry_adjust')->hiddenInput(); ?>
            <?= $form->field($model, 'description')->hiddenInput(); ?>
            <?= $form->field($model, 'entry_status')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?></div></div></div>