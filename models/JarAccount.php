<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "jar_account".
 *
 * @property integer $account_id
 * @property string $account_name
 * @property string $credit
 * @property string $debit
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
            [['credit', 'debit', 'share_unit', 'order_num', 'status'], 'integer'],
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
            'credit' => Yii::t('jar.models', 'Credit'),
            'debit' => Yii::t('jar.models', 'Debit'),
            'share_unit' => Yii::t('jar.models', 'Share Unit'),
            'order_num' => Yii::t('jar.models', 'Order Num'),
            'description' => Yii::t('jar.models', 'Description'),
            'status' => Yii::t('jar.models', 'Status'),
            'delete_flag' => Yii::t('jar.models', 'Delete Flag'),
        ];
    }
}