<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Data_Migration
{
	public function __construct(
		HC4_Database_Forge $dbForge,
		JB7_21Availability_Data_Crud $crud
	)
	{}

	public function version1()
	{
		$this->dbForge->add_field(
			array(
				'id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					'auto_increment' => TRUE
					),
				'from_time' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'to_time' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'interval' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'applied_on' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'applied_on_details' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),

				'valid_from_date' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),
				'valid_to_date' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),

				'calendar_id' => array(
					'type'		=> 'INTEGER',
					'null'		=> FALSE,
					),
				'priority' => array(
					'type'		=> 'INTEGER',
					'null'		=> FALSE,
					'default'	=> 2,
					)
				)
			);

		$this->dbForge->add_key( 'id', TRUE );
		$this->dbForge->create_table( 'jb7_availability' );

		$sample = array(
			'from_time'		=> 10 * 60 * 60,
			'to_time'		=> 18 * 60 * 60,
			'interval'		=> 30 * 60,
			'applied_on'	=> 'daysofweek',
			'applied_on_details' => '["1","2","3","4","5"]',
			'calendar_id' 	=> 1,
			'priority'	 	=> 2,
			);
		$this->crud->create( $sample );

		$this->dbForge->add_field(
			array(
				'id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					'auto_increment' => TRUE
					),
				'from_calendar_id' => array(
					'type'		=> 'INTEGER',
					'null'		=> FALSE,
					),
				'to_calendar_id' => array(
					'type'		=> 'INTEGER',
					'null'		=> FALSE,
					),
				)
			);

		$this->dbForge->add_key( 'id', TRUE );
		$this->dbForge->create_table( 'jb7_availability_sync' );
	}
}