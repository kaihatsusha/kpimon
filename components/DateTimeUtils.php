<?php
namespace app\components;

use yii\base\Exception;
use app\components\StringUtils;

class DateTimeUtils {
	const FM_DB_DATETIME = 'Y-m-d H:i:s';
	const FM_DB_DATE = 'Y-m-d';
	const FM_DB_TIME = 'H:i:s';
    const FM_DEV_DATETIME = 'Ymd His';
	const FM_DEV_YM = 'Ym';
    const FM_DEV_DATE = 'Ymd';
    const FM_DEV_TIME = 'His';
	const FM_VIEW_DATE = 'Y-m-d';
	
	private static $DATE_FORMAT;
	
	/**
	 * get DateFormat
	 * @param string $usefor
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 * @throws CException
	 */
	public static function getDateFormat($usefor='php', $language='', $width = 'short', $split = '-') {
		// init value
		if (null === self::$DATE_FORMAT) {
			self::$DATE_FORMAT = [
				'en'=>[
					'short'=>['php'=>'m{0}d{0}Y','jui'=>'MM{0}dd{0}yyyy','pattern'=>'/^\d{2}{0}\d{2}{0}\d{4}$/'] // 09/03/2014 (Sep 3rd 2014)
				],
				'ja'=>[
					'short'=>['php'=>'Y{0}m{0}d','jui'=>'yyyy{0}MM{0}dd','pattern'=>'/^\d{4}{0}\d{2}{0}\d{2}$/'] // 2014/09/03 (Sep 3rd 2014)
				],
				'vi'=>[
					'short'=>['php'=>'d{0}m{0}Y','jui'=>'dd{0}MM{0}yyyy','pattern'=>'/^\d{2}{0}\d{2}{0}\d{4}$/'] // 03/09/2014 (Sep 3rd 2014)
				]
			];
		}
		
		//$lang = empty($language) ? Yii::app()->language : $language;
		$lang = empty($language) ? 'ja' : $language;
		if (isset(self::$DATE_FORMAT[$lang][$width][$usefor])) {
			$dateformat = self::$DATE_FORMAT[$lang][$width][$usefor];
			return StringUtils::format($dateformat, $split);
		} else {
			throw new Exception(Yii::t('DateTimeUtils', 'No date format is supported'));
		}
	}
	
	/**
	 * get DateFormat for JUI
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 */
	public static function getJuiDateFormat($language='', $width = 'short', $split = '-') {
		return self::getDateFormat('jui', $language, $width, $split);
	}
	
	/**
	 * get DateFormat for PHP
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 */
	public static function getPhpDateFormat($language='', $width = 'short', $split = '-') {
		return self::getDateFormat('php', $language, $width, $split);
	}
	
	/**
	 * get DateFormat for YII
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 */
	public static function getYiiDateFormat($language='', $width = 'short', $split = '-') {
		return self::getDateFormat('pattern', $language, $width, $split);
	}
	
	/**
	 * convert datetime (DB-string) to DateTime
	 * @param String $datetime
	 * @return DateTime
	 */
	public static function getDateTimeFromDB($datetime) {
		$dt = \DateTime::createFromFormat(self::FM_DB_DATETIME, $datetime);
		return $dt;
	}
	
    /**
	 * get datetime from database
	 * @param string $datetime: value from database (EX: Y-m-d H:i:s)
	 * @param string $format: new format
	 * @return mixed string OR DateTime
	 */
	public static function formatDateTimeFromDB($datetime, $format) {
		$dt = \DateTime::createFromFormat(self::FM_DB_DATETIME, $datetime);
		return $dt->format($format);
	}
	
	/**
	 * get Now as DateTime
	 * @param String $informat
	 * @param String $outformat
	 * @return DateTime
	 */
	public static function getNow($informat, $outformat) {
		$dt = new \DateTime();
		if (is_null($informat) || is_null($outformat)) {
			return $dt;
		}
		
		$dtf = $dt->format($informat);
		return \DateTime::createFromFormat($outformat, $dtf);
	}
	
