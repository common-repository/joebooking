<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_03Acl_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_Migration_Interface $migration,
		HC4_Settings_Interface $settings,
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
	// DEFAULTS
		$settings
			// ->init( 'datetime_date_format', 'j M Y' )
			// ->init( 'datetime_time_format', 'g:ia' )
			// ->init( 'datetime_week_starts', 0 )
			;


		$router
			->add( 'GET/notallowed',	'JB7_03Acl_Ui_NotAllowed@get' )
			;

		$screen
			->title( 'notallowed',	'__Not Allowed__' )
			;

	// GENERAL
		$router
			->add( 'CHECK:GET/admin',		'JB7_03Acl_Ui_Check@checkAdmin' )
  			->add( 'CHECK:GET/admin*',		'JB7_03Acl_Ui_Check@checkAdmin' )
			->add( 'CHECK:POST/admin',		'JB7_03Acl_Ui_Check@checkAdmin' )
			->add( 'CHECK:POST/admin*',	'JB7_03Acl_Ui_Check@checkAdmin' )

			->add( 'CHECK:GET/manage',		'JB7_03Acl_Ui_Check@checkAdmin' )
  			->add( 'CHECK:GET/manage*',	'JB7_03Acl_Ui_Check@checkAdmin' )
			->add( 'CHECK:POST/manage',	'JB7_03Acl_Ui_Check@checkAdmin' )
			->add( 'CHECK:POST/manage*',	'JB7_03Acl_Ui_Check@checkAdmin' )

	// BOOKINGS
			;
	}
}