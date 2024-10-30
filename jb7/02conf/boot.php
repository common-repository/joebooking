<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_02Conf_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_Migration_Interface $migration,
		HC4_Settings_Interface $settings,
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
	// DATA
		$migration
			->register( 'conf', 1, 'JB7_02Conf_Data_Migration@version1' )
			;

	// DEFAULTS
		$settings
			->init( 'datetime_date_format', 'j M Y' )
			->init( 'datetime_time_format', 'g:ia' )
			->init( 'datetime_week_starts', 0 )
			;

	// UI
		$router
			->add( 'GET/admin/conf',				'JB7_02Conf_Ui_Admin_Index@get' )

			->add( 'GET/admin/conf/datetime',	'JB7_02Conf_Ui_Admin_Datetime@get' )
			->add( 'POST/admin/conf/datetime',	'JB7_02Conf_Ui_Admin_Datetime@post' )

			->add( 'GET/admin/conf/email',	'JB7_02Conf_Ui_Admin_Email@get' )
			->add( 'POST/admin/conf/email',	'JB7_02Conf_Ui_Admin_Email@post' )
			;

		$screen
			->menu(	'',				array( 'admin/conf', '__Settings__') )
			->menu(	'admin/conf',	array( '{CURRENT}/datetime', '__Date and Time__') )
			->menu(	'admin/conf',	array( '{CURRENT}/email', '__Email__') )

			->title(	'admin/conf',				'__Settings__' )
			->title(	'admin/conf/datetime',	'__Date and Time__' )
			->title(	'admin/conf/email',		'__Email__' )
			;
	}
}