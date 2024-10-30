<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_Migration_Interface $migration,
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
		$migration
			->register( 'calendars', 1, 'JB7_11Calendars_Data_Migration@version1' )
			;

	// API
		$router
			;

	// UI
		$router
			->add( 'GET/admin/calendars',					'JB7_11Calendars_Ui_Admin_Index@get' )
			->add( 'GET/admin/calendars/status/[:s]',	'JB7_11Calendars_Ui_Admin_Index@get' )

			->add( 'GET/admin/calendars/new',			'JB7_11Calendars_Ui_Admin_New@get' )
			->add( 'POST/admin/calendars/new',			'JB7_11Calendars_Ui_Admin_New@post' )

			->add( 'GET/admin/calendars/[:id]',				'JB7_11Calendars_Ui_Admin_Id_Edit@get' )
			->add( 'POST/admin/calendars/[:id]',			'JB7_11Calendars_Ui_Admin_Id_Edit@post' )
			;

		$screen
			->title(	'admin/calendars',					'__Calendars__' )
			// ->title( 'admin/calendars/status/[:s]',	'SH5_11Calendars_View_Admin_Index@titleStatus' )

			->title(	'admin/calendars/new',		'__New Calendar__' )

			->title( 'admin/calendars/[:id]',			'JB7_11Calendars_Ui_Admin_Id@title' )

			->menu(	'',						array('admin/calendars', '__Calendars__') )
			->menu(	'admin/calendars',	array('{CURRENT}/new', '+ __Add New__') )
			->menu(	'admin/calendars',	'JB7_11Calendars_Ui_Admin_Index@menu' )
			;
	}
}