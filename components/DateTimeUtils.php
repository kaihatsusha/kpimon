<?php
namespace app\components;

use Yii;
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
	const FM_VIEW_DATE_WD = 'Y-m-d (D)';
	
	const FN_KEY_GETDATE_YEAR = 'year';					// A full numeric representation of a year, 4 digits (Examples: 1999 or 2003)
	const FN_KEY_GETDATE_MONTH_INT = 'mon';				// Numeric representation of a month (1 through 12)
	const FN_KEY_GETDATE_MONTH_STR = 'month';			// A full textual representation of a month, such as January or March (January through December)
	const FN_KEY_GETDATE_HOURS = 'hours';				// Numeric representation of hours [0 to 23]
	const FN_KEY_GETDATE_MINUTES = 'minutes';			// Numeric representation of minutes [0 to 59]
	const FN_KEY_GETDATE_SECONDS = 'seconds';			// Numeric representation of seconds [0 to 59]
	const FN_KEY_GETDATE_DAYSOFYEAR = 'yday';			// Numeric representation of the day of the year (0 through 365)
	const FN_KEY_GETDATE_DAYSOFMONTH = 'mday';			// Numeric representation of the day of the month [1 to 31]
	const FN_KEY_GETDATE_DAYSOFWEEK_INT = 'wday';		// Numeric representation of the day of the week [0 (for Sunday) through 6 (for Saturday)]
	const FN_KEY_GETDATE_DAYSOFWEEK_STR = 'weekday';	// A full textual representation of the day of the week (Sunday through Saturday)
	const FN_KEY_GETDATE_TOTAL_SECONDS = 0;				// Seconds since the Unix Epoch, similar to the values returned by time() and used by date(). (System Dependent, typically -2147483648 through 2147483647.)
	
	private static $DATE_FORMAT;
	
	private static function getDateFormatInstall($language='') {
		//$lang = empty($language) ? Yii::app()->language : $language;
		$lang = empty($language) ? 'ja' : $language;
		
		// init value
		if (is_null(self::$DATE_FORMAT)) {
			self::$DATE_FORMAT = [
				'en'=>[
					'short'=>['php'=>'m{0}d{0}Y','jui'=>'MM{0}dd{0}yyyy','pattern'=>'/^\d{2}{0}\d{2}{0}\d{4}$/'], // 09/03/2014 (Sep 3rd 2014)
					'weekday'=>[
						['text-color'=>'text-red'],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>'text-blue']
					] // 0 (for Sunday) through 6 (for Saturday)
				],
				'ja'=>[
					'short'=>['php'=>'Y{0}m{0}d','jui'=>'yyyy{0}MM{0}dd','pattern'=>'/^\d{4}{0}\d{2}{0}\d{2}$/'], // 2014/09/03 (Sep 3rd 2014)
					'weekday'=>[
						['text-color'=>'text-red'],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>'text-blue']
					] // 0 (for Sunday) through 6 (for Saturday)
				],
				'vi'=>[
					'short'=>['php'=>'d{0}m{0}Y','jui'=>'dd{0}MM{0}yyyy','pattern'=>'/^\d{2}{0}\d{2}{0}\d{4}$/'], // 03/09/2014 (Sep 3rd 2014)
					'weekday'=>[
						['text-color'=>'text-red'],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>''],
						['text-color'=>'text-blue']
					] // 0 (for Sunday) through 6 (for Saturday)
				]
			];
		}
		
		if (isset(self::$DATE_FORMAT[$lang])) {
			return self::$DATE_FORMAT[$lang];
		} else {
			throw new Exception(Yii::t('DateTimeUtils', 'No date format is supported for this Language'));
		}
	}
	
	/**
	 * get DateFormat
	 * @param string $usefor
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 * @throws Exception
	 */
	public static function getDateFormat($usefor='php', $language='', $width = 'short', $split = '-') {
		$dateFormatInstall = self::getDateFormatInstall($language);
		
		if (isset($dateFormatInstall[$width][$usefor])) {
			$dateformat = $dateFormatInstall[$width][$usefor];
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
	 * @return string datetime with new format
	 */
	public static function formatDateTimeFromDB($datetime, $format) {
		$dt = self::getDateTimeFromDB($datetime);
		return $dt->format($format);
	}

	/**
	 * convert datetime (DB-string) to DateTime
	 * @param String $datetime
	 * @return DateTime
	 */
	public static function getDateFromDB($datetime) {
		$dt = \DateTime::createFromFormat(self::FM_DB_DATE, $datetime);
		return $dt;
	}
	
	/**
	 * get datetime from database
	 * @param string $datetime: value from database (EX: Y-m-d)
	 * @param string $format: new format
	 * @return string datetime with new format
	 */
	public static function formatDateFromDB($datetime, $format) {
		$dt = self::getDateFromDB($datetime);
		return $dt->format($format);
	}
	
	/**
	 * get Now as DateTime
	 * @param String $informat
	 * @param String $outformat
	 * @return DateTime
	 */
	public static function getNow($informat = null, $outformat = null) {
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
	 * @throws Exception
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
	 * @throws Exception
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
			case 'string':
				$intervalObj = new \DateInterval($interval);
				$result->add($intervalObj);
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
	 * format html for date
	 * @param String $datetime
	 * @param String $df
	 * @param mixed $htmlOpts [tag=>'span', class=>'abc']
	 * @return String
	 */
	public static function htmlDateFormat($datetime, $df, $htmlOpts = false) {
		if ($htmlOpts === false) {
			return $datetime;
		}
		
		$dt = getdate(self::parse($datetime, $df)->getTimestamp());
		$daysweek = $dt[self::FN_KEY_GETDATE_DAYSOFWEEK_INT];
		$dateFormatInstall = self::getDateFormatInstall();
		$textcolor = $dateFormatInstall['weekday'][$daysweek]['text-color'];
		
		$tag = isset($htmlOpts['tag']) ? $htmlOpts['tag'] : 'span';
		$fulltag = '<' . $tag .' class="{0}">{1}';
		$fulltag .= '</' . $tag .'>';
		$css = (isset($htmlOpts['class']) ? $htmlOpts['class'] : '') . ' ' . $textcolor;
		
		return StringUtils::format($fulltag, [$css, $datetime]);
	}
	
	/**
	 * format html for date
	 * @param string $datetime: value from database (EX: Y-m-d)
	 * @param string $format: new format
	 * @param mixed $htmlOpts [tag=>'span', class=>'abc']
	 * @return string
	 */
	public static function htmlDateFormatFromDB($datetime, $format, $htmlOpts = false) {
		$date = \DateTime::createFromFormat(self::FM_DB_DATE, $datetime);
		$datestr = $date->format($format);
		
		if ($htmlOpts === false) {
			return $datestr;
		}
		
		$dt = getdate($date->getTimestamp());
		$daysweek = $dt[self::FN_KEY_GETDATE_DAYSOFWEEK_INT];
		$dateFormatInstall = self::getDateFormatInstall();
		$textcolor = $dateFormatInstall['weekday'][$daysweek]['text-color'];
		
		$tag = isset($htmlOpts['tag']) ? $htmlOpts['tag'] : 'span';
		$fulltag = '<' . $tag .' class="{0}">{1}';
		$fulltag .= '</' . $tag .'>';
		$css = (isset($htmlOpts['class']) ? $htmlOpts['class'] : '') . ' ' . $textcolor;
		
		return StringUtils::format($fulltag, [$css, $datestr]);
	}
}
?>