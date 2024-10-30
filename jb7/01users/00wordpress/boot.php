<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_01Users_00WordPress_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();
		$bind['JB7_01Users_Data_Repo'] = 'JB7_01Users_00WordPress_Data_Repo';
		return $bind;
	}

	public function __construct(
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
	// UI
		$router
			->add( 'GET/user/profile',		'JB7_01Users_00WordPress_Ui_User_Profile@get' )
			;

		$screen
			->menu( 'admin/users',		array( admin_url('user-new.php'), '+ __Add New__') )
			;
	}
}