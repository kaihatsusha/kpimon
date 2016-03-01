<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jar_share_detail".
 *
 * @property string $share_id
 * @property integer $account_id
 * @property integer $share_unit
 * @property string $delete_flag
 */
class JarShareDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jar_share_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['share_id', 'account_id', 'share_unit'], 'required'],
            [['share_id', 'account_id', 'share_unit'], 'integer'],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'share_id' => 'Share ID',
            'account_id' => 'Account ID',
            'share_unit' => 'Share Unit',
            'delete_flag' => 'Delete Flag',
        ];
    }
}
