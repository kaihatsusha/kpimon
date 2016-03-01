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
class JarAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jar_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
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
    public function attributeLabels()
    {
        return [
            'account_id' => 'Account ID',
            'account_name' => 'Account Name',
            'credit' => 'Credit',
            'debit' => 'Debit',
            'share_unit' => 'Share Unit',
            'order_num' => 'Order Num',
            'description' => 'Description',
            'status' => 'Status',
            'delete_flag' => 'Delete Flag',
        ];
    }
}
