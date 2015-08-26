<?php

namespace app\modules\api\ga\models;

use Yii;

/**
 * This is the model class for table "media".
 *
 * @property integer $media_id
 * @property string $media_name
 * @property string $rt_keyds
 * @property integer $order_num
 * @property integer $logo
 * @property string $delete_flag
 */
class Media extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_num'], 'integer'],
            [['media_name', 'rt_keyds', 'logo'], 'string', 'max' => 100],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'media_id' => 'Media ID',
            'media_name' => 'Media Name',
            'rt_keyds' => 'Rt Keyds',
            'order_num' => 'Order Num',
			'logo' => 'Logo',
            'delete_flag' => 'Delete Flag',
        ];
    }
}
