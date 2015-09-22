<?php
namespace app\components;

class StringUtils {
	/**
	 * return new string with input template
	 * @param string $template: Hello {0} world !
	 * @param mixed $values: string-position {0} OR array-set of position {0}, {1}, ...
	 */
	public static function format($template, $values) {
		if (empty($template)) {
			return false;
		}
		
		$patterns = array();
		$replacements = array();
		$index = 0;
		if (is_array($values)) {
			foreach ($values as $key => $value) {
				$patterns[$index] = "/\{$key\}/";
				$replacements[$index] = $value;
				$index++;
			}
		} else {
			$patterns[$index] = "/\{$index\}/";
			$replacements[$index] = $values;
		}
		return count($patterns) > 0 ? preg_replace($patterns, $replacements, $template) : $template;
	}
}
?>