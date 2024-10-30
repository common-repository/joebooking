<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Data_Migration
{
	public function __construct(
		HC4_Database_Forge $dbForge,
		JB7_11Calendars_Data_Crud $crud
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
				'title' => array(
					'type' => 'VARCHAR(255)',
					'null' => FALSE,
					),
				'description' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'status' => array(
					'type' => 'VARCHAR(16)',
					'null' => TRUE,
					'default' => 'active',
					),
				'slot_size' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'capacity' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					'default' => 1,
					),

				'access' => array(
					'type' => 'VARCHAR(16)',
					'null' => TRUE,
					'default' => 'public',
					),
				'min_from_now' => array(
					'type' => 'VARCHAR(16)',
					'null' => TRUE,
					'default' => '3 hours',
					),
				'max_from_now' => array(
					'type' => 'VARCHAR(16)',
					'null' => TRUE,
					'default' => '8 weeks',
					),
				'initial_status' => array(
					'type' => 'VARCHAR(16)',
					'null' => TRUE,
					'default' => 'pending',
					),
				)
			);

		$this->dbForge->add_key( 'id', TRUE );
		$this->dbForge->create_table( 'jb7_calendars' );

		$sample = array(
			'id'		=> 1,
			'title'		=> 'My Calendar',
			'status'	=> 'active',
			'slot_size'	=> 30*60,
			'capacity'	=> 1,
			'access'	=> 'public',
			'min_from_now' => '3 hours',
			'max_from_now' => '8 weeks',
			);
		$this->crud->create( $sample );
	}
}