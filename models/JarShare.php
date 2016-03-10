<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "jar_share".
 *
 * @property string $share_id
 * @property integer $share_value
 * @property string $share_date
 * @property string $description
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class JarShare extends \yii\db\ActiveRecord {
    public $share_date_from = null;
    public $share_date_to = null;
    public $share_value_old = null;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'jar_share';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['share_date', 'create_date', 'update_date', 'share_date_from', 'share_date_to', 'share_value', 'description'], 'safe'],
            [['delete_flag'], 'string', 'max' => 1],
            [['share_date_from', 'share_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]],
            [['share_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['share_date', 'share_value'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['description'], 'string', 'max' => 200, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['share_value'], 'integer', 'min' => 0, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'share_id' => Yii::t('jar.models', 'ID'),
            'share_value' => Yii::t('jar.models', 'Share Value'),
            'share_date' => Yii::t('jar.models', 'Share Date'),
            'share_date_from' => Yii::t('jar.models', 'Share Date From'),
            'share_date_to' => Yii::t('jar.models', 'Share Date To'),
            'description' => Yii::t('jar.models', 'Description'),
            'create_date' => Yii::t('jar.models', 'Create Date'),
            'update_date' => Yii::t('jar.models', 'Update Date'),
            'delete_flag' => Yii::t('jar.models', 'Delete Flag'),
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