<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "oef_nav".
 *
 * @property string $nav_id
 * @property string $trade_date
 * @property string $decide_date
 * @property double $nav_value
 * @property double $nav_value_prev
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class OefNav extends \yii\db\ActiveRecord {
    public $trade_date_from = null;
    public $trade_date_to = null;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'oef_nav';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['trade_date', 'trade_date_from', 'trade_date_to', 'decide_date', 'nav_value', 'nav_value_prev'], 'safe'],
            [['delete_flag'], 'string', 'max' => 1],
            [['trade_date', 'decide_date', 'nav_value', 'nav_value_prev'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['nav_value', 'nav_value_prev'], 'number', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['trade_date', 'decide_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['trade_date_from', 'trade_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'nav_id' => Yii::t('oef.models', 'ID'),
            'trade_date' => Yii::t('oef.models', 'Trade Date'),
            'trade_date_from' => Yii::t('oef.models', 'Trade Date From'),
            'trade_date_to' => Yii::t('oef.models', 'Trade Date To'),
            'decide_date' => Yii::t('oef.models', 'Decide Date'),
            'nav_value' => Yii::t('oef.models', 'Nav'),
            'nav_value_prev' => Yii::t('oef.models', 'Prev Nav'),
            'create_date' => Yii::t('oef.models', 'Create Date'),
            'update_date' => Yii::t('oef.models', 'Update Date'),
            'delete_flag' => Yii::t('oef.models', 'Delete Flag'),
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