<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
    use app\components\DateTimeUtils;
    use app\components\MasterValueUtils;
    use app\components\NumberUtils;

    $formModeValue = $formMode[MasterValueUtils::PG_MODE_NAME];
    $this->title = Yii::t('fin.deposit', 'Create Fixed Deposit');
    if ($formModeValue === MasterValueUtils::PG_MODE_EDIT) {
        $this->title = Yii::t('fin.deposit', 'Edit Fixed Deposit');
    } elseif ($formModeValue === MasterValueUtils::PG_MODE_COPY) {
        $this->title = Yii::t('fin.deposit', 'Copy Fixed Deposit');
    }

    $openingDate = DateTimeUtils::getDateFromDB($model->opening_date);
    $closingDate = DateTimeUtils::getDateFromDB($model->closing_date);
    $dateDiff = $closingDate->diff($openingDate);
?>

<div class="box box-default">
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Confirm Values'); ?></h3></div>
    <div id="finDepositConfirmForm" class="box-body"><?php $form = ActiveForm::begin(); ?>
        <div class="row"><div class="col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('saving_account'); ?></th>
                    <td class="info"><?= isset($arrSavingAccount[$model->saving_account]) ? $arrSavingAccount[$model->saving_account] : ''; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('interest_rate'); ?></th>
                    <td class="info"><?= NumberUtils::format($model->interest_rate, 4); ?> %</td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('opening_date'); ?></th>
                    <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->opening_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('closing_date'); ?></th>
                    <td class="info"><?= DateTimeUtils::htmlDateFormatFromDB($model->closing_date, DateTimeUtils::FM_VIEW_DATE_WD, true); ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('interest_days'); ?></th>
                    <td class="info"><?= $dateDiff->days; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('interest_add'); ?></th>
                    <td class="info"><?= NumberUtils::format($model->interest_add); ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('entry_value'); ?></th>
                    <td class="info"><?= NumberUtils::format($model->entry_value); ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('add_flag'); ?></th>
                    <td class="info"><?= isset($arrTimedepositTrantype[$model->add_flag]) ? $arrTimedepositTrantype[$model->add_flag] : ''; ?></td>
                </tr>
                <tr>
                    <th class="warning"><?= $model->getAttributeLabel('current_assets'); ?></th>
                    <td class="info"><?= isset($arrCurrentAssets[$model->current_assets]) ? $arrCurrentAssets[$model->current_assets] : ''; ?></td>
                </tr>
            </table>
            <div style="display: none">
                <?= $form->field($model, 'saving_account')->hiddenInput(); ?>
                <?= $form->field($model, 'interest_rate')->hiddenInput(); ?>
                <?= $form->field($model, 'opening_date')->hiddenInput(); ?>
                <?= $form->field($model, 'closing_date')->hiddenInput(); ?>
                <?= $form->field($model, 'interest_add')->hiddenInput(); ?>
                <?= $form->field($model, 'entry_value')->hiddenInput(); ?>
                <?= $form->field($model, 'add_flag')->hiddenInput(); ?>
                <?= $form->field($model, 'current_assets')->hiddenInput(); ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
                <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
            </div>
        </div></div>
    <?php ActiveForm::end(); ?></div>
</div>