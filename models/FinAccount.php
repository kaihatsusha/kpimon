<?php
namespace app\models;

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
            'account_id' => 'Account ID',
            'account_name' => 'Account Name',
            'account_type' => 'Account Type',
            'bank_id' => 'Bank ID',
            'opening_date' => 'Opening Date',
            'closing_date' => 'Closing Date',
            'opening_balance' => 'Opening Balance',
            'closing_balance' => 'Closing Balance',
            'noterm_interest_rate' => 'Noterm Interest Rate',
            'term_interest_rate' => 'Term Interest Rate',
            'interest_method' => 'Interest Method',
            'capital' => 'Capital',
			'order_num' => 'Order Num',
            'delete_flag' => 'Delete Flag',
        ];
    }
	
	protected function initialize() {
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
		}
		
		if (is_null($result)) {
			return $this;
		}
		
		foreach ($this->getTableSchema()->columns as $column) {
			$result->{$column->name} = $this->{$column->name};
		}
		$result->initialize();
		return $result;
	}
}