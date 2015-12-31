<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "net_bill".
 *
 * @property string $id
 * @property string $bill_date
 * @property integer $total
 * @property integer $member_num
 * @property string $member_list
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class NetBill extends \yii\db\ActiveRecord {
    public $bill_date_from = null;
    public $bill_date_to = null;
    public $arr_member_list = null;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'net_bill';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['bill_date', 'bill_date_from', 'bill_date_to', 'arr_member_list', 'create_date', 'update_date'], 'safe'],
            [['total', 'member_num'], 'integer'],
            [['member_list'], 'string'],
            [['delete_flag'], 'string', 'max' => 1],
            [['bill_date_from', 'bill_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]],
            [['bill_date', 'arr_member_list'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['bill_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('fin.models', 'ID'),
            'bill_date' => Yii::t('fin.models', 'Bill Date'),
            'bill_date_from' => Yii::t('fin.models', 'Bill Date From'),
            'bill_date_to' => Yii::t('fin.models', 'Bill Date To'),
            'total' => Yii::t('fin.models', 'Total'),
            'member_num' => Yii::t('fin.models', 'Member Number'),
            'member_list' => Yii::t('fin.models', 'Member List'),
            'arr_member_list' => Yii::t('fin.models', 'Member List'),
            'create_date' => Yii::t('fin.models', 'Create Date'),
            'update_date' => Yii::t('fin.models', 'Update Date'),
            'delete_flag' => Yii::t('fin.models', 'Delete Flag')
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