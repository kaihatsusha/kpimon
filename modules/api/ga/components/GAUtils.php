<?php

/**
 * Author : ngo nhan
 */

namespace app\modules\api\ga\components;

Class GAUtils {

	private static function addField($attr_array) {
		$name = '';
		$value = '';
		foreach ($attr_array as $attr) {
			$attr = explode('=', trim($attr));

			if ($attr[0] == 'name') {
				$name = trim($attr[1], '"');
			} elseif ($attr[0] == 'value') {
				$value = trim($attr[1], '"');
			}
		}
		return array('name' => $name, 'value' => $value);
	}

	static function parseHidden($html) {
		//preg_match_all ("/<b>(.*)<\/b>/U", $userinfo, $pat_array);
		$html = preg_replace("/[\r\n]{2,}/", " ", $html);
		//var_dump( $userinfo);
		//preg_match_all ("/(<input.*type=\"hidden\"[^>]*>)*/", $userinfo, $pat_array);
		//preg_match_all ("/<input([^\=]*\=\"[^\"]*).*>/g", $userinfo, $pat_array);

		preg_match_all("/<input[^>]*type=\"hidden\"[^>]*>/", $html, $pat_array);
		//preg_match_all ("/(<input(\s*[^>=]*=\"[^\"]*\")+)/", $userinfo, $pat_array);
		//var_dump( $pat_array);
		$hiddenFields = array();
		foreach ($pat_array[0] as $inputTag) {
			preg_match_all('/(\s[^=]+=\"[^\"]+\")+/U', $inputTag, $attr_array);
			$newField = self::addField($attr_array[0]);
			//var_dump($newField);
			$hiddenFields[$newField['name']] = $newField['value'];
			//var_dump($inputTag, $attr_array[0]);
		}
		return $hiddenFields;
	}

	static function parseCookie($cookieStr) {
		preg_match_all('/Cookie:\s(([^=;\s]+=[^\s]+\s)+)/m', $cookieStr, $matches);
		return isset($matches[1][0])?$matches[1][0]:'';
	}

	static function parseBody($html) {
		preg_match("/<body.*\/body>/s", $html, $pat_array);
		return $pat_array;
	}

	static function echoDiv($str) {
		//var_dump($str[0]); return 0;
		if (!isset($_GET['debug']))
			return;
		$div = '<div style="height:500px;overflow:auto; width:100%"><pre>#</pre></div>';
		if (!empty($str))
			if (is_array($str))
				foreach ($str as $st)
					echo str_replace('#', htmlspecialchars($st), $div);
			else
				echo str_replace('#', htmlspecialchars($str), $div);
		else
			echo str_replace('#', 'No BODY', $div);
	}

	static function echoStr($str) {
		if (!isset($_GET['debug']))
			return;
		if (is_array($str)) {
			foreach ($str as $st) {
				print_r($st);
			}
		} else {
			echo $str;
		}
	}

	static function getLoginError($html) {

		//$matches = array();
		$ret = preg_match("/<span[^>]*class=\"error-msg\"[^>]*>([^<]*)<\/span>/s", $html, $matches);
		$last = array_pop($matches);
		if ($ret && !empty($matches) && !empty($last)) {
			//var_dump($matches);die;
			self::echoSt('<strong style="color:red">' . $last . '</strong>');
			return true;
		}
		return false;
	}

	static function streamPing($id = null) {
		echo $id?"event: ping-$id\n":"event: ping\n";
		$curDate = date(DATE_ISO8601);
		echo 'data: {"time": "' . $curDate . '"}';
		echo "\n\n";
	}

	static function streamHasData($data,$id = null) {
		echo $id?"event: hasdata-$id\n":"event: hasdata\n";
		echo 'data: ' . $data;
		echo "\n\n";
	}

	static function streamError($error, $flush = false,$id = null) {
		echo $id?"event: error-$id\n":"event: error\n";
		if (is_array($error)) {
			if (isset($error['errno']))
				echo 'data: ErrorCode:' . $error['errno'];
			if (isset($error['error']))
				echo 'data: ErrorMsg:' . $error['error'];
			if (!isset($error['error']) && !isset($error['errno']))
				echo 'data: Unknow Error';
		} else
			echo 'data: Error:' . $error;
		echo "\n\n";

		if (!$flush)
			return;

		ob_flush();
		flush();
	}

	static function streamWarn($warn, $flush = true,$id = null) {
		echo $id?"event: warn-$id\n":"event: warn\n";
		echo 'data: ' . $warn;
		echo "\n\n";

		if ($flush) {
			ob_flush();
			flush();
		}
	}

}

// end class Utils

//$test = <<<COD
//fdfd Cookie: APISID=8Hgsh66s83Onxh9c/A6bSgtOWJY8E63ma8; HSID=AA8ObpsFgRZWIuDob; NID=69=NOFy_s0E6pIyHIH4ewu0MlWROJM7s_C6CMbGeG-eOZP8Dj8mfQv0IeJp4L4eHhLnN_GDzP5dxrrob_eiBi17YrRN4z3a88hxfMYYayQUa-QSRZtj6J_kuF70LiMUowyBPapQC7qu3pQdnofSbAH3EmU3N92DYicRnmZQyy0hYvS-FvhCw0EZKbdRmFyj; SAPISID=NYXOqMk2ZEZ72ClS/AEi7CzsgxgH9AUgFK; SID=DQAAAF4BAACME6Nr3vPKjXp7ocqBcr7DMUdAVNV9Vs04e4L9BBa6c64FG1QMT84tCgVS0bWyA0rhutTLoD4hR-LxeCx0iOyj6nOjy5VIMmn2S2WZ_hvBXxOpzqo_NcMw1Tx2XBLvTLW3RrvRxB54EaXaDdaa73SFSO3xX69x98CrB1Yv5wtke9ls1GfU1-JcCltBDcT4xKtfGe6JATHOl9gYsTofLE1aDezenv_ILlpyw3rQEPsBr2t2JcGIFp-CdFxyjhLKC6g3mUGbL57DltcURSQcyd6V3rTsKKBKJVMHHHW48BjH0Lf4tRnwGA4hlY5zI8qNDqAVuP1mhpcMDqdrkLFm7JaKmq4UqKCZlbkbYK1Pz9YJtT4UWSETNpupMqin0uUrg-dUPWRTMFq1dszRpYoD6APFb2tnU19E1KKo0PRfs7-3qMPflKKYrB621JAw-aMlBsphQE8NmaqgkCTJQ_1OqlU_; SSID=As9Pst-C3uzdYwCn- Accept: 
//COD;

//preg_match("/<span[^>]*>([^<]*)<\/span>/s",$test, $attr_array);
//preg_match_all('/Cookie:\s(([^=]+=[^\s]+\s)+)/m', $test, $matches);
//echo($matches[1][0]);
//var_dump(Utils::getLoginError($test));
