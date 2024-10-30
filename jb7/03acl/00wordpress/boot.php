<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_03Acl_00WordPress_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();
		$bind['JB7_03Acl_Data_Repo'] = 'JB7_03Acl_00WordPress_Data_Repo';
		return $bind;
	}

	public function __construct(
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen,

		HC4_Settings_Interface $settings,
		JB7_03Acl_00WordPress_Data_Repo $repoAcl
	)
	{
		$router
			->add( 'GET/admin/conf/acl',		'JB7_03Acl_00WordPress_Ui_Admin_Settings@get' )
			->add( 'POST/admin/conf/acl',		'JB7_03Acl_00WordPress_Ui_Admin_Settings@post' )
			;

		$screen
			->title(	'admin/conf/acl',		'__Access Permissions__' )
			->menu(	'admin/conf',			array('admin/conf/acl',	'__Access Permissions__') )
			;

	// INIT SETTINGS SET ALL CUSTOMERS BY DEFAULT
		global $wp_roles;
		if( ! isset($wp_roles) ){
			$wp_roles = new WP_Roles();
		}
		$wpRoles = array();
		$names = $wp_roles->get_names();
		foreach( $names as $k => $v ){
			$k = str_replace(' ', '_', $k);
			$wpRoles[$k] = $v;
		}

		$defaultAdmins = $repoAcl->getDefaultAdminRoles();
		foreach( array_keys($wpRoles) as $wpRoleName ){
			// $pName = 'users_wp_' . $wpRoleName . '_' . 'customer';
			// $pValue = in_array($wpRoleName, $defaultAdmins) ? 0 : 1;
			// $settings->init( $pName, $pValue );

			$pName = 'users_wp_' . $wpRoleName . '_' . 'admin';
			$pValue = in_array($wpRoleName, $defaultAdmins) ? 1 : 0;
			$settings->init( $pName, $pValue );
		}
	}

}