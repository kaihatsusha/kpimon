<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jar_payment".
 *
 * @property string $id
 * @property string $entry_date
 * @property integer $account_id
 * @property string $share_id
 * @property integer $credit
 * @property integer $debit
 * @property string $description
 * @property string $delete_flag
 */
class JarPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jar_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['entry_date', 'account_id'], 'required'],
            [['entry_date'], 'safe'],
            [['account_id', 'share_id', 'credit', 'debit'], 'integer'],
            [['description'], 'string', 'max' => 100],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entry_date' => 'Entry Date',
            'account_id' => 'Account ID',
            'share_id' => 'Share ID',
            'credit' => 'Credit',
            'debit' => 'Debit',
            'description' => 'Description',
            'delete_flag' => 'Delete Flag',
        ];
    }
}
