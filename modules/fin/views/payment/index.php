<?php
	use app\components\MasterValueUtils;
?>
<?php if(Yii::$app->session->hasFlash(MasterValueUtils::FLASH_SUCCESS)): ?><div class="alert alert-success">
	<?php echo Yii::$app->session->getFlash(MasterValueUtils::FLASH_SUCCESS); ?>
</div><?php endif; ?>
<?php if(Yii::$app->session->hasFlash(MasterValueUtils::FLASH_ERROR)): ?><div class="alert alert-error">
	<?php echo Yii::$app->session->getFlash(MasterValueUtils::FLASH_ERROR); ?>
</div><?php endif; ?>