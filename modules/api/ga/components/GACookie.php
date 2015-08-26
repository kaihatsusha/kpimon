<?php

/*
 * Author: Nhan
 */

namespace app\modules\api\ga\components;

use app\modules\api\ga\components\GAUtils;
use Yii;
/**
 * Description of GACookie
 *
 * @author slen
 */
class GACookie {

	const KEY_COOKIE = '__GA__Cookie';
	const RUNNING_KEY = '__GA__GET__COOKIE__RUNNING';
	
	public static function getRequestCookie($realTimeKey = 'a46049337w89311011p92802096') {
		$url = 'https://www.google.com/analytics/realtime/realtime/getData?';
		$url .= 'key=' . $realTimeKey;
		$url .= '&ds=' . $realTimeKey;
		$url .= '&pageId=RealtimeReport%2Frt-overview&q=t%3A0%7C%3A1%7C%3A0%3A%2Ct%3A33%7C%3A1%7C%3A5%3A%2Cot%3A0%3A0%3A4%3A%2Cot%3A0%3A0%3A3%3A%2Ct%3A7%7C%3A1%7C%3A5%3A6%3D%3DREFERRAL%3B%2Ct%3A7%7C%3A1%7C%3A5%3A6%3D%3DSOCIAL%3B%2Ct%3A10%7C%3A1%7C%3A10%3A%2Ct%3A18%7C%3A1%7C%3A10%3A%2Cg%3A4%7C5%7C2%7C1%7C%3A1%7C%3A10%3A2!%3Dzz%3B%2C&f=&hl=en_US';

		$useragent = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36';

		$header = array(
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3!",
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
		//curl_setopt($ch, CURLOPT_COOKIE, $this->_gaCookie);
		curl_setopt($ch, CURLOPT_URL, $url);

		$cookie_file = Yii::getAlias('@runtime') . '/' . 'cookie1.txt';
		//curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);

		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		// excute
		$result = array('error' => false);
		$response = curl_exec($ch);
		GaUtils::echoDiv($response);
		$error = curl_error($ch);
		if (empty($error)) {
			$responeInfo = curl_getinfo($ch);
			//var_export($responeInfo);
			if ($responeInfo['http_code'] == 200 && $request_cookies = curl_getinfo($ch)) {
				$request_cookies = $request_cookies['request_header'];
				$result['cookie'] = GaUtils::parseCookie($request_cookies);
				if (empty($result['cookie']))
					$result['error'] = 'Cookie empty';
				//var_dump('cookieeeeeeeeeeeee', $result['cookie']);die;
				//GaUtils::echoDiv($result['cookie']);die;
			} else {
				$result['error'] = 'Cookie recheck';
			}
		} else {
			$result['error'] = $error;
		}

		curl_close($ch);

		return $result;
	}
	
	private static $_curl_hd = false;
	
	private static function getCurl(){
		
		if (self::$_curl_hd === false){
			$useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36";

			$header = array(
				"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
				//"Referer: https://accounts.google.com/ServiceLogin?sacu=1&passive=1209600&acui=3",
				//"Referer: " . $ext['ref'],
				"Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3!",
				//"Accept-Encoding: gzip,deflate,sdch",
				"Content-Type: application/x-www-form-urlencoded",
				"Origin: https://accounts.google.com",
				"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36",
				"Accept-Encoding: deflate,sdch",
				"Accept-Language: en-US,en;q=0.8",
				"Keep-Alive: 300");
			//"Host: www.google.com");

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			//curl_setopt($ch, CURLOPT_NOBODY, 1);
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			
			$cookie_file = Yii::getAlias('@runtime') . '/' . 'cookie1.txt';
			//curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
			
			self::$_curl_hd = $ch;
			
		}
		
		return self::$_curl_hd;
		
	}

	private static function login($step = 0, $ext = array(), $url = 'https://accounts.google.com/ServiceLoginAuth') {
		$_ext = array();
		$_ext['ref'] = $url;
		
		//$url = 'https://accounts.google.com/ServiceLoginAuth';

		$ch = self::getCurl();
		
		curl_setopt($ch, CURLOPT_REFERER, $ext['ref']);
		
		curl_setopt($ch, CURLOPT_URL, $url);
		
		if ($step == 0) {
			// Options for first GET
			curl_setopt($ch, CURLOPT_COOKIESESSION, true); //reset session
			// Reset to default
			curl_setopt($ch, CURLOPT_POST, false); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, '');
		} elseif ($step == 1 && isset($ext['post_data'])) { // perpare post data
			// Reset to default
			curl_setopt($ch, CURLOPT_COOKIESESSION, false); //reset session
			// Options for POST
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $ext['post_data']);
		}



