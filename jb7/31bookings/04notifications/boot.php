<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_04Notifications_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_App_Events $events,
		HC4_App_Router $router,
		JB7_04Notifications_Data_Repo $repo,
		HC4_Html_Screen_Interface $screen
	)
	{
		$events
			->register( 'JB7_31Bookings_Data_Repo', 'JB7_31Bookings_04Notifications_Service_Message_Created@listen' )
			->register( 'JB7_31Bookings_Data_Repo', 'JB7_31Bookings_04Notifications_Service_Message_Status@listen' )
			;

	// UI
		$router
			->add( 'GET/admin/conf/notifications',		'JB7_31Bookings_04Notifications_Ui_Admin_Conf_Notifications@get' )
			->add( 'POST/admin/conf/notifications',	'JB7_31Bookings_04Notifications_Ui_Admin_Conf_Notifications@post' )
			;

		$screen
			->menu(	'admin/conf',	array('{CURRENT}/notifications', '__Email Notifications__') )
			->title(	'admin/conf/notifications',	'__Email Notifications__' )
			;

		$repo
			->register( 
				'email-booking-created-manager',
				'__New Booking Created__',
				"__A new booking created__" . "\n" .
				"__Status__: {STATUS}" . "\n" .
				"__Ref No__: {REFNO}" . "\n" .
				"{DATE} {TIME}" . "\n" .
				"{CALENDAR}" . "\n" .
				"{DETAILS}"
				)
			->register( 
				'email-booking-created-customer',
				'__Your New Booking__',
				"__Here is your new booking__" . "\n" .
				"__Status__: {STATUS}" . "\n" .
				"__Ref No__: {REFNO}" . "\n" .
				"{DATE} {TIME}" . "\n" .
				"{CALENDAR}"
				)
			->register(
				'email-booking-status-manager',
				'__Booking Status Changed__',
				"__Your booking status changed__" . "\n" .
				"__Status__: {STATUS}" . "\n" .
				"__Ref No__: {REFNO}" . "\n" .
				"{DATE} {TIME}" . "\n" .
				"{CALENDAR}" . "\n" .
				"{DETAILS}"
				)
			->register( 
				'email-booking-status-customer',
				'__Your Booking Status Changed__',
				"__Your booking status changed__" . "\n" .
				"__Status__: {STATUS}" . "\n" .
				"__Ref No__: {REFNO}" . "\n" .
				"{DATE} {TIME}" . "\n" .
				"{CALENDAR}"
				)
			;
	}
}