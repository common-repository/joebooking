<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_Migration_Interface $migration,
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
		$migration
			->register( 'availability', 1, 'JB7_21Availability_Data_Migration@version1' )
			;

	// API
		$router
			;

	// UI
		$router
			->add( 'GET/manager/availability',				'JB7_21Availability_Ui_Manager_Index@get' )

			->add( 'GET/manager/availability/[:cid]',		'JB7_21Availability_Ui_Manager_Calendar@get' )

			->add( 'GET/manager/availability/[:cid]/new',	'JB7_21Availability_Ui_Manager_New@get' )
			->add( 'POST/manager/availability/[:cid]/new',	'JB7_21Availability_Ui_Manager_New@post' )

			->add( 'GET/manager/availability/[:cid]/sync',	'JB7_21Availability_Ui_Manager_Sync@get' )
			->add( 'POST/manager/availability/[:cid]/sync',	'JB7_21Availability_Ui_Manager_Sync@post' )
			->add( 'POST/manager/availability/[:cid]/unsync',	'JB7_21Availability_Ui_Manager_Sync@postUnsync' )

			->add( 'GET/manager/availability/:cid/[:id]',	'JB7_21Availability_Ui_Manager_Id_Edit@get' )
			->add( 'POST/manager/availability/:cid/[:id]',	'JB7_21Availability_Ui_Manager_Id_Edit@post' )

			->add( 'POST/manager/availability/:cid/[:id]/delete',	'JB7_21Availability_Ui_Manager_Id_Delete@post' )
			;

		$screen
			->title(	'manager/availability',				'__Availability__' )
			->title(	'manager/availability/[:cid]',	'JB7_21Availability_Ui_Manager_Calendar@title' )

			->menu(	'manager/availability/[:cid]',	'JB7_21Availability_Ui_Manager_Calendar@menu' )

			->title(	'manager/availability/:cid/new',		'__New Availability__' )
			->title(	'manager/availability/:cid/:id',		'__Edit Availability__' )
			->title( 'manager/availability/:cid/sync',	'__Sync From Another Calendar__' )

			->menu(	'manager/availability/:cid/:id',		array( '{CURRENT}/delete', NULL, '__Delete__' ) )

			->menu( '',	'JB7_21Availability_Ui_Manager_Index@menu' )
			;

	// CALENDARS UI
		$screen
			->menu(	'admin/calendars/[:id]',	'JB7_21Availability_Ui_Admin_Calendars_Id_Availability@menu' )
			;
	}
}