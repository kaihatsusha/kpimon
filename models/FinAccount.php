<?php
namespace app\models;

use Yii;
use app\components\MasterValueUtils;

/**
 * This is the model class for table "fin_account".
 *
 * @property string $account_id
 * @property string $account_name
 * @property integer $account_type
 * @property integer $bank_id
 * @property string $opening_date
 * @property string $closing_date
 * @property string $opening_balance
 * @property string $closing_balance
 * @property double $noterm_interest_rate
 * @property double $term_interest_rate
 * @property integer $interest_method
 * @property string $capital
 * @property string $order_num
 * @property string $delete_flag
 */
class FinAccount extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'fin_account';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['account_name'], 'required'],
            [['account_type', 'bank_id', 'opening_balance', 'closing_balance', 'interest_method', 'capital', 'order_num'], 'integer'],
            [['opening_date', 'closing_date'], 'safe'],
            [['noterm_interest_rate', 'term_interest_rate'], 'number'],
            [['account_name'], 'string', 'max' => 200],
            [['delete_flag'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'account_id' => Yii::t('fin.models', 'Account ID'),
            'account_name' => Yii::t('fin.models', 'Account Name'),
            'account_type' => Yii::t('fin.models', 'Account Type'),
            'bank_id' => Yii::t('fin.models', 'Bank ID'),
            'opening_date' => Yii::t('fin.models', 'Opening Date'),
            'closing_date' => Yii::t('fin.models', 'Closing Date'),
            'opening_balance' => Yii::t('fin.models', 'Opening Balance'),
            'closing_balance' => Yii::t('fin.models', 'Closing Balance'),
            'noterm_interest_rate' => Yii::t('fin.models', 'Noterm Interest Rate'),
            'term_interest_rate' => Yii::t('fin.models', 'Term Interest Rate'),
            'interest_method' => Yii::t('fin.models', 'Interest Method'),
            'capital' => Yii::t('fin.models', 'Capital'),
			'order_num' => Yii::t('fin.models', 'Order Num'),
            'delete_flag' => Yii::t('fin.models', 'Delete Flag'),
        ];
    }
	
	public function initialize() {
		// do nothing
	}
	
	public function instance() {
		$result = null;
		switch ($this->account_type) {
			case MasterValueUtils::MV_FIN_ACCOUNT_TYPE_CASH :
				$result = new extended\FinAccount01I00();
				break;
			case MasterValueUtils::MV_FIN_ACCOUNT_TYPE_CURRENT :
				$result = new extended\FinAccount02I00();
				break;
			case MasterValueUtils::MV_FIN_ACCOUNT_TYPE_CREDIT :
				$result = new extended\FinAccount03I00();
				break;
			case MasterValueUtils::MV_FIN_ACCOUNT_TYPE_TIME_DEPOSIT :
				$result = new extended\FinAccount04I00();
				break;
			case MasterValueUtils::MV_FIN_ACCOUNT_TYPE_ADVANCE :
				$result = new extended\FinAccount05I00();
				break;
			case MasterValueUtils::MV_FIN_ACCOUNT_TYPE_OIR :
				$result = new extended\FinAccount06I00();
				break;
            case MasterValueUtils::MV_FIN_ACCOUNT_TYPE_CCQ :
                $result = new extended\FinAccount07I00();
                break;
		}
		
		if (is_null($result)) {
			return $this;
		}
		
		foreach ($this->getTableSchema()->columns as $column) {
			$result->{$column->name} = $this->{$column->name};
		}
		return $result;
	}
}