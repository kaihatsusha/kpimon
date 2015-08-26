<?php

namespace app\modules\api\ga\models;

use Yii;

/**
 * This is the model class for table "ga_acount".
 *
 * @property integer $acount_id
 * @property string $rt_pvs_per_minute
 * @property string $rt_pvs_per_second
 * @property string $rt_pvs_right_now
 * @property string $rt_active_pages
 * @property string $rt_social_traffic
 * @property string $rt_referrals
 * @property string $rt_keywords
 * @property string $rt_locations
 * @property string $cookie
 */
class GaAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ga_acount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cookie'], 'string'],
            [['rt_pvs_per_minute', 'rt_pvs_per_second', 'rt_pvs_right_now', 'rt_active_pages', 'rt_social_traffic', 'rt_referrals', 'rt_keywords', 'rt_locations'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'acount_id' => 'Acount ID',
            'rt_pvs_per_minute' => 'Rt Pvs Per Minute',
            'rt_pvs_per_second' => 'Rt Pvs Per Second',
            'rt_pvs_right_now' => 'Rt Pvs Right Now',
            'rt_active_pages' => 'Rt Active Pages',
            'rt_social_traffic' => 'Rt Social Traffic',
            'rt_referrals' => 'Rt Referrals',
            'rt_keywords' => 'Rt Keywords',
            'rt_locations' => 'Rt Locations',
            'cookie' => 'Cookie',
        ];
    }
}
