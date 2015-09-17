<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FinAccountEntry */
/* @var $form ActiveForm */
?>
<div class="testview">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'entry_date') ?>
        <?= $form->field($model, 'create_date') ?>
        <?= $form->field($model, 'update_date') ?>
        <?= $form->field($model, 'entry_value') ?>
        <?= $form->field($model, 'account_source') ?>
        <?= $form->field($model, 'account_target') ?>
        <?= $form->field($model, 'entry_status') ?>
        <?= $form->field($model, 'delete_flag') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- testview -->
