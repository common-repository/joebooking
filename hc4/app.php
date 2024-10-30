<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
if( ! function_exists('_print_r') ){
	function _print_r( $thing )
	{
		echo '<pre>';
		print_r( $thing );
		echo '</pre>';
	}
}

if( ! class_exists('HC4_App') ){

class HC4_App
{
	protected $appConfig = array();
	protected $appDir = NULL;

	protected $factory = NULL;
	protected $modules = array();
	protected $profiler = NULL;

	protected $logFile = NULL;

	public function __construct( $appDir, array $appConfig = array() )
	{
		$this->appDir = $appDir;
		$this->modules = isset( $appConfig['modules'] ) ? $appConfig['modules'] : array();

		if( ! isset($appConfig['platform']) ){
			$appConfig['platform'] = 'standalone';
		}

		$this->appConfig = $appConfig;

		spl_autoload_register( array($this, 'autoloader') );
	}

	public function logFile( $file )
	{
		$this->logFile = $file;
		return $this;
	}

	public function boot()
	{
		$bind = array();
		$bind[ get_class($this) ] = $this;

		$profiler = new HC4_App_Profiler;
		$bind[ get_class($profiler) ] = $profiler;

		$appConfig = new HC4_App_Config( $this->appConfig );
		$appConfig['app-dir'] = $this->appDir;
		$appConfig[ get_class($profiler) ] = $profiler;

		$bind[ get_class($appConfig) ] = $appConfig;

	// init bind
		reset( $this->modules );
		foreach( $this->modules as $moduleName ){
			// $moduleClassName = $moduleName . '_Index';
			$moduleClassName = $moduleName . '_Boot';
			if( class_exists($moduleClassName) ){
				$thisBind = method_exists($moduleClassName, 'bind') ?
					call_user_func( array($moduleClassName, 'bind'), (array) $appConfig ) : 
					array()
					;
				$bind = array_merge( $bind, $thisBind );
			}
		}

		$this->factory = array( new HC4_App_Factory($bind, $this->modules), 'make' );

		if( in_array('hc4_session', $this->modules) ){
			$sessionPrefix = isset( $this->appConfig['app-short-name'] ) ? $this->appConfig['app-short-name'] . '_' : 'hc4_';
			$session = call_user_func( $this->factory, 'HC4_Session_Interface', $sessionPrefix );
		}

		reset( $this->modules );
		foreach( $this->modules as $moduleName ){
			// $moduleClassName = $moduleName . '_Index';
			$moduleClassName = $moduleName . '_Boot';
			if( class_exists($moduleClassName) ){
				$module = call_user_func( $this->factory, $moduleClassName );
			}
		}

	// MIGRATIONS
		if( in_array('hc4_migration', $this->modules) ){
			$migration = call_user_func( $this->factory, 'HC4_Migration_Interface' );
			$migration->up();
		}

	// INIT EVENTS
		$events = call_user_func( $this->factory, 'HC4_App_Events' );
	}

	public function log( array $log = array() )
	{
		if( ! $this->logFile ){
			return;
		}

		if( ! $log ){
			return;
		}

		$now = date( 'j M Y g:ia', time() );
		array_unshift( $log, $now );

		$out = join( "\t", $log );

		$fp = fopen( $this->logFile, 'a' );
		fwrite( $fp, $out . "\n" );
		fclose( $fp );

		return $this;
	}

	public function handleRequest( $defaultSlug = NULL )
	{
		$uri = new HC4_App_Uri();
		$slug = $uri->getSlug();
		if( ! strlen($slug) ){
			$slug = $defaultSlug;
		}

		$request = new HC4_App_Request;
		$requestMethod = $request->getMethod();

		$postData = NULL;
		if( in_array($requestMethod, array('post', 'put', 'patch')) ){
			$postData = $request->getPost();
		}

	// LOG
		if( $this->logFile ){
			$out = array();
			$out[] = $request->getIpAddress();
			$out[] = $requestMethod;
			$out[] = $slug;
			if( $postData ){
				$out[] = http_build_query( $postData );
			}

			$this->log( $out );
		}

		if( ! $this->check( $requestMethod, $slug ) ){
			$to = 'notallowed';
			$return = array( $to, NULL );
		}
		else {
			$return = $this->handle( $requestMethod, $slug, $postData );
		}

	// IF STRING THEN SHOW IT
		if( ! is_array($return) ){
		// CLOSING
			reset( $this->modules );
			foreach( $this->modules as $moduleName ){
				$moduleClassName = $moduleName . '_Close';
				if( class_exists($moduleClassName) ){
					$module = call_user_func( $this->factory, $moduleClassName );
				}
			}

			return $return;
		}

	// REDIRECT OR HEADER STATUS
		$to = array_shift( $return );
		$msg = array_shift( $return );
		$error = array_shift( $return );

	// HEADER STATUS
		if( is_numeric($to) ){
			$out = array();
			if( $msg ){
				$out['message'] = $message;
			}
			if( $error ){
				$out['error'] = $error;
			}

		// CLOSING
			reset( $this->modules );
			foreach( $this->modules as $moduleName ){
				$moduleClassName = $moduleName . '_Close';
				if( class_exists($moduleClassName) ){
					$module = call_user_func( $this->factory, $moduleClassName );
				}
			}

			HC4_App_Functions::httpStatusCode( $to );
			header( 'Content-type: application/json' );
			$out = json_encode( $out );
			echo $out;
			exit;
		}

		$session = NULL;
		if( in_array('hc4_session', $this->modules) ){
			$session = call_user_func( $this->factory, 'HC4_Session_Interface' );
		}

		if( $session ){
			if( $error ){
				$session->setFlashdata( 'error', $error );
				$session->setFlashdata( 'post', $postData );
			}
			if( $msg ){
				$session->setFlashdata( 'message', $msg );
			}
		}

	// IF POST THEN REDIRECT
		if( NULL !== $postData ){
		// CLOSING
			reset( $this->modules );
			foreach( $this->modules as $moduleName ){
				$moduleClassName = $moduleName . '_Close';
				if( class_exists($moduleClassName) ){
					$module = call_user_func( $this->factory, $moduleClassName );
				}
			}

			$uri = new HC4_App_Uri();

			if( '-referrer-' == $to ){
				$to = $slug;
				$to = $uri->makeUrl( $to );

				$to .= ( FALSE === strpos($to, '?') ) ? '?' : '&';
				if( isset($postData['hcs']) ){
					// $myAppShortName = isset( $this->appConfig['app-short-name'] ) ? $this->appConfig['app-short-name'] : 'hc4';
					// $to .= 'hcs=' . $myAppShortName;
					$to .= 'hcs=' . $postData['hcs'];
				}
			}
			else {
				$to = $uri->makeUrl( $to );
			}

			$out = array( 'to' => $to );

			$redirect = call_user_func( $this->factory, 'HC4_Redirect_Interface' );
			$redirect->call( $to );
			exit;
		}
	/* IF GET THEN JUST SHOW THE TARGET */
		else {
			return $this->handle( 'get', $to );
		}

		return $return;
	}

	public function check( $method, $slug )
	{
		$return = TRUE;

		$router = call_user_func( $this->factory, 'HC4_App_Router' );
		$method = 'CHECK:' . $method;
		$checkers = $router->find( $method, $slug );

		if( ! $checkers ){
			return $return;
		}

		foreach( $checkers as $h ){
			list( $checker, $args ) = $h;

		// ADD SLUG TO ARGUMENTS
			array_unshift( $args, $slug );

			$checker = trim( $checker );
			if( FALSE === strpos($checker, '@') ){
				$method = 'call';
			}
			else {
				list( $checker, $method ) = explode( '@', $checker );
			}
			$checker = array( $checker, $method );

			if( ! is_object($checker[0]) ){
				$checker[0] = call_user_func( $this->factory, $checker[0] );
			}

		// HANDLE
			try {
				$return = call_user_func_array( $checker, $args );
			}
			catch( HC4_App_Exception_DataError $e ){
				if( $session ){
					$error = $e->getMessage();
					$session->setFlashdata( 'error', $error );
					$session->setFlashdata( 'post', $postData );
				}
				$return = array( '-referrer-', NULL, $error );
				break;
			}

			if( NULL !== $return ){
				break;
			}
		}

		if( NULL === $return ){
			$return = TRUE;
		}

		return $return;
	}

	public function handle( $method, $slug, $postData = NULL )
	{
		$return = NULL;

		$router = call_user_func( $this->factory, 'HC4_App_Router' );
		$handlers = $router->find( $method, $slug );

// echo "FOR SLUG '$slug'<br>";
// _print_r( $handlers );
// exit;

		if( ! $handlers ){
			$return = "NOTHING TO HANDLE THIS REQUEST: '$method:$slug'<br>";
			// echo $return;
			// exit;
			return $return;
		}

		$session = NULL;
		if( in_array('hc4_session', $this->modules) ){
			$session = call_user_func( $this->factory, 'HC4_Session_Interface' );
		}

		foreach( $handlers as $h ){
			list( $handler, $args ) = $h;

		// ADD POST TO ARGUMENTS
			if( NULL !== $postData ){
				array_unshift( $args, $postData );
			}
		// ADD SLUG TO ARGUMENTS
			array_unshift( $args, $slug );

			$handler = trim( $handler );
			if( FALSE === strpos($handler, '@') ){
				$method = 'call';
			}
			else {
				list( $handler, $method ) = explode( '@', $handler );
			}
			$handler = array( $handler, $method );

			if( ! is_object($handler[0]) ){
				$handler[0] = call_user_func( $this->factory, $handler[0] );
			}

		// HANDLE
			try {
				$return = call_user_func_array( $handler, $args );
			}
			catch( HC4_App_Exception_DataError $e ){
				if( $session ){
					$error = $e->getMessage();
					$session->setFlashdata( 'error', $error );
					$session->setFlashdata( 'post', $postData );
				}
				$return = array( '-referrer-', NULL, $error );
				break;
			}
			catch( HC4_App_Exception_FormErrors $e ){
				if( $session ){
					$session->setFlashdata( 'form_errors', $e->getErrors() );
					$session->setFlashdata( 'post', $postData );
				}
				$return = array( '-referrer-', NULL );
				break;
			}

			if( NULL !== $return ){
				break;
			}
		}

		return $return;
	}

	public function autoloader( $inclass )
	{
		$class = trim( $inclass );

		if( ! (('_' == substr($class, 3, 1)) OR ('_' == substr($class, 4, 1))) ){
			return;
		}

		$class = strtolower( $class );

		if( defined('HC4_DEV_INSTALL') && (substr($class, 0, strlen('hc4_')) == 'hc4_') ){
			$dir = HC4_DEV_INSTALL;
		}
		else {
			$dir = $this->appDir;
		}

		$thisFile = $dir . '/' . str_replace( '_', '/', $class ) . '.php';
		if( file_exists($thisFile) ){
			include_once( $thisFile );
			return;
		}
		// echo "HC4 FOR '$inclass' TRIED $thisFile<br>\n";
	}
}
}