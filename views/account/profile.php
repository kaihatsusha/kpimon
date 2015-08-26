<?php

use app\models\UserProfile;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\UserProfile */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = Yii::t('common', 'Edit profile')
?>

<div class="user-profile-form">
	<?php $form = ActiveForm::begin(); ?>
    <?php echo $form->field($model, 'fullname')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model, 'locale')->dropDownlist(Yii::$app->params['availableLocales']) ?>

    <?php echo $form->field($model, 'gender')->dropDownlist([
        UserProfile::GENDER_FEMALE => Yii::t('common', 'Female'),
        UserProfile::GENDER_MALE => Yii::t('common', 'Male')
    ]) ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
