<?php
namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "fin_total_assets_month".
 *
 * @property string $assets_id
 * @property integer $year
 * @property integer $month
 * @property string $assets_value
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class FinTotalAssetsMonth extends \yii\db\ActiveRecord {
    public $fmonth = null;
    public $fmonth_from = null;
    public $fmonth_to = null;
    public $credit = null;
    public $debit = null;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'fin_total_assets_month';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['year', 'month', 'assets_value'], 'integer'],
            [['create_date', 'update_date', 'fmonth', 'fmonth_from', 'fmonth_to'], 'safe'],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'assets_id' => Yii::t('fin.models', 'ID'),
            'year' => Yii::t('fin.models', 'Year'),
            'month' => Yii::t('fin.models', 'Month'),
            'fmonth' => Yii::t('fin.models', 'Month'),
            'fmonth_from' => Yii::t('fin.models', 'Month From'),
            'fmonth_to' => Yii::t('fin.models', 'Month To'),
            'assets_value' => Yii::t('fin.models', 'Assets'),
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