	/**
	 * get Now as String
	 * @param String $format
	 * @return String
	 */
	public static function formatNow($format) {
		$dt = new \DateTime();
		return $dt->format($format);
	}
	
	/**
	 * parse datetime from String
	 * @param type $datetime
	 * @param type $informat
	 * @param type $outformat
	 * @return mixed String/DateTime
	 */
	public static function parse($datetime, $informat, $outformat = null) {
		$dt = \DateTime::createFromFormat($informat, $datetime);
		return is_null($outformat) ? $dt : $dt->format($outformat);
	}
	
	/**
	 * create datetime from Timestamp
	 * @param type $timestamp
	 * @param type $outformat
	 * @return mixed String/DateTime
	 */
	public static function createFromTimestamp($timestamp, $outformat = null) {
		$dt = new \DateTime();
		$dt->setTimestamp($timestamp);
		return is_null($outformat) ? $dt : $dt->format($outformat);
	}
	
	/**
	 * clone a DateTime
	 * @param type $datetime Integer or DateTime
	 * @return type DateTime
	 * @throws Exception
	 */
	public static function cloneDateTime($datetime) {
		$result = new \DateTime();
		$error = false;
		
		$type = gettype($datetime);
		switch ($type) {
			case 'integer':
				$result->setTimestamp($datetime);
				break;
			case 'object':
				if ($datetime instanceof \DateTime) {
					$result->setTimestamp($datetime->getTimestamp());
				} else {
					$error = true;
				}
				break;
			default:
				$error = true;
				break;
		}
		
		if ($error) {
			throw new Exception(Yii::t('DateTimeUtils', 'Unknown type'));
		}
		return $result;
	}
	
	/**
	 * create a DateTime with sub data
	 * @param mixed $datetime integer(recommended) OR DateTime
	 * @param DateInterval $interval EX: P10D
	 * @param string $format if you want to return a string, you should use this
	 * @param boolean $nochange
	 * @return mixed string OR DateTime base on '$format' - the new date time with sub data
	 * @throws CException
	 */
	public static function subDateTime($datetime, $interval, $format = null, $nochange = true) {
		$result = ($nochange || gettype($datetime) == 'integer') ? self::cloneDateTime($datetime) : $datetime;
		$error = false;
		
		$type = gettype($interval);
		switch ($type) {
			case 'object':
				if ($interval instanceof \DateInterval) {
					$result->sub($interval);
				} else {
					$error = true;
				}
				break;
			case 'string':
				$intervalObj = new \DateInterval($interval);
				$result->sub($intervalObj);
				break;
			default:
				$error = true;
				break;
		}
		
		if ($error) {
			throw new Exception(Yii::t('DateTimeUtils', 'Unknown type'));
		}
		return (null == $format) ? $result : $result->format($format);
	}
	
	/**
	 * create a DateTime with add data
	 * @param mixed $datetime integer(recommended) OR DateTime
	 * @param DateInterval $interval EX: P10D
	 * @param string $format if you want to return a string, you should use this
	 * @param boolean $nochange
	 * @return mixed string OR DateTime base on '$format' - the new date time with add data
	 * @throws CException
	 */
	public static function addDateTime($datetime, $interval, $format = null, $nochange = true) {
		$result = ($nochange || gettype($datetime) == 'integer') ? self::cloneDateTime($datetime) : $datetime;
		$error = false;
		
		$type = gettype($interval);
		switch ($type) {
			case 'object':
				if ($interval instanceof \DateInterval) {
					$result->add($interval);
				} else {
					$error = true;
				}
				break;
			default:
				$error = true;
				break;
		}
		
		if ($error) {
			throw new Exception(Yii::t('DateTimeUtils', 'Unknown type'));
		}
		return (null == $format) ? $result : $result->format($format);
	}
}
?>