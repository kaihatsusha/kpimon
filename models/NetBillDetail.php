<?php
namespace app\models;

use Yii;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "net_bill_detail".
 *
 * @property string $bill_id
 * @property integer $item_no
 * @property string $item_name
 * @property integer $price
 * @property string $pay_date
 * @property string $description
 */
class NetBillDetail extends \yii\db\ActiveRecord {
    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    public $delete_flag = 0;
    public $is_valid = true;
    public $price_old = 0;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'net_bill_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['item_no', 'item_name', 'price', 'pay_date', 'description', 'delete_flag'], 'safe'],
            [['item_name', 'price', 'pay_date'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['item_name'], 'string', 'max' => 50, 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['description'], 'string', 'max' => 100, 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['item_no', 'price'], 'integer', 'on' => [MasterValueUtils::SCENARIO_CREATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'bill_id' => Yii::t('fin.models', 'Bill Id'),
            'item_no' => Yii::t('fin.models', 'Item No'),
            'item_name' => Yii::t('fin.models', 'Item Name'),
            'price' => Yii::t('fin.models', 'Price'),
            'pay_date' => Yii::t('fin.models', 'Pay Date'),
            'description' => Yii::t('fin.models', 'Description'),
        ];
    }
}