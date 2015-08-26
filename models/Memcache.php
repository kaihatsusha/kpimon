<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "memcache".
 *
 * @property integer $memcache_id
 * @property string $host_name
 * @property string $port
 * @property string $key_prefix
 * @property integer $order_num
 * @property string $delete_flag
 */
class Memcache extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'memcache';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['host_name'], 'required'],
            [['order_num'], 'integer'],
            [['host_name'], 'string', 'max' => 100],
            [['port'], 'string', 'max' => 10],
            [['key_prefix'], 'string', 'max' => 200],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'memcache_id' => 'Memcache ID',
            'host_name' => 'Host Name',
            'port' => 'Port',
            'key_prefix' => 'Key Prefix',
            'order_num' => 'Order Num',
            'delete_flag' => 'Delete Flag',
        ];
    }
}
