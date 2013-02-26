<?php namespace Regulus\Exterminator;

/*----------------------------------------------------------------------------------------------------------
	Exterminator
		A simple debugging composer package for Laravel 4 that allows you to print data on the screen
		depending on whether a 'debug' cookie is present.

		created by Cody Jassman
		last updated on February 22, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class Exterminator {

	public static $shownCss = false;
	public static $shownJs = false;

	public static $debugData = array();

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
		return static::x('Exterminator enabled!');
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
		echo static::css();
		echo static::openPanel();

		echo '<pre>';
		var_dump($data);
		echo '</pre></div>' . "\n";
	}

	/**
	 * Adds debug data to debug data array
	 *
	 * @param  mixed    $data
	 */
	public static function a($data)
	{
		static::$debugData[] = $data;
	}

	/**
	 * Prepares debug data for display.
	 *
	 */
	public static function display()
	{
		$authorized = static::authorized();
		if (!is_bool($authorized)) return $authorized;

		if ($authorized && !empty(static::$debugData)) {
			echo static::css();
			echo static::openPanel();
			echo '<a href="" class="toggle-debug">Hide</a>' . "\n";
			foreach (static::$debugData as $data) {
				echo '<pre>' . "\n";
				echo static::varDump($data);
				echo '</pre>' . "\n";
			}
			echo '</div>' . "\n";
			echo static::js();
		}
	}

	/**
	 * Gets a string from a var_dump() and formats HTML special characters so that strings show exact data
	 * and no data erroneously renders HTML tags.
	 *
	 * @param  mixed   $var
	 *
	 */
	public static function varDump($var = false)
	{
		ob_start();
    	var_dump($var);
    	$string = ob_get_clean();
    	return static::entities($string);
	}

	/**
	 * Creates the debug panel opening markup.
	 *
	 * @return string
	 */
	private static function openPanel()
	{
		$html = '<div class="debug"><div class="debug-bg"></div>' . "\n";
		$html .= '<h1>Exterminator</h1>' . "\n";
		return $html;
	}

	/**
	 * Creates the CSS for the debug panel.
	 *
	 * @return string
	 */
	private static function css()
	{
		if (!static::$shownCss) {
			$html = '<style type="text/css">' . "\n";;
			$html .= 'div.debug { z-index: 10800; position: fixed; top: 80px; right: 36px; min-width: 560px; max-width: 980px; min-height: 24px;';
			$html .= 'font-family: Arial, Helvetica, sans-serif; }';
			$html .= 'div.debug div.debug-bg { position: absolute; opacity: 0.9; width: 100%; height: 100%; background-color: #000;';
			$html .= '-moz-border-radius: 8px; -webkit-border-radius: 8px; border-radius: 8px; }';
			$html .= 'div.debug:hover div.debug-bg { opacity: 0.97; }';
			$html .= 'div.debug a.toggle-debug { position: absolute; top: -14px; right: 8px; padding: 2px 3px; ';
			$html .= 'background-color: #700; color: #fff; font-size: 11px; font-weight: bold; text-decoration: none; cursor: pointer; ';
			$html .= 'border: 1px solid #888; -moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px; }';
			$html .= 'div.debug a.toggle-debug.show { background-color: #060; }';
			$html .= 'div.debug a.toggle-debug:hover { background-color: #fff; color: #000; }';
			$html .= 'div.debug h1 { position: absolute; top: -8px; left: 0; background: none; color: #700; ';
			$html .= 'font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: normal; font-style: italic; ';
			$html .= 'text-shadow: #000 0 0 5px; }';
			$html .= 'div.debug pre { position: relative; margin: 12px 6px; padding: 10px; ';
			$html .= 'background: none; border: 1px solid #555; color: #fff; }';
			$html .= '</style>' . "\n";
			return $html;
		}
	}

	/**
	 * Creates the CSS for the debug panel.
	 *
	 * @return string
	 */
	private static function js()
	{
		if (!static::$shownJs) {
			$html = '<script type="text/javascript">' . "\n";
			$html .= '$("a.toggle-debug").click(function(e){' . "\n";
			$html .= 'e.preventDefault();' . "\n";
			$html .= 'if ($(this).text() == "Hide") {' . "\n";
			$html .= '$(this).parents("div.debug").children("pre").fadeOut("fast"); $(this).addClass("show").text("Show");' . "\n";
			$html .= '} else { $(this).parents("div.debug").children("pre").fadeIn("fast"); ';
			$html .= '$(this).removeClass("show").text("Hide"); }' . "\n";
			$html .= '});' . "\n";
			$html .= '</script>' . "\n";
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

}