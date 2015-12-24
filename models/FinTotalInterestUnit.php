<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "fin_total_interest_unit".
 *
 * @property string $id
 * @property string $start_date
 * @property string $end_date
 * @property double $interest_unit
 * @property string $create_date
 * @property string $update_date
 */
class FinTotalInterestUnit extends \yii\db\ActiveRecord {
    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'fin_total_interest_unit';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['start_date', 'end_date', 'create_date', 'update_date'], 'safe'],
            [['interest_unit'], 'number'],
            [['start_date', 'interest_unit'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['interest_unit'], 'number', 'min' => 0, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]],
            [['start_date', 'end_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_COPY]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'start_date' => Yii::t('fin.models', 'Start Date'),
            'end_date' => Yii::t('fin.models', 'End Date'),
            'interest_unit' => Yii::t('fin.models', 'Unit'),
            'create_date' => Yii::t('fin.models', 'Create Date'),
            'update_date' => Yii::t('fin.models', 'Update Date'),
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
