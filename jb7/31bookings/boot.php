<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_App_Events $events,
		HC4_Migration_Interface $migration,
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
		$migration
			->register( 'bookings', 1, 'JB7_31Bookings_Data_Migration@version1' )
			->register( 'bookings', 2, 'JB7_31Bookings_Data_Migration@version2' )
			->register( 'bookings', 3, 'JB7_31Bookings_Data_Migration@version3' )
			;

		$events
			->register( 'JB7_31Bookings_Data_Repo', 'JB7_31Bookings_Data_Audit@log' )
			;
	}
}