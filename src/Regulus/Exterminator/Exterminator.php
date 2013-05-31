<?php namespace Regulus\Exterminator;

/*----------------------------------------------------------------------------------------------------------
	Exterminator
		A simple debugging composer package for Laravel 4 that allows you to print data on the screen
		depending on whether a 'debug' cookie is present.

		created by Cody Jassman
		last updated on May 30, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class Exterminator {

	public static $shownCSS    = false;
	public static $shownJS     = false;
	public static $debugData   = array();
	public static $varDumpHTML = "";

	/**
	 * Dumps data to the screen and exits.
	 *
	 * @param  mixed    $data
	 */
	public static function authorized()
	{
		$code = Config::get('exterminator::code');
		if (Request::is('*/'.$code)) {
			$uri = str_replace('/'.$code, '', str_replace(URL::to('').'/', '', Request::url()));
			return Redirect::to($uri)->withCookie(Cookie::make('debug', true, 10800));
		}

		if (Cookie::get('debug')) return true;
		return false;
	}

	/**
	 * Dumps data to the screen and exits.
	 *
	 * @param  mixed    $data
	 */
	public static function x($data)
	{
		$authorized = static::authorized();
		if (!is_bool($authorized)) return $authorized;

		if ($authorized) {
			static::dump($data);
			exit;
		}
		return;
	}

	/**
	 * Used to enable Exterminator by running it with a predefined string.
	 *
	 */
	public static function e()
	{
		return static::x('Exterminator Enabled!');
	}

	/**
	 * Dumps data to the screen.
	 *
	 * @param  mixed    $data
	 */
	public static function o($data)
	{
		$authorized = static::authorized();
		if (!is_bool($authorized)) return $authorized;

		if ($authorized) static::dump($data);
	}

	/**
	 * Dumps data to the screen.
	 *
	 * @param  mixed    $data
	 */
	private static function dump($data)
	{
		$html = static::css();
		$html .= static::openPanel();

		$html .= '<pre>';
		$html .= static::varDump($data);
		$html .= '</pre></div></div>' . "\n";

		echo $html;
	}

	/**
	 * Adds debug data to debug data array. If you include get_defined_vars() as $definedVars, Exterminator will also
	 * be able to list the names of your variables above the variable dumps. This may not work in all cases.
	 *
	 * @param  mixed    $data
	 * @param  array    $definedVars
	 */
	public static function a($data, &$definedVars = false)
	{
		if ($definedVars) {
			$varName = static::varName($data, $definedVars);
			static::$debugData[$varName] = $data;
		} else {
			static::$debugData[] = $data;
		}
	}

	/**
	 * Prepares debug data for display.
	 *
	 * @return string
	 */
	public static function display()
	{
		$authorized = static::authorized();
		if (!is_bool($authorized)) return $authorized;

		if ($authorized && !empty(static::$debugData)) {
			$html = static::css();
			$html .= static::openPanel(true);
			foreach (static::$debugData as $varName => $data) {
				$html .= '<div class="var-dump">' . "\n";
				if (!is_numeric($varName)) {
					$html .= "<h3>$".$varName."</h3>";
				}
				$html .= '<pre>' . "\n";
				$html .= static::varDump($data);
				//$html .= '</pre>' . "\n";
				$html .= '</div>' . "\n";
			}
			$html .= '</div></div>' . "\n";
			$html .= static::js();

			echo $html;
		}
	}

	/**
	 * Creates a color-coded HTML dump of a variable or creates a string from a var_dump() and formats
	 * HTML special characters so that strings show exact data and no data erroneously renders HTML tags.
	 *
	 * @param  mixed   $var
	 * @param  boolean $html
	 * @return string
	 */
	public static function varDump($var = false, $html = true)
	{
		if ($html) {
			return static::varDumpHTML($var);
		} else {
			ob_start();
			var_dump($var);
			$string = ob_get_clean();
			return '<pre>'. "\n" . static::entities($string). "\n" . '</pre>' . "\n";
		}
	}

	/**
	 * Creates a color-coded HTML dump of a variable.
	 *
	 * @param  mixed   $var
	 * @return string
	 */
	public static function varDumpHTML($var = false)
	{
		static::$varDumpHTML = '';
		static::cycleVarDumpHTML($var);
		return static::$varDumpHTML;
	}

	/**
	 * Cycles through array or object and creates color-coded HTML along the way.
	 *
	 * @param  mixed   $var
	 */
	private static function cycleVarDumpHTML($var = false)
	{
		if (is_object($var) || is_array($var)) {
			if (is_object($var)) {
				$var = (array) $var;
				$type = "object";
			} else {
				$type = "array";
			}

			static::$varDumpHTML .= $type.'(<span class="var-length">'.count($var).'</span>) {<div class="var-area">';
			foreach ($var as $key => $value) {
				if (is_int($key)) {
					$type = "numeric";
					$quotes = '';
				} else {
					$type = "string";
					$quotes = '"';
				}
				static::$varDumpHTML .= '<span class="var-key">['.$quotes.'<span class="var-'.$type.'">'.$key.'</span>'.$quotes.']</span> => ';
				if (is_object($value) || is_array($value)) {
					static::cycleVarDumpHTML($value);
				} else {
					static::cycleNonArrayVarDumpHTML($value);
				}
			}
			static::$varDumpHTML .= '</div><!-- /var-area -->'."\n".'}<br />';
		} else {
			static::cycleNonArrayVarDumpHTML($var);
		}
	}

	/**
	 * Creates color-coded HTML for booleans, integers, floats, and strings.
	 *
	 * @param  mixed   $var
	 */
	private static function cycleNonArrayVarDumpHTML($var = false)
	{
		$quotes = ""; $prefix = "";
		if (is_bool($var)) {
			$var = $var ? 'true' : 'false';
			$type = "bool-".$var;
		} else if (is_int($var) || is_float($var)) {
			$type = "numeric";
		} else {
			$type = "string";
			$quotes = '"';
			$prefix = $type.'(<span class="var-length">'.strlen($var).'</span>) ';
		}
		static::$varDumpHTML .= $prefix.$quotes.'<span class="var-'.$type.'">'.$var.'</span>'.$quotes.'<br />';
	}

	/**
	 * Creates the debug panel opening markup.
	 *
	 * @param  boolean  $var
	 * @return string
	 */
	private static function openPanel($hideButton = false)
	{
		$html = '<div class="debug"><div class="debug-bg"></div>' . "\n";
		$html .= '<h1>Exterminator</h1>' . "\n";
		if ($hideButton) $html .= '<a href="" class="toggle-debug">Hide</a>' . "\n";
		$html .= '<div class="area">';
		return $html;
	}

	/**
	 * Creates the CSS for the debug panel.
	 *
	 * @return string
	 */
	private static function css()
	{
		if (!static::$shownCSS) {
			$css = file_get_contents('../vendor/regulus/exterminator/public/assets/css/exterminator.css');
			$html = '<style type="text/css">' . "\n" . $css . "\n" . '</style>' . "\n";
			return $html;
		}
	}

	/**
	 * Creates the Javascript for the debug panel.
	 *
	 * @return string
	 */
	private static function js()
	{
		if (!static::$shownJS) {
			$js = file_get_contents('../vendor/regulus/exterminator/public/assets/js/exterminator.js');
			$html = '<script type="text/javascript">' . "\n" . $js . "\n" . '</script>' . "\n";
			return $html;
		}
	}

	/**
	 * Convert HTML characters to entities.
	 *
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}

	/**
	 * Get a variable's name if possible (and if defined variables was passed).
	 *
	 * @param  mixed   $var
	 * @param  array   $definedVars
	 * @return string
	 */
	public static function varName(&$var, &$definedVars)
	{
		foreach ($definedVars as $varName => $value) {
			if ($value === $var) return $varName;
		}
		return false;
	}

}