<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_99App_00WordPress_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( $appConfig )
	{
		$return = array();
		$return['JB7_99App_Ui_Promo'] = 'JB7_99App_00WordPress_Ui_Promo';
		return $return;
	}

	public function __construct(
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
	// UI
		$router
			->add( 'GET/admin/publish',	'JB7_99App_00WordPress_Ui_Admin_Publish@get' )
			;

		$screen
			->title( 'admin/publish',	'__Publish__' )
			->menu( '',			array('admin/publish', '__Publish__') )
			;
	}
}