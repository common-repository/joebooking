<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_98Demo_Ui_Init
{
	public function __construct(
		HC4_Settings_Interface $settings,
		HC4_Database_Forge $dbForge,
		HC4_Migration_Interface $migration
	)
	{}

	public function get()
	{
		set_time_limit( 300 );
		ini_set( 'memory_limit', '64M' );

		$this->settings->resetAll();

		$tables = array(
			'jb7_audit',
			'jb7_conf',
			'jb7_calendars',
			'jb7_calendargroups',
			'jb7_availability',
			'jb7_bookings',
		);

		foreach( $tables as $table ){
			$this->dbForge->drop_table( $table );
		}

		$this->migration->up();
	}
}