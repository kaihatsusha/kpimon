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
    public $entry_adjust = 0;
    public $entry_updated = 0;

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
            [['entry_adjust', 'entry_date', 'entry_date_from', 'entry_date_to', 'create_date', 'update_date'], 'safe'],
            [['entry_value', 'account_source', 'account_target', 'share_id', 'entry_status'], 'integer'],
            [['description'], 'string', 'max' => 100],
            [['delete_flag'], 'string', 'max' => 1],
            [['entry_date_from', 'entry_date_to'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_LIST]],
            [['account_source', 'account_target'], 'default', 'value' => 0, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE, MasterValueUtils::SCENARIO_LIST]],
            [['entry_date'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['entry_value'], 'required', 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['entry_value'], 'integer', 'min' => 0, 'on' => [MasterValueUtils::SCENARIO_CREATE]],
            [['account_source'], 'validateSourceRelatedTarget', 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]],
            [['account_source'], 'validateEntryAdjust', 'on' => [MasterValueUtils::SCENARIO_UPDATE]],
            [['entry_date'], 'date', 'format' => 'php:' . self::$_PHP_FM_SHORTDATE, 'on' => [MasterValueUtils::SCENARIO_CREATE, MasterValueUtils::SCENARIO_UPDATE]]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('jar.models', 'ID'),
            'entry_date' => Yii::t('jar.models', 'Entry Date'),
            'entry_date_from' => Yii::t('jar.models', 'Entry Date From'),
            'entry_date_to' => Yii::t('jar.models', 'Entry Date To'),
            'entry_value' => Yii::t('jar.models', 'Entry Value'),
            'entry_adjust' => Yii::t('jar.models', 'Adjustment'),
            'entry_updated' => Yii::t('jar.models', 'Updated Value'),
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

    /**
     * validate Source Related Target
     */
    public function validateSourceRelatedTarget() {
        if ($this->account_source == 0 && $this->account_target == 0) {
            $this->addError('account_source', Yii::t('common', 'Debit Account and Credit Account must not be empty at the same time.'));
            $this->addError('account_target', Yii::t('common', 'Debit Account and Credit Account must not be empty at the same time.'));
            return false;
        }
        if ($this->account_source == $this->account_target) {
            $this->addError('account_source', Yii::t('common', 'Debit Account must be different to Credit Account.'));
            $this->addError('account_target', Yii::t('common', 'Credit Account must be different to Debit Account.'));
            return false;
        }
        if ($this->account_source > 0 && $this->account_target > 0 && $this->account_source != MasterValueUtils::MV_JAR_ACCOUNT_TEMP && $this->account_target != MasterValueUtils::MV_JAR_ACCOUNT_TEMP) {
            $this->addError('account_source', Yii::t('common', 'Debit Account or Credit Account must not be TEMP.'));
            $this->addError('account_target', Yii::t('common', 'Debit Account or Credit Account must not be TEMP.'));
            return false;
        }
        if ($this->account_source == 0 && $this->account_target == MasterValueUtils::MV_JAR_ACCOUNT_TEMP) {
            $this->addError('account_target', Yii::t('common', 'Credit Account must not be TEMP.'));
            return false;
        }
        if ($this->account_target == 0 && $this->account_source == MasterValueUtils::MV_JAR_ACCOUNT_TEMP) {
            $this->addError('account_source', Yii::t('common', 'Debit Account must not be TEMP.'));
            return false;
        }
        return true;
    }

    /**
     * validate Entry Adjust
     */
    public function validateEntryAdjust() {
        $temp = $this->entry_value + $this->entry_adjust;
        if ($temp < 0) {
            $this->addError('entry_adjust', Yii::t('common', '{field} must be larger than {value}', ['field'=>$this->getAttributeLabel('entry_adjust'), 'value'=>-$this->entry_value]));
            return false;
        }
        return true;
    }
}