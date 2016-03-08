<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "jar_account".
 *
 * @property integer $account_id
 * @property string $account_name
 * @property integer $account_type
 * @property integer $real_balance
 * @property integer $useable_balance
 * @property integer $share_unit
 * @property integer $order_num
 * @property string $description
 * @property integer $status
 * @property string $delete_flag
 */
class JarAccount extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'jar_account';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['account_name'], 'required'],
            [['account_type', 'real_balance', 'useable_balance', 'share_unit', 'order_num', 'status'], 'integer'],
            [['account_name', 'description'], 'string', 'max' => 200],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'account_id' => Yii::t('jar.models', 'Account ID'),
            'account_name' => Yii::t('jar.models', 'Account Name'),
            'account_type' => Yii::t('jar.models', 'Account Type'),
            'real_balance' => Yii::t('jar.models', 'Real Balance'),
            'useable_balance' => Yii::t('jar.models', 'Useable Balance'),
            'share_unit' => Yii::t('jar.models', 'Share Unit'),
            'order_num' => Yii::t('jar.models', 'Order Num'),
            'description' => Yii::t('jar.models', 'Description'),
            'status' => Yii::t('jar.models', 'Status'),
            'delete_flag' => Yii::t('jar.models', 'Delete Flag'),
        ];
    }
}
