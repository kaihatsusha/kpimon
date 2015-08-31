<?php
namespace app\components;

class DateTimeUtils {
	const FM_DB_DATETIME = 'Y-m-d H:i:s';
	const FM_DB_DATE = 'Y-m-d';
	const FM_DB_TIME = 'H:i:s';
    const FM_DEV_DATETIME = 'Ymd His';
    const FM_DEV_DATE = 'Ymd';
    const FM_DEV_TIME = 'His';
	const FM_VIEW_DATE = 'Y-m-d';
	
	private static $DATE_FORMAT;
	
	/**
	 * get DateFormat for JUI
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 */
	public static function getJuiDateFormat($language='', $width = 'short', $split = '/') {
		return self::getDateFormat('jui', $language, $width, $split);
	}
	
	/**
	 * get DateFormat for PHP
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 */
	public static function getPhpDateFormat($language='', $width = 'short', $split = '/') {
		return self::getDateFormat('php', $language, $width, $split);
	}
	
	/**
	 * get DateFormat for YII
	 * @param string $language
	 * @param string $width
	 * @param string $split
	 * @return string DateFormat
	 */
	public static function getYiiDateFormat($language='', $width = 'short', $split = '/') {
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
	 * @param String $format
	 * @return DateTime
	 */
	public static function getNow($format) {
		$dt = self::formatNow($format);
		return \DateTime::createFromFormat($format, $dt);
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
}
?>