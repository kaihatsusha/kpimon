<?php
use app\models\UserProfile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('common', 'User Settings')
?>

<div class="user-profile-form">

    <?php $form = ActiveForm::begin(); ?>

    <h2><?php echo Yii::t('common', 'Profile settings') ?></h2>

    <?php echo $form->field($model->getModel('profile'), 'fullname')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model->getModel('profile'), 'locale')->dropDownlist(Yii::$app->params['availableLocales']) ?>

    <?php echo $form->field($model->getModel('profile'), 'gender')->dropDownlist([
        UserProfile::GENDER_FEMALE => Yii::t('common', 'Female'),
        UserProfile::GENDER_MALE => Yii::t('common', 'Male')
    ]) ?>

    <h2><?php echo Yii::t('common', 'Account Settings') ?></h2>

    <?php echo $form->field($model->getModel('account'), 'username') ?>

    <?php echo $form->field($model->getModel('account'), 'password')->passwordInput() ?>

    <?php echo $form->field($model->getModel('account'), 'password_confirm')->passwordInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('common', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
