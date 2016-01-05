<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "net_payment".
 *
 * @property integer $customer_id
 * @property string $entry_date
 * @property integer $credit
 * @property integer $debit
 * @property string $order_id
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class NetPayment extends \yii\db\ActiveRecord {
    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'net_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['customer_id', 'entry_date'], 'required'],
            [['customer_id', 'credit', 'debit', 'order_id'], 'integer'],
            [['entry_date', 'create_date', 'update_date'], 'safe'],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'customer_id' => Yii::t('fin.models', 'Customer ID'),
            'entry_date' => Yii::t('fin.models', 'Entry Date'),
            'credit' => Yii::t('fin.models', 'Credit'),
            'debit' => Yii::t('fin.models', 'Debit'),
            'order_id' => Yii::t('fin.models', 'Bill ID'),
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