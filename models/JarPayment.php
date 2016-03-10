<?php
namespace app\models;

use Yii;
use yii\db\Expression;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "jar_payment".
 *
 * @property string $id
 * @property string $entry_date
 * @property integer $entry_value
 * @property integer $account_source
 * @property integer $account_target
 * @property string $share_id
 * @property string $description
 * @property integer $entry_status
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class JarPayment extends \yii\db\ActiveRecord {
    public $entry_date_from = null;
    public $entry_date_to = null;

    public static $_PHP_FM_SHORTDATE = 'Y-m-d';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'jar_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['entry_date'], 'required'],
            [['entry_date', 'create_date', 'update_date'], 'safe'],
            [['entry_value', 'account_source', 'account_target', 'share_id', 'entry_status'], 'integer'],
            [['description'], 'string', 'max' => 100],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('jar.models', 'ID'),
            'entry_date' => Yii::t('jar.models', 'Entry Date'),
            'entry_value' => Yii::t('jar.models', 'Entry Value'),
            'account_source' => Yii::t('jar.models', 'Debit Account'),
            'account_target' => Yii::t('jar.models', 'Credit Account'),
            'share_id' => Yii::t('jar.models', 'Share'),
            'description' => Yii::t('jar.models', 'Description'),
            'entry_status' => Yii::t('jar.models', 'Status'),
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