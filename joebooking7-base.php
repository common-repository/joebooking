<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices',
		create_function( '',
			"echo '<div class=\"error\"><p>" .
			__('JoeBooking requires PHP 5.3 to function properly. Please upgrade PHP or deactivate JoeBooking.', 'joebooking') ."</p></div>';"
			)
	);
	return;
}

if( ! class_exists('JoeBooking7_Wordpress') ){

class JoeBooking7_Wordpress
{
	public $dir;
	public $adminMenuLabel = 'JoeBooking';
	public $appConfig = array();

	public function __construct( $dir, array $appConfig = array() )
	{
		$this->dir = $dir;
		$this->appConfig = $appConfig;

		add_action( 'init', array($this, '_init') );
		add_action( 'init', array($this, 'intercept') );
		add_action( 'init', array($this, 'addRoles') );
		add_action( 'admin_init', array($this, 'adminInit') );
		add_action( 'admin_menu', array($this, 'adminMenu') );

		$this->hc4init();
		add_shortcode( 'jb7', array($this, 'shortcode') );
	}

	public function hc4init()
	{
		$platform = 'wordpress';
		$moreConfig = include( $this->dir . '/jb7/config.php' );
		$appConfig = array_merge( $this->appConfig, $moreConfig );

		$this->myPage = $appConfig['app-name'];
		$this->myAdminPage = $appConfig['app-name'];
		$this->myShortPage = $appConfig['app-short-name'];

		$appConfig['platform'] = $platform;
		$this->app = new HC4_App( $this->dir, $appConfig );
	}

	public function adminMenu()
	{
		$mainLabel = get_site_option( $this->myPage . '_menu_title' );
		if( ! strlen($mainLabel) ){
			$mainLabel = $this->adminMenuLabel;
		}

		$menuIcon = isset($this->menuIcon) ? $this->menuIcon : NULL;
		$menuIcon = 'dashicons-calendar';
		$requireCap = 'read';
		// $requireCap = 'manage_jb7';
		$page = add_menu_page(
			$mainLabel,
			$mainLabel,
			$requireCap,
			$this->myAdminPage,
			array( $this, 'render' ),
			$menuIcon,
			30
			);
	}

	public function _init()
	{
		// $modules = $this->app->getModules();
		// $modules = apply_filters( 'ha7_modules', $modules );
		// $this->app->setModules( $modules );

		// $appDirs = $this->app->getAppDirs();
		// $appDirs = apply_filters( 'ha7_dirs', $appDirs );
		// $this->app->setAppDirs( $appDirs );

		// $this->libDirs = apply_filters( 'ha7_libdirs', $this->libDirs );
		do_action( 'jb7_init' );

		$this->app->boot();
	}

	public function adminInit()
	{
		if( $this->isMeAdmin() ){
			$this->actionResult = $this->app->handleRequest();
		}
	}

	public function render()
	{
		echo $this->actionResult;
	}

	public function addRoles()
	{
		$myRoles = array(
			'jb7_admin' => array( 
				'label'			=> 'JoeBooking7 Administrator',
				'capabilities'	=> array('manage_jb7'),
				'assign_to'		=> array('editor', 'administrator')
				),

			// 'jb7_customer' => array( 
			// 	'label'			=> 'JoeBooking7 Customer',
			// 	'capabilities'	=> array(),
			// 	'assign_to'		=> array()
			// 	),
			);

		foreach( $myRoles as $role => $roleArray ){
			$r = get_role( $role );
			if( $r ){
				continue;
			}

			add_role( $role, $roleArray['label'], array('read' => TRUE) );

			if( $roleArray['capabilities'] ){
				global $wp_roles;
				reset( $roleArray['capabilities'] );
				foreach( $roleArray['capabilities'] as $cap ){
					$wp_roles->add_cap( $role, $cap );
					reset( $roleArray['assign_to'] );
					foreach( $roleArray['assign_to'] as $alsoTo ){
						$wp_roles->add_cap( $alsoTo, $cap );
					}
				}
			}
		}
	}

	public function shortcode( $shortcodeAtts )
	{
		$route = 'front';
		$result = $this->app->handleRequest( $route );
		return $result;
	}

// intercepts if in the front page our slug is given then it's ours
	public function intercept()
	{
		if( ! $this->isIntercepted() ){
			return;
		}

		$result = $this->app->handleRequest();
		echo $result;
		exit;
	}

	public function isIntercepted()
	{
		$return = FALSE;

		$k = 'hcs';
		if( array_key_exists($k, $_GET) ){
			$v = sanitize_text_field( $_GET[$k] );
			if( ($v == $this->myPage) OR ($v == $this->myShortPage) ){
				$return = TRUE;
			}
		}

		return $return;
	}

	public function isMeAdmin()
	{
		$return = FALSE;
		if( ! isset($_REQUEST['page']) ){
			return $return;
		} 

		$page = sanitize_text_field( $_REQUEST['page'] );

		if( $page == $this->myAdminPage ){
			$return = TRUE;
		}

		return $return;
	}
}

}