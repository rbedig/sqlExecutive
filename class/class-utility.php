<?php 

/* 
* Class Utility
* Copyright (c) 2014 Ron Bedig <info@rbedig.com>
* GNU General Public License
* You may reproduce, alter and ditribute this software for free
*
* file: class-utility.php
*/

Class Utility
{
	/**
	 * ensure that a variable is set
	 * if the referenced variable is not set, it is set to the default
	 * returns the value
	 */
	public static function setMe(&$var, $default='')
	{
		// isset returns false if the variable is not set or NULL
		if (!isset($var))
		{
			return $default;
		}
		return $var;
	}

	/**
	 *  for a specific key returns the COOKIE value,
	 *  but only after updating the cookie from POST
	 */
	public static function cookieGetSet($key, $default='')
	{
		if (isset($_POST[$key])) {
			$me = $_POST[$key];
		}
		elseif (isset($_COOKIE[$key])) {
			$me = $_COOKIE[$key];
		}	
		else {
			$me = $default;
		}
		// set or renew cookie, 30 days
		setcookie($key, $me, (time() + (3600 * 24 * 30)));
		return $me;
	}

	/**
	 *  for a specific key returns the SESSION value,
	 *  but only after updating the session from POST
	 */
	public static function sessionGetSet($key, $default='')
	{
		if (isset($_POST[$key])) {
			$me = $_POST[$key];
		}
		elseif (isset($_SESSION[$key])) {
			$me = $_SESSION[$key];
		}	
		else {
			$me = $default;
		}
		// set or reset session value
		$_SESSION[$key] = $me;
		return $me;
	}	

	// debugging function
	public static function traceMe($variable)
	{
		echo '<p>*';
		print_r($variable);
		echo '*</p>';
	}
}