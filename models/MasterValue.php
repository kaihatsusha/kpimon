<?php
namespace app\models;

/**
 * This is the model class for table "master_value".
 *
 * @property string $master_value_id
 * @property string $value_code
 * @property string $value
 * @property integer $order
 * @property string $locale
 * @property string $label
 * @property string $delete_flag
 */
class MasterValue extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'master_value';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['value_code', 'value', 'locale'], 'required'],
            [['order'], 'integer'],
            [['value_code', 'value'], 'string', 'max' => 50],
            [['locale'], 'string', 'max' => 6],
            [['label'], 'string', 'max' => 100],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'master_value_id' => 'Master Value ID',
            'value_code' => 'Value Code',
            'value' => 'Value',
            'order' => 'Order',
            'locale' => 'Locale',
            'label' => 'Label',
            'delete_flag' => 'Delete Flag',
        ];
    }
}