<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\PasswordResetRequestForm */

$this->title =  Yii::t('common', 'Request password reset');
$this->params['breadcrumbs'][] = $this->title;
$this->params['body-class'] = 'login-page';
?>
<div class="login-box">
   <div class="login-logo">
        <?php echo Html::encode($this->title) ?>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
		<?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
			<?php echo $form->field($model, 'email') ?>
			<div class="form-group">
				<?php echo Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
			</div>
		<?php ActiveForm::end(); ?>
    </div>
</div>