		// excute
		$result = array('error' => false);
		$response = curl_exec($ch);
		$error = curl_error($ch);
		if (empty($error)) {
			$_ext['curl_ok'] = true;

			$responeInfo = curl_getinfo($ch);
			//var_dump($responeInfo);
			$_ext['response_info'] = $responeInfo;

			//echo '<pre>'.htmlspecialchars(GaUtils::parseBody($response)).'</pre>';
			if ($responeInfo['http_code'] == 200) {// have body
				GaUtils::echoDiv(GaUtils::parseBody($response));
				GaUtils::echoDiv(GaUtils::parseHidden($response));
			}

			if ($step == 0) { // get login form
				$postData = GaUtils::parseHidden($response);
				//var_dump(GaUtils::parseHidden($response));
				//$_ext['post_data'] = http_build_query(GaUtils::parseHidden($response)).'&Email=ngonhan2k5%40gmail.com&Passwd=mitsamitsu';
				//$_ext['post_data'] = http_build_query(GaUtils::parseHidden($response)).'&Email=nhan.ngolechi%40washinengine.com&Passwd=kakaka123';
				if (!empty($postData))
					$_ext['post_data'] = http_build_query($postData) . '&' . http_build_query($ext['gacc']);
			}elseif ($step == 1) {
				$_ext['google_error'] = GaUtils::getLoginError($response);
			}

			return $_ext;
		}
	}

	public static function getNewCookie() {
		if (\Yii::$app->memCache->get(self::RUNNING_KEY)) {
			GAUtils::streamWarn('Another process is curling to get new cookie, Please wait');
			sleep(5);
			return false;
		}
		GAUtils::streamWarn('I am the choosen one, Belive me!');
		$ret = false;
		\Yii::$app->memCache->set(self::RUNNING_KEY, 1, 60);
		//self::$_running = true;
		GaUtils::echoStr("====================== GET ==========================...");
		$ext = array(
			'gacc' => Yii::$app->params['googleAcc'],
			'ref' => ''
		);

		$ext = self::login(0, $ext);
		//if (isset($ext['google_error']) && $ext['google_error']==false){
		if (isset($ext['curl_ok']) && !empty($ext['post_data'])) {
			GaUtils::echoStr("======================= POST =========================...");
			GaUtils::echoStr($ext['post_data']);

			sleep(4); // time for input username and pass :)

			$ext = self::login(1, $ext);
			if ($ext['google_error'] == false && $ext['response_info']['redirect_url']) {
				GaUtils::echoStr('OK:' . $ext['response_info']['redirect_url']);
				sleep(5);
				$loop = 2;
				while (!empty($ext['response_info']['redirect_url'])) {
					GaUtils::echoStr("====================== GET ==========================...");
					$ext = self::login($loop++, $ext, $ext['response_info']['redirect_url']); // check cookie
				}

				if (isset($ext['curl_ok'])) {
					$ret = self::getRequestCookie('a46049337w89311011p92802096');
					//GaUtils::echoStr($ret);
					//var_dump($ret);die;
				}
			}
		}
		//self::$_running = false;
		\Yii::$app->memCache->delete(self::RUNNING_KEY);
		return $ret;
	}

	public static function getCookie($getNew = false){
		//try get from cache
		$cookie = \Yii::$app->memCache->get(GACookie::KEY_COOKIE);
		if(empty($cookie) || $getNew){
			
			GAUtils::streamWarn('Can not get new cookie from memcache');
			
			//cookie empty, try get new cook and store in cache
			$cookie = self::getNewCookie();
			if (empty($cookie) || (isset($cookie['error']) && $cookie['error'] !== false) || empty($cookie['cookie'])) {
				if (isset($_GET['debug']))
					throw new \yii\base\Exception('Can not get new cookie');
				else
					GAUtils::streamError('Can not get new cookie');
			}else {
				$cookie = $cookie['cookie'];
				\Yii::$app->memCache->set(GACookie::KEY_COOKIE, $cookie);
			}
		}
		return $cookie;
	}
}
