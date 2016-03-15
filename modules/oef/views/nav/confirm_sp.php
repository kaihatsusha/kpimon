<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('oef.nav', 'Create Nav');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('oef.nav', 'Edit Nav');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('oef.nav', 'Copy Nav');
    }
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-footer">
        <ul class="nav nav-stacked nav-no-padding">
            <?php if (!is_null($model->nav_id)): ?><li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('id'); ?>
                <span class="pull-right"><?= $model->nav_id; ?></span>
            </a></li><?php endif; ?>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('trade_date'); ?>
                <?= DateTimeUtils::htmlDateFormat($model->trade_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('decide_date'); ?>
                <?= DateTimeUtils::htmlDateFormat($model->decide_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('nav_value'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->nav_value, 2); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('nav_value_prev'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->nav_value_prev, 2); ?></span>
            </a></li>
        </ul>
        <div style="display: none">
            <?= $form->field($model, 'trade_date')->hiddenInput(); ?>
            <?= $form->field($model, 'decide_date')->hiddenInput(); ?>
            <?= $form->field($model, 'nav_value')->hiddenInput(); ?>
            <?= $form->field($model, 'nav_value_prev')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?></div></div></div>