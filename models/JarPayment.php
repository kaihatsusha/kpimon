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
 * @property integer $account_id
 * @property string $share_id
 * @property integer $credit
 * @property integer $debit
 * @property string $description
 * @property string $create_date
 * @property string $update_date
 * @property string $delete_flag
 */
class JarPayment extends \yii\db\ActiveRecord {
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
            [['entry_date', 'account_id'], 'required'],
            [['entry_date', 'create_date', 'update_date'], 'safe'],
            [['account_id', 'share_id', 'credit', 'debit'], 'integer'],
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
            'account_id' => Yii::t('jar.models', 'Account'),
            'share_id' => Yii::t('jar.models', 'Share'),
            'credit' => Yii::t('jar.models', 'Credit'),
            'debit' => Yii::t('jar.models', 'Debit'),
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