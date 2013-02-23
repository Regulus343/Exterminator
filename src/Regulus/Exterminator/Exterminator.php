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

		if (Cookie::get('debug')) {
			return true;
		}
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
	 * Dumps data to the screen.
	 *
	 * @param  mixed    $data
	 */
	public static function o($data)
	{
		$authorized = static::authorized();
		if (!is_bool($authorized)) return $authorized;

		if ($authorized) static::dump($data);
		return;
	}

	/**
	 * Dumps data to the screen.
	 *
	 * @param  mixed    $data
	 */
	private static function dump($data)
	{
		echo '<style type="text/css">';
		echo 'div.debug { z-index: 10800; position: absolute; top: 120px; right: 24px; }';
		echo 'div.debug div.debug-bg { position: absolute; opacity: 0.7; width: 100%; height: 100%; background-color: #000;';
		echo '-moz-border-radius: 8px; -webkit-border-radius: 8px; border-radius: 8px; }';
		echo 'div.debug:hover div.debug-bg { opacity: 1; }';
		echo 'div.debug pre { position: relative; padding: 16px; background: none; color: #fff; }';
		echo '</style>';
		echo '<div class="debug"><div class="debug-bg"></div><pre>';
		var_dump($data);
		echo '</pre></div>';
	}

	/**
	 * Used to enable Exterminator by running it with a predefined string.
	 *
	 */
	public static function e()
	{
		return static::x('Exterminator enabled!');
	}

}