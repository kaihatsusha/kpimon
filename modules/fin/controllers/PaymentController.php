<?php
namespace app\modules\fin\controllers;

use yii\web\Controller;
use app\models\FinAccountEntry;

class PaymentController extends Controller {
	public function actionIndex() {
		
	}
	
	public function actionCreate() {
		$model = new FinAccountEntry();
		return $this->render('create', ['model'=>$model]);
	}
	
	public function actionUpdate() {
		/*if ($post === NULL)
        {
        Yii::$app->session->setFlash('error', 'A post with that id does not exist');
        Yii::$app->getResponse()->redirect(array('site/index'));
		 * 
		 <div class="alert alert-error">
        <?php echo Yii::$app->session->getFlash('error'); ?>
        </div>
        <?php endif; ?>
 
        <?php if(Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
        <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
		 * 
		 
		         $model = new Post;
        if ($this->populate($_POST, $model) && $model->save())
        Yii::$app->response->redirect(array('site/read', 'id' => $model->id));
 
        echo $this->render('create', array(
        'model' => $model
        ));
        }*/
	}
}
?>