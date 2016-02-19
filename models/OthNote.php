<?php
namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "oth_note".
 *
 * @property integer $id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property integer $costs
 * @property integer $order_num
 * @property integer $entry_log
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class OthNote extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'oth_note';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['start_date', 'end_date', 'create_date', 'update_date'], 'safe'],
            [['costs', 'order_num', 'entry_log'], 'integer'],
            [['name'], 'string', 'max' => 50],
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
            'start_date' => Yii::t('fin.models', 'Start Date'),
            'end_date' => Yii::t('fin.models', 'End Date'),
            'costs' => Yii::t('fin.models', 'Costs'),
            'order_num' => Yii::t('fin.models', 'Order Num'),
            'entry_log' => Yii::t('fin.models', 'Entry Log'),
            'create_date' => Yii::t('fin.models', 'Create Date'),
            'update_date' => Yii::t('fin.models', 'Update Date'),
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