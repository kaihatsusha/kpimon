<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\LoginForm */

$this->title = Yii::t('common', 'Sign In');
$this->params['breadcrumbs'][] = $this->title;
$this->params['body-class'] = 'login-page';
?>
<div class="login-box">
    <div class="login-logo">
        <?php echo Html::encode($this->title) ?>
    </div><!-- /.login-logo -->
    <div class="header"></div>
    <div class="login-box-body">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <div class="body">
            <?php echo $form->field($model, 'identity') ?>
            <?php echo $form->field($model, 'password')->passwordInput() ?>
            <?php echo $form->field($model, 'rememberMe')->checkbox(['class'=>'simple']) ?>
			<div style="color:#999;margin:1em 0">
				<?php echo Yii::t('common', 'If you forgot your password you can reset it <a href="{link}">here</a>', [
					'link'=>yii\helpers\Url::to(['site/request-password-reset'])
				]) ?>
			</div>
        </div>
        <div class="footer">
            <?php echo Html::submitButton(Yii::t('common', 'Sign me in'), [
                'class' => 'btn btn-primary btn-flat btn-block',
                'name' => 'login-button'
            ]) ?>
			<div class="form-group">
				<?php echo Html::a(Yii::t('common', 'Need an account? Sign up.'), ['signup']) ?>
			</div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>

