<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\SignupForm */

$this->title = Yii::t('common', 'Signup');
$this->params['breadcrumbs'][] = $this->title;
$this->params['body-class'] = 'register-page';
?>
<div class="register-box">
	<div class="register-box">
		<div class="register-logo"> <?php echo Html::encode($this->title) ?></div>
		<div class="register-box-body">
			<?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?php echo $form->field($model, 'username') ?>
                <?php echo $form->field($model, 'email') ?>
                <?php echo $form->field($model, 'password')->passwordInput() ?>
                <div class="form-group">
                    <?php echo Html::submitButton(Yii::t('frontend', 'Signup'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
		</div>
	</div>
</div>
