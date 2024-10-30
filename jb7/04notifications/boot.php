<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_Migration_Interface $migration
	)
	{
		$migration
			->register( 'notifications', 1, 'JB7_04Notifications_Data_Migration@version1' )
			;
	}
}