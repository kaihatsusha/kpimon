<?php

namespace app\modules\api\v1\controllers;

use \app\models\User;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = '\app\modules\api\v1\resources\User';

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                [
                    'class' => HttpBasicAuth::className(),
                    'auth' => function ($username, $password) {
                        $user = User::findByLogin($username);
						var_dump($user);die;
                        return $user->validatePassword($password)
                            ? $user
                            : null;
                    }
                ],
//                HttpBearerAuth::className(),
//                QueryParamAuth::className()
            ]
        ];
		$behaviors['authenticator'] = [
			'class' => HttpBasicAuth::className(),
		];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'findModel' => [$this, 'findModel']
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction'
            ]
        ];
    }
	
//	public function findModel($id){
//		return User::model()->findByPrimary($id);
//	}
}
