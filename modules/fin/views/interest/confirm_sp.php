<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('fin.interest', 'Create Interest Unit');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('fin.interest', 'Edit Interest Unit');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('fin.interest', 'Copy Interest Unit');
    }

    $startDate = DateTimeUtils::parse($model->start_date, $fmShortDatePhp);
    $endDateHtml = null;
    $endDate = null;
    if (empty($model->end_date)) {
        $endDate = DateTimeUtils::getNow();
        $endDateHtml = '<span class="text-fuchsia pull-right">' . $endDate->format(DateTimeUtils::FM_VIEW_DATE_WD) . '</span>';
    } else {
        $endDate = DateTimeUtils::parse($model->end_date, $fmShortDatePhp);
        $endDateHtml = DateTimeUtils::htmlDateFormat($endDate, DateTimeUtils::FM_VIEW_DATE_WD, null, ['class'=>'pull-right']);
    }
    $interval = $endDate->diff($startDate);
    $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;
?>

<div class="row"><div class="col-md-12"><div class="box box-widget widget-detail">
    <div class="widget-detail-header bg-maroon"><h3 class="widget-detail-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div class="box-footer" id="finInterestConfirmForm"><?php $form = ActiveForm::begin(); ?>
        <ul class="nav nav-stacked nav-no-padding">
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('start_date'); ?>
                <?= DateTimeUtils::htmlDateFormatFromDB($model->start_date, DateTimeUtils::FM_VIEW_DATE_WD, ['class'=>'pull-right']); ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('end_date'); ?>
                <?= $endDateHtml; ?>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= Yii::t('fin.grid', 'Days'); ?>
                <span class="pull-right"><?= $days; ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= $model->getAttributeLabel('interest_unit'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->interest_unit, 2); ?></span>
            </a></li>
            <li><a href="javascript:void(0);">
                <?= Yii::t('fin.grid', 'Interest'); ?>
                <span class="pull-right badge bg-red"><?= NumberUtils::format($model->interest_unit * $days, 2); ?></span>
            </a></li>
        </ul>
        <div style="display: none">
            <?= $form->field($model, 'start_date')->hiddenInput(); ?>
            <?= $form->field($model, 'end_date')->hiddenInput(); ?>
            <?= $form->field($model, 'interest_unit')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info btn-lg btn-block', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    <?php ActiveForm::end(); ?></div>
</div></div></div>