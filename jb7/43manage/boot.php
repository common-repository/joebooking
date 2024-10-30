<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
	// UI
		$router
			->add( 'GET/manage',					'JB7_43Manage_Ui_Index@get' )
			->add( 'POST/manage',				'JB7_43Manage_Ui_Index@post' )
			->add( 'GET/manage/[:params]',	'JB7_43Manage_Ui_Schedule@get' )
			;

		$screen
			->title( 'manage',				'__Manage Bookings__' )
			->title( 'manage/[:params]',	'JB7_43Manage_Ui_Schedule@title' )
			->menu(	'manage/[:params]',	'JB7_43Manage_Ui_Schedule@menu' )
			->menu( '',	'JB7_43Manage_Ui_Index@menu' )
			;

	// NEW
		$router
			->add( 'GET/manage/[:p]/new/[:p2]',		'JB7_43Manage_Ui_New_Index@get' )
			->add( 'POST/manage/[:p]/new/[:p2]',	'JB7_43Manage_Ui_New_Index@post' )
			;

		$screen
			->title( 'manage/[:p]/new/[:p2]',	'JB7_43Manage_Ui_New_Index@title' )
			;

	// BOOKINGS
		$router
			->add( 'GET/manage/:params/[:id]',		'JB7_43Manage_Ui_Bookings_Id@get' )

			->add( 'GET/manage/:p/[:id]/status',	'JB7_43Manage_Ui_Bookings_Id_Status@get' )
			->add( 'POST/manage/:p/[:id]/status',	'JB7_43Manage_Ui_Bookings_Id_Status@post' )

			->add( 'GET/manage/:p/[:id]/reschedule/[:p2]',			'JB7_43Manage_Ui_Bookings_Id_Reschedule@get' )
			->add( 'GET/manage/:p/[:id]/reschedule/:p2/new/[:p3]',	'JB7_43Manage_Ui_Bookings_Id_Reschedule_New@get' )
			->add( 'POST/manage/:p/[:id]/reschedule/:p2/new/[:p3]',	'JB7_43Manage_Ui_Bookings_Id_Reschedule_New@post' )

			->add( 'GET/manage/:p/[:id]/reschedule/:p2/[:newid]',	'JB7_43Manage_Ui_Bookings_Id_Reschedule_New@getSwap' )
			->add( 'POST/manage/:p/[:id]/reschedule/:p2/[:newid]',	'JB7_43Manage_Ui_Bookings_Id_Reschedule_New@postSwap' )

			->add( 'GET/manage/:p/[:id]/audit',		'JB7_43Manage_Ui_Bookings_Id_Audit@get' )
			;

		$screen
			->title( 'manage/:p/[:id]',	'JB7_43Manage_Ui_Bookings_Id@title' )

			->menu( 'manage/:p/:id',		array('{CURRENT}/status', '__Status__') )
			->menu( 'manage/[:p]/[:id]',	'JB7_43Manage_Ui_Bookings_Id@menu' )

			->menu(	'manage/[:p]/[:id]/reschedule/[:p2]',	'JB7_43Manage_Ui_Bookings_Id_Reschedule@menu' )

			->title( 'manage/:ps/:id/status',				'__Status__' )
			->title( 'manage/:p/:id/reschedule/:p2',		'__Reschedule__' )
			->title( 'manage/:p/:id/reschedule/:p2/new/:p3',	'__Confirm Reschedule__' )

			->title( 'manage/:p/:id/reschedule/:p2/:newid',		'__Swap Bookings__' )

			->menu(	'manage/:p/[:id]',			array('{CURRENT}/audit', '__History__') )
			->title( 'manage/:p/[:id]/audit',	'__History__' )
			;
	}
}