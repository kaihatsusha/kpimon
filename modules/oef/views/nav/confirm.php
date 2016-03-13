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

<div class="box box-default"><?php $form = ActiveForm::begin(['requiredCssClass' => 'form-group-required']); ?>
    <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('fin.form', 'Details'); ?></h3></div>
    <div class="box-body"><div class="row"><div class="col-md-12">
        <table class="table table-bordered">
            <?php if (!is_null($model->nav_id)): ?><tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('nav_id'); ?></th>
                <td class="info"><?= $model->nav_id; ?></td>
                </tr><?php endif; ?>
            <tr>
                <th class="warning" style="width: 200px;"><?= $model->getAttributeLabel('trade_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->trade_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('decide_date'); ?></th>
                <td class="info"><?= DateTimeUtils::htmlDateFormat($model->decide_date, DateTimeUtils::FM_VIEW_DATE_WD, $fmShortDatePhp, true); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('nav_value'); ?></th>
                <td class="info"><?= NumberUtils::format($model->nav_value, 2); ?></td>
            </tr>
            <tr>
                <th class="warning"><?= $model->getAttributeLabel('nav_value_prev'); ?></th>
                <td class="info"><?= NumberUtils::format($model->nav_value_prev, 2); ?></td>
            </tr>
        </table>
        <div style="display: none">
            <?= $form->field($model, 'trade_date')->hiddenInput(); ?>
            <?= $form->field($model, 'decide_date')->hiddenInput(); ?>
            <?= $form->field($model, 'nav_value')->hiddenInput(); ?>
            <?= $form->field($model, 'nav_value_prev')->hiddenInput(); ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('button', 'Back'), ['class'=>'btn btn-default', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_BACK]); ?>
            <?= Html::submitButton(Yii::t('button', 'Save'), ['class'=>'btn btn-info', 'name'=>MasterValueUtils::SM_MODE_NAME, 'value'=>MasterValueUtils::SM_MODE_CONFIRM]); ?>
        </div>
    </div></div></div>
<?php ActiveForm::end(); ?></div>