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
        $endDateHtml = '<span class="text-fuchsia">' . $endDate->format(DateTimeUtils::FM_VIEW_DATE_WD) . '</span>';
    } else {
        $endDate = DateTimeUtils::parse($model->end_date, $fmShortDatePhp);
        $endDateHtml = DateTimeUtils::htmlDateFormat($endDate, DateTimeUtils::FM_VIEW_DATE_WD, null, true);
    }
    $interval = $endDate->diff($startDate);
    $days = ($interval->invert === 1 ? 1 : -1) * $interval->days + 1;
?>

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div id="finInterestConfirmForm" class="box-body"><?php $form = ActiveForm::begin(); ?>
        <div class="row"><div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('start_date'); ?></th>
                    <td class="info"><?= DateTimeUtils::htmlDateFormat($model->start_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('end_date'); ?></th>
                    <td class="info"><?= $endDateHtml; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= Yii::t('fin.grid', 'Days'); ?></th>
                    <td class="info"><?= $days; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('interest_unit'); ?></th>
                    <td class="info"><?= NumberUtils::format($model->interest_unit, 4); ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= Yii::t('fin.grid', 'Interest'); ?></th>
                    <td class="info"><?= NumberUtils::format($model->interest_unit * $days, 4); ?></td>
                </tr>
            </table>
            <div style="display: none">
                <?= $form->field($model, 'start_date')->hiddenInput(); ?>
                <?= $form->field($model, 'end_date')->hiddenInput(); ?>
                <?= $form->field($model, 'interest_unit')->hiddenInput(); ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
                <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div>
