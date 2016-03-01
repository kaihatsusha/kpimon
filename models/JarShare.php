<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "jar_share".
 *
 * @property string $share_id
 * @property integer $share_value
 * @property string $share_date
 * @property string $delete_flag
 */
class JarShare extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jar_share';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['share_value'], 'integer'],
            [['share_date'], 'safe'],
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
            'share_value' => 'Share Value',
            'share_date' => 'Share Date',
            'delete_flag' => 'Delete Flag',
        ];
    }
}
