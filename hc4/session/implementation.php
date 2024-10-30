<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Session_Implementation
	implements HC4_Session_Interface
{
	protected $_prefix = 'hc4_';
	protected $request = NULL;
	protected $encrypt = NULL;

	protected $encryption_key = NULL;

	protected $sess_encrypt_cookie		= FALSE;
	protected $sess_expiration			= 7200;
	protected $sess_expire_on_close		= FALSE;
	protected $sess_match_ip			= FALSE;
	protected $sess_match_useragent		= FALSE;
	protected $sess_cookie_name			= 'hc4_session';
	protected $cookie_prefix				= '';
	protected $cookie_path				= '';
	protected $cookie_domain				= '';
	protected $cookie_secure				= FALSE;
	protected $sess_time_to_update		= 300;
	protected $flashdata_key				= 'flash';
	protected $time_reference				= 'time';
	protected $userdata					= array();
	protected $now;

	protected $builtin_props = array(
		'session_id',
		'ip_address',
		'user_agent',
		'last_activity',
		'user_data'
		);

	/**
	 * Session Constructor
	 *
	 * The constructor runs the session routines automatically
	 * whenever the class is instantiated.
	 */

	public function __construct(
		$prefix = 'hc4_'
	)
	{
		// $this->request = $request;
		$this->_prefix = $prefix;

		$this->encryption_key = md5(__FILE__);

// echo "INIT SESSION FOR '$this->_prefix'<br>";

		if( session_id() == '' ){
			@session_start();
		}

		// Set the "now" time.  Can either be GMT or server time, based on the
		// config prefs.  We use this to set the "last activity" time
		$this->now = $this->_get_time();

		// Set the session length. If the session expiration is
		// set to zero we'll set the expiration two years from now.
		if ($this->sess_expiration == 0){
			$this->sess_expiration = (60*60*24*365*2);
		}

	// Set the cookie name
		// $this->sess_cookie_name = $this->cookie_prefix . $this->sess_cookie_name . '_' . $this->_prefix;
		$this->sess_cookie_name = $this->cookie_prefix . $this->sess_cookie_name;

		// Run the Session routine. If a session doesn't exist we'll
		// create a new one.  If it does, we'll update it.
		if ( ! $this->sess_read()){
			$this->sess_create();
		}
		else {
			$this->sess_update();
		}

		// Delete 'old' flashdata (from last request)
		$this->_flashdata_sweep();

		// Mark all new flashdata as old (data will be deleted before next request)
		$this->_flashdata_mark();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the current session data if it exists
	 *
	 * @access	public
	 * @return	bool
	 */
	function sess_read()
	{
		// Fetch the cookie
		$session = array_key_exists($this->sess_cookie_name, $_COOKIE) ? $_COOKIE[$this->sess_cookie_name] : FALSE;

		// No cookie?  Goodbye cruel world!...
		if ($session === FALSE)
		{
			// log_message('debug', 'A session cookie was not found.');
			return FALSE;
		}

		// Decrypt the cookie data
		if( $this->encrypt ){
			$session = $this->encrypt->decode($session);
		}
		else {
			// encryption was not used, so we need to check the md5 hash
			$hash	 = substr($session, strlen($session)-32); // get last 32 chars
			$session = substr($session, 0, strlen($session)-32);

			// Does the md5 hash match?  This is to prevent manipulation of session data in userspace
			if ($hash !==  md5($session.$this->encryption_key)){
				// echo 'The session cookie data did not match what was expected. This could be a possible malicious attempt.';
				$this->sess_destroy();
				return FALSE;
			}
		}

		// Unserialize the session array
		$session = $this->_unserialize($session);

		// Is the session data we unserialized an array with the correct format?
		if ( ! is_array($session) OR ! isset($session['session_id']) OR ! isset($session['ip_address']) OR ! isset($session['user_agent']) OR ! isset($session['last_activity'])){
			$this->sess_destroy();
			return FALSE;
		}

		// Is the session current?
		if (($session['last_activity'] + $this->sess_expiration) < $this->now){
			$this->sess_destroy();
			return FALSE;
		}

		// Session is valid!
		$this->userdata = $session;
		unset($session);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Write the session data
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_write()
	{
		$this->_set_cookie();
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{
		$sessid = '';
		while (strlen($sessid) < 32){
			$sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		// $sessid .= $this->request->getIpAddress();

		$this->userdata = array(
			'session_id'	=> md5(uniqid($sessid, TRUE)),
			// 'ip_address'	=> $this->request->getIpAddress(),
			// 'user_agent'	=> substr($this->request->getUserAgent(), 0, 120),
			'last_activity'	=> $this->now,
			'user_data'		=> ''
			);

		// Write the cookie
		$this->_set_cookie();
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{
		// We only update the session every five minutes by default
		if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now)
		{
			return;
		}

		// Save the old session id so we know which record to
		// update in the database if we need it
		$old_sessid = $this->userdata['session_id'];
		$new_sessid = '';
		while (strlen($new_sessid) < 32)
		{
			$new_sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		// $new_sessid .= $this->request->getIpAddress();

		// Turn it into a hash
		$new_sessid = md5(uniqid($new_sessid, TRUE));

		// Update the session data in the session data array
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = $this->now;

		// _set_cookie() will handle this for us if we aren't using database sessions
		// by pushing all userdata to the cookie.
		$cookie_data = NULL;

		// Write the cookie
		$this->_set_cookie($cookie_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Destroy the current session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_destroy()
	{
		// Kill the cookie
		@setcookie(
					$this->sess_cookie_name,
					addslashes(serialize(array())),
					($this->now - 31500000),
					$this->cookie_path,
					$this->cookie_domain,
					0
				);

		// Kill session data
		$this->userdata = array();
	}

	public function getUserdata($item)
	{
		$my_key = $this->_prefix . $item;
		if( isset($_SESSION[$my_key]) ){
			return $_SESSION[$my_key];
		}
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch all session data
	 *
	 * @access	public
	 * @return	array
	 */
	function all_userdata()
	{
		$return = array();
		/* get flash data we store in _SESSION */
		foreach( $_SESSION as $key => $v ){
			if( ! (substr($key, 0, strlen($this->_prefix)) == $this->_prefix) )
				continue;
			$my_key = substr($key, strlen($this->_prefix) );
			$return[ $my_key ] = $v;
		}

		$parent_return = $this->userdata;
		$return = array_merge( $return, $parent_return );
		return $return;
	}

	public function setUserdata( $key, $value, $append = FALSE )
	{
		$newdata = array( $key => $value );

		$parent_newdata = array();
		if (count($newdata) > 0){
			$parent_newdata = array();
			foreach ($newdata as $key => $val){
				if( ! in_array($key, $this->builtin_props) ){
					$my_key = $this->_prefix . $key;
					if( $append ){
						if( ! isset($_SESSION[$my_key]) )
							$_SESSION[$my_key] = array();
						if( ! is_array($_SESSION[$my_key]) )
							$_SESSION[$my_key] = array( $_SESSION[$my_key] );
						$_SESSION[$my_key][] = $val;
					}
					else {
						$_SESSION[$my_key] = $val;
					}
				}
				else {
					$parent_newdata[ $key ] = $val;
				}
			}
		}

		if( $parent_newdata ){
			if (count($parent_newdata) > 0){
				foreach( $parent_newdata as $key => $val){
					$this->userdata[$key] = $val;
				}
			}
			$this->sess_write();
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a session variable from the "userdata" array
	 *
	 * @access	array
	 * @return	void
	 */
	public function unsetUserdata( $key )
	{
		$parent_newdata = array();

		if( ! in_array($key, $this->builtin_props) ){
			$my_key = $this->_prefix . $key;
			unset($_SESSION[$my_key]);
		}
		else {
			$parent_newdata[ $key ] = $val;
		}

		if( $parent_newdata ){
			foreach ($parent_newdata as $key => $val){
				unset($this->userdata[$key]);
			}
			$this->sess_write();
		}
		return $this;
	}

	public function addFlashdata( $name, $value )
	{
		return $this->setFlashdata( $name, $value, TRUE );
	}

	public function setFlashdata( $name, $value, $append = FALSE )
	{
		$newdata = array( $name => $value );

		foreach( $newdata as $key => $val ){
			$flashdata_key = $this->flashdata_key.':new:'.$key;
			$this->setUserdata( $flashdata_key, $val, $append );
		}

		return $this;
	}

	public function getFlashdata( $key )
	{
		$flashdata_key = $this->flashdata_key.':old:'.$key;
		return $this->getUserdata($flashdata_key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Identifies flashdata as 'old' for removal
	 * when _flashdata_sweep() runs.
	 *
	 * @access	private
	 * @return	void
	 */
	protected function _flashdata_mark()
	{
		$userdata = $this->all_userdata();
		foreach ($userdata as $name => $value)
		{
			$parts = explode(':new:', $name);
			if (is_array($parts) && count($parts) === 2)
			{
				$new_name = $this->flashdata_key.':old:'.$parts[1];
				$this->setUserdata($new_name, $value);
				$this->unsetUserdata($name);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Removes all flashdata marked as 'old'
	 *
	 * @access	private
	 * @return	void
	 */

	protected function _flashdata_sweep()
	{
		$userdata = $this->all_userdata();
		foreach ($userdata as $key => $value){
			if (strpos($key, ':old:')){
				$this->unsetUserdata($key);
			}
		}

	}

	protected function _get_time()
	{
		if (strtolower($this->time_reference) == 'gmt'){
			$now = time();
			$time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
		}
		else {
			$time = time();
		}

		return $time;
	}

	// --------------------------------------------------------------------

	/**
	 * Write the session cookie
	 *
	 * @access	public
	 * @return	void
	 */
	function _set_cookie($cookie_data = NULL)
	{
		if (is_null($cookie_data)){
			$cookie_data = $this->userdata;
		}

		// Serialize the userdata for the cookie
		$cookie_data = $this->_serialize($cookie_data);

		if( $this->encrypt ){
			$cookie_data = $this->encrypt->encode($cookie_data);
		}
		else {
			// if encryption is not used, we provide an md5 hash to prevent userside tampering
			$cookie_data = $cookie_data.md5($cookie_data.$this->encryption_key);
		}

		$expire = ($this->sess_expire_on_close === TRUE) ? 0 : $this->sess_expiration + time();
		// Set the cookie
		@setcookie(
			$this->sess_cookie_name,
			$cookie_data,
			$expire,
			$this->cookie_path,
			$this->cookie_domain,
			$this->cookie_secure
			);
	}

	protected function _serialize($data)
	{
		if (is_array($data)){
			foreach ($data as $key => $val){
				if (is_string($val)){
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else {
			if (is_string($data)){
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}
		return serialize($data);
	}

	protected function _unserialize($data)
	{
		$data = @unserialize( $this->strip_slashes($data) );

		if (is_array($data)){
			foreach ($data as $key => $val){
				if (is_string($val)){
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}
			return $data;
		}

		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}

	function strip_slashes($str)
	{
		if (is_array($str)){
			foreach ($str as $key => $val){
				$str[$key] = $this->strip_slashes($val);
			}
		}
		else {
			$str = stripslashes($str);
		}
		return $str;
	}
}