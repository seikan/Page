<?php

/**
 * Page: A collection of useful page functions for PHP web application.
 *
 * Copyright (c) 2017 Sei Kan
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  2017 Sei Kan <seikan.dev@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @see       https://github.com/seikan/Page
 */
class Page
{
	const BOTH = 0;
	const GET = 1;
	const POST = 2;

	/**
	 * Get client IP address.
	 *
	 * @return string
	 */
	public function getClientIp()
	{
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		if (isset($_SERVER['X-Real-IP']) && filter_var($_SERVER['X-Real-IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $_SERVER['X-Real-IP'];
		}

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				return $ip;
			}
		}

		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Get current page URL.
	 *
	 * @return string
	 */
	public function getCurrentUrl()
	{
		return 'http'.(($this->isHttps()) ? 's' : '').'://'.$_SERVER['SERVER_NAME'].(('80' == $_SERVER['SERVER_PORT'] || ('443' == $_SERVER['SERVER_PORT'] && $this->isHttps())) ? '' : (':'.$_SERVER['SERVER_PORT'])).$_SERVER['REQUEST_URI'];
	}

	/**
	 * Get client user agent.
	 *
	 * @return string
	 */
	public function getUserAgent()
	{
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}

	/**
	 * Get page referer if available.
	 *
	 * @return string
	 */
	public function getReferer()
	{
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	}

	/**
	 * Check if current page is HTTPS.
	 *
	 * @return bool
	 */
	public function isHttps()
	{
		return (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO']) ? true : ((isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) ? true : false);
	}

	/**
	 * Rewrite a URL with new query string.
	 *
	 * @param string $url
	 * @param array  $queries
	 *
	 * @return string
	 */
	public function rewrite($url, $queries = [])
	{
		if (false === ($parser = @parse_url($url))) {
			return false;
		}

		if (!isset($parser['scheme'])) {
			return false;
		}

		if (isset($parser['query'])) {
			parse_str($parser['query'], $query);
			$queries = array_merge($query, $queries);
		}

		return $parser['scheme'].'://'.$parser['host'].((isset($parser['path'])) ? $parser['path'] : '').(!empty($queries) ? ('?'.http_build_query($queries)) : '');
	}

	/**
	 * Get value from a GET or POST request.
	 *
	 * @param string $key
	 * @param int    $method
	 * @param bool   $html
	 *
	 * @return string
	 */
	public function request($key, $method = self::BOTH, $html = true)
	{
		switch ($method) {
			case self::GET:
				return (isset($_GET[$key])) ? $this->strips($_GET[$key], $html) : null;
				break;

			case self::POST:
				return (isset($_POST[$key])) ? $this->strips($_POST[$key], $html) : null;
				break;

			default:
			case self::BOTH:
				return (isset($_GET[$key])) ? $this->strips($_GET[$key], $html) : ((isset($_POST[$key])) ? $this->strips($_POST[$key], $html) : null);
				break;
		}
	}

	/**
	 * Clean up string to display safely.
	 *
	 * @param string $text
	 * @param bool   $hmtl
	 *
	 * @return string
	 */
	private function strips($text, $html = true)
	{
		if (!is_array($text) && !is_object($text)) {
			return ($html) ? htmlentities(stripslashes(trim($text))) : stripslashes(trim($text));
		}

		foreach ($text as $key => $value) {
			(is_array($text)) ? $text[$key] = $this->strips($value, $html) : $text->{$key} = $this->strips($value, $html);
		}

		return $text;
	}
}
