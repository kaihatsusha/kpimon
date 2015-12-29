<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "net_customer".
 *
 * @property integer $id
 * @property string $name
 * @property integer $balance
 * @property string $description
 * @property integer $status
 * @property integer $order_num
 * @property string $delete_flag
 */
class NetCustomer extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'net_customer';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'description', 'status'], 'safe'],
            [['name'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['balance', 'status', 'order_num'], 'integer'],
            [['name'], 'string', 'max' => 50, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['description'], 'string', 'max' => 100, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('fin.models', 'ID'),
            'name' => Yii::t('fin.models', 'Name'),
            'balance' => Yii::t('fin.models', 'Balance'),
            'description' => Yii::t('fin.models', 'Description'),
            'status' => Yii::t('fin.models', 'Status'),
            'order_num' => Yii::t('fin.models', 'Order Num'),
            'delete_flag' => Yii::t('fin.models', 'Delete Flag'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_date', 'update_date'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['update_date'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}