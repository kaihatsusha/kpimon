<?php
namespace app\modules\api\ga\components;
/**
 * Description of GARequest
 *
 * @author Tri
 */
/**
 * GARequest
 *
 * @property app\modules\api\ga\components\GARequest $_gaRequest
 */
class GARequest {
	
	const KEY_DATA_PREFIX = '__GA__Data__MediaId-';
	const KEY_RTOUTPUT = '__GA__RTOUTPUT';
	private $_gaCookie = null;
	private $_rtOutputs = null;
	private $_oldDataList = null;
	private static $_gaRequest = null;
	public static $_CURL_ERROR_CODE_EXT = [
		1000 => 'Cookie is expired !',
		1001 => '',
	];
	
	private function __construct() {
		$this->_oldDataList = array();
	}
	
	public static function install() {
		if (self::$_gaRequest == null) {
			self::$_gaRequest = new GARequest();
			self::$_gaRequest->gaAccount();
		}
		return self::$_gaRequest;
	}
	
		
	private function writeToMemcached($key,$data,$duration = 0) {	
		\Yii::$app->memCache->set($key, $data,$duration);
		return false;
	}
	
	private function readFromMemcached($key) {
		return \Yii::$app->memCache->get($key);
//		return false;
	}
	
	private function readFromDatabase() {

	}
	
	/**
	 * 
	 * @param type $realTimeKey
	 * @return type
	 */
	public function realTimeCurl($realTimeKey = 'a46049337w89311011p92802096') {
		$url = 'https://www.google.com/analytics/realtime/realtime/getData?';
		$url .= 'key=' . $realTimeKey;
		$url .= '&ds=' . $realTimeKey;
		$url .= '&pageId=RealtimeReport%2Frt-overview&q=t%3A0%7C%3A1%7C%3A0%3A%2Ct%3A33%7C%3A1%7C%3A5%3A%2Cot%3A0%3A0%3A4%3A%2Cot%3A0%3A0%3A3%3A%2Ct%3A7%7C%3A1%7C%3A5%3A6%3D%3DREFERRAL%3B%2Ct%3A7%7C%3A1%7C%3A5%3A6%3D%3DSOCIAL%3B%2Ct%3A10%7C%3A1%7C%3A10%3A%2Ct%3A18%7C%3A1%7C%3A10%3A%2Cg%3A4%7C5%7C2%7C1%7C%3A1%7C%3A10%3A2!%3Dzz%3B%2C&f=&hl=en_US';
		
		$useragent = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36';
		
		$header = array(
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3!",
			//"Accept-Encoding: gzip,deflate,sdch",
			"Accept-Encoding: deflate,sdch",
			"Accept-Language: en-US,en;q=0.8",
			"Keep-Alive: 300",
			"Host: www.google.com");
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch, CURLOPT_NOBODY, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_COOKIE, $this->_gaCookie);
		curl_setopt($ch, CURLOPT_URL, $url);
		
		// excute
		$result = array('error'=>false);
		$response = curl_exec($ch);
		$error = curl_error($ch);
		$curl_errno = curl_errno($ch);
		if (empty($error)) {
			$result['errno'] = 0;
			$responeInfo = curl_getinfo($ch);
			if ($responeInfo['http_code'] == 200) {
				$contenttype = $responeInfo['content_type'];
				if (strpos($contenttype, 'text/javascript;') === FALSE) {
					$result['errno'] = 1000;
				} else {
					foreach ($this->_rtOutputs as $key => $value) {
						 $response = str_replace($value, $key, $response);
					}
					$result['value'] = $response;
					$result['timestamp'] = time();
				}
			} else {
				$result['errno'] = 1001;
			}
			$result['error'] = $result['errno'] && !empty(self::$_CURL_ERROR_CODE_EXT[$result['errno']])?
					self::$_CURL_ERROR_CODE_EXT[$result['errno']] : 
					($result['errno']?$result['errno']:null);
		} else {
			$result['error'] = $error;
			$result['errno'] = $curl_errno;
		}
		curl_close($ch);
		
		return $result;
	}
	
	public function getGAData($mediaId,$realTimeKey){
		// try get data from cache
		$jsonData = $this->readFromMemcached(self::KEY_DATA_PREFIX.$mediaId);
		if(empty($jsonData)){
			// put old value for other process
			if (isset($this->_oldDataList[$mediaId])) {
				$this->writeToMemcached(self::KEY_DATA_PREFIX.$mediaId, $this->_oldDataList[$mediaId], 5);
			}
			
			//get cookie
			$this->_gaCookie = GACookie::getCookie();
			GAUtils::echoStr($this->_gaCookie);
			
			$result = $this->realTimeCurl($realTimeKey);
			if($result['errno'] || array_key_exists($result['errno'], self::$_CURL_ERROR_CODE_EXT)){
				if($result['errno'] == 1000)
					$this->_gaCookie = GACookie::getCookie(true);

				$result = $this->realTimeCurl($realTimeKey);
				GAUtils::echoStr($result);
			}
			
			if(!$result['errno']){
				$this->writeToMemcached(self::KEY_DATA_PREFIX.$mediaId, $result, 5);
				$this->_oldDataList[$mediaId] = $result;
			}
			return $result;
			
		}
		return $jsonData;
	}
		
	public function gaAccount(){
		$rtOutput = $this->readFromMemcached(self::KEY_RTOUTPUT);
		if(empty($rtOutput)){
			$rtOutput = \app\modules\api\ga\models\GaAccount::find()->where(['acount_id'=> 1])->one();
			if(empty($rtOutput)){
				throw new \yii\base\Exception('Can not find any account');
			}
			$this->_rtOutputs = array(
				'rt_pvs_per_minute'=>$rtOutput->rt_pvs_per_minute,
				'rt_pvs_per_second'=>$rtOutput->rt_pvs_per_second,
				'rt_pvs_right_now'=>$rtOutput->rt_pvs_right_now,
				'rt_active_pages'=>$rtOutput->rt_active_pages,
				'rt_social_traffic'=>$rtOutput->rt_social_traffic,
				'rt_referrals'=>$rtOutput->rt_referrals,
				'rt_keywords'=>$rtOutput->rt_keywords,
				'rt_locations'=>$rtOutput->rt_locations
			);
			$this->writeToMemcached(self::KEY_RTOUTPUT, $this->_rtOutputs,3600*24*7);
		} else {
			$this->_rtOutputs = $rtOutput;
		}
	}
}
?>