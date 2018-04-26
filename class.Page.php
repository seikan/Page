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
	 * Array to store GET requests.
	 *
	 * @var array
	 */
	protected $get = [];

	/**
	 * Array to store POST requests.
	 *
	 * @var array
	 */
	protected $post = [];

	/**
	 * Array to store browser cookies.
	 *
	 * @var array
	 */
	protected $cookie = [];

	/**
	 * Array to store server variables.
	 *
	 * @var array
	 */
	protected $server = [];

	/**
	 * Initialize.
	 */
	public function __construct()
	{
		$this->get = $this->clean($_GET);
		$this->post = $this->clean($_POST);
		$this->cookie = $this->clean($_COOKIE);
		$this->server = $this->clean($_SERVER);
	}

	/**
	 * Get client IP address.
	 *
	 * @return string
	 */
	public function getClientIp()
	{
		if (isset($this->server['HTTP_CF_CONNECTING_IP']) && filter_var($this->server['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $this->server['HTTP_CF_CONNECTING_IP'];
		}

		if (isset($this->server['X-Real-IP']) && filter_var($this->server['X-Real-IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $this->server['X-Real-IP'];
		}

		if (isset($this->server['HTTP_X_FORWARDED_FOR'])) {
			$ip = trim(current(explode(',', $this->server['HTTP_X_FORWARDED_FOR'])));

			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				return $ip;
			}
		}

		return (isset($this->server['REMOTE_ADDR'])) ? $this->server['REMOTE_ADDR'] : null;
	}

	/**
	 * Get current page URL.
	 *
	 * @return string
	 */
	public function getCurrentUrl()
	{
		return 'http' . (($this->isHttps()) ? 's' : '') . '://' . $this->server['SERVER_NAME'] . (($this->server['SERVER_PORT'] == '80' || ($this->server['SERVER_PORT'] == '443' && $this->isHttps())) ? '' : (':' . $this->server['SERVER_PORT'])) . $this->server['REQUEST_URI'];
	}

	/**
	 * Get client user agent.
	 *
	 * @return string
	 */
	public function getUserAgent()
	{
		return isset($this->server['HTTP_USER_AGENT']) ? $this->server['HTTP_USER_AGENT'] : '';
	}

	/**
	 * Get page referer if available.
	 *
	 * @return string
	 */
	public function getReferer()
	{
		return isset($this->server['HTTP_REFERER']) ? $this->server['HTTP_REFERER'] : '';
	}

	/**
	 * Check if current page is HTTPS.
	 *
	 * @return bool
	 */
	public function isHttps()
	{
		return (isset($this->server['HTTP_X_FORWARDED_PROTO']) && $this->server['HTTP_X_FORWARDED_PROTO'] == 'https') ? true : ((isset($this->server['HTTPS']) && $this->server['HTTPS'] == 'on') ? true : false);
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
		if (($parser = @parse_url($url)) === false) {
			return false;
		}

		if (!isset($parser['scheme'])) {
			return false;
		}

		if (isset($parser['query'])) {
			parse_str($parser['query'], $query);
			$queries = array_merge($query, $queries);
		}

		return $parser['scheme'] . '://' . $parser['host'] . ((isset($parser['path'])) ? $parser['path'] : '') . (!empty($queries) ? ('?' . http_build_query($queries)) : '');
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
	public function request($key, $method = self::BOTH)
	{
		switch ($method) {
			case self::GET:
				return (isset($this->get[$key])) ? $this->get[$key] : null;
				break;

			case self::POST:
				return (isset($this->post[$key])) ? $this->post[$key] : null;
				break;

			default:
			case self::BOTH:
				return (isset($this->get[$key])) ? $this->get[$key] : ((isset($this->post[$key])) ? $this->post[$key] : null);
				break;
		}
	}

	/**
	 * Check if this is a form post request.
	 *
	 * @return bool
	 */
	public function isPost()
	{
		return !empty($_POST);
	}

	/**
	 * Get server variable by key.
	 *
	 * @param array|string $key
	 *
	 * @return array|string
	 */
	public function getVariable($key)
	{
		if (is_array($key)) {
			$values = [];
			foreach ($key as $value) {
				$values[$key] = (isset($this->server[$key])) ? $this->server[$key] : null;
			}

			return $values;
		}

		return (isset($this->server[$key])) ? $this->server[$key] : null;
	}

	/**
	 * Get cookie value by name.
	 *
	 * @param string $name
	 *
	 * @return string|null
	 */
	public function getCookie($name)
	{
		return (isset($this->cookie[$name])) ? $this->cookie[$name] : null;
	}

	/**
	 * Set cookie value.
	 *
	 * @param string $name
	 * @param string $value
	 * @param int    $day
	 * @param string $path
	 * @param string $domain
	 * @param bool   $secure
	 * @param bool   $httpOnly
	 */
	public function setCookie($name, $value = '', $day = 1, $path = '/', $domain = '', $secure = false, $httpOnly = true)
	{
		setcookie($name, $value, time() + (60 * 60 * 24 * $day), $path, $domain, $secure, $httpOnly);
		$this->cookie = $this->clean($_COOKIE);
	}

	/**
	 * Delete cookie by name.
	 *
	 * @param string $name
	 */
	public function deleteCookie($name)
	{
		setcookie($name, '', time() - 3600);
		$this->cookie = $this->clean($_COOKIE);
	}

	/**
	 * Clean up string to display safely.
	 *
	 * @param mixed $data
	 *
	 * @return array|string
	 */
	private function clean($data)
	{
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$data[$this->clean($key)] = $this->clean($value);
			}
		} else {
			$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
		}

		return $data;
	}
}
