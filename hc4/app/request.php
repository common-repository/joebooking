<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Request_
{
	public function getMethod();
	public function getIpAddress();
	public function getUserAgent();
	public function getCookie( $index );
	public function getReferrer();

	public function getPost();

	public function isAjax();
}

class HC4_App_Request implements HC4_Request_
{
	protected $_postPrefix = 'hc4-';

	public function __construct()
	{
		if( ! defined('WPINC') ){
			$this->_sanitizeGet();
			$this->_sanitizeCookie();
			$this->_sanitizePost();
		}
	}

	public function getPost()
	{
		$return = NULL;

		if( empty($_POST) ){
			$return = file_get_contents( 'php://input' );
		}
		else {
			$return = array();
			foreach( array_keys($_POST) as $key ){
				// if( substr($key, 0, strlen($this->_postPrefix)) != $this->_postPrefix ){
					// continue;
				// }
				// $my_key = substr($key, strlen($this->_postPrefix));
				// $return[$my_key] = $this->_fetch_from_array($_POST, $key);
				$return[$key] = $this->_fetch_from_array($_POST, $key);
			}
		}

		return $return;
	}

	public function getReferrer()
	{
		$return = NULL;
		if( isset($_SERVER['HTTP_REFERER']) && strlen($_SERVER['HTTP_REFERER']) ){
			$return = $_SERVER['HTTP_REFERER'];
		}
		return $return;
	}

	public function isAjax()
	{
		$return = FALSE;
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ){
			$return = TRUE;
		}
		return $return;
	}

	public function getMethod()
	{
		$return = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
		return $return;
	}

	public function getIpAddress()
	{
		$return = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		return $return;
	}

	public function getUserAgent()
	{
		$return = ( ! isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
		return $return;
	}

	public function getCookie( $index )
	{
		return $this->_fetch_from_array( $_COOKIE, $index );
	}

	protected function _fetch_from_array($array, $index = '')
	{
		if ( ! isset($array[$index])){
			return FALSE;
		}
		$return = $array[$index];
		if( ! is_array($return) ){
			$return = trim( $return );
		}
		return $return;
	}

	protected function _sanitizeGet()
	{
		if (is_array($_GET) AND count($_GET) > 0){
			foreach ($_GET as $key => $val){
				$_GET[$this->_cleanInputKeys($key)] = $this->_cleanInputData($val);
			}
		}
	}

	protected function _sanitizePost()
	{
		if (is_array($_POST) AND count($_POST) > 0){
			foreach ($_POST as $key => $val){
				if( substr($key, 0, strlen($this->_postPrefix)) !== $this->_postPrefix ){
					continue;
				}
				$_POST[$this->_cleanInputKeys($key)] = $this->_cleanInputData($val);
			}
		}
	}

	protected function _sanitizeCookie()
	{
		if (is_array($_COOKIE) AND count($_COOKIE) > 0){
			unset($_COOKIE['$Version']);
			unset($_COOKIE['$Path']);
			unset($_COOKIE['$Domain']);

			foreach ($_COOKIE as $key => $val){
				$_COOKIE[$this->_cleanInputKeys($key)] = $this->_cleanInputData($val);
			}
		}
	}

	protected function _cleanInputKeys( $str )
	{
		if ( ! preg_match("/^[a-z0-9:_\/\-\~]+$/i", $str)){
			exit('Disallowed Key Characters on: ' . '"' . $str . '"' . '<br>');
		}
		return $str;
	}

	protected function _cleanInputData( $str )
	{
		if( is_array($str) ){
			$new_array = array();
			foreach ($str as $key => $val){
				$new_array[$this->_cleanInputKeys($key)] = $this->_cleanInputData($val);
			}
			return $new_array;
		}

		/* We strip slashes if magic quotes is on to keep things consistent
		   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
			it will probably not exist in future versions at all.
		*/
		$need_strip = FALSE;
		if( version_compare(PHP_VERSION, '5.4', '<') && get_magic_quotes_gpc() ){
			$need_strip = TRUE;
		}
		elseif( defined('WPINC') ){
			$need_strip = TRUE;
		}

		if( $need_strip ){
			$str = stripslashes($str);
		}

		// Remove control characters
		$str = HC4_App_Functions::removeInvisibleCharacters($str);

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE){
			$str = str_replace(array("\r\n", "\r", "\r\n\n"), PHP_EOL, $str);
		}

		return $str;
	}
}