<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Audit_Data_Migration
{
	public function __construct( HC4_Database_Forge $dbForge )
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
				'table_name' => array(
					'type' => 'VARCHAR(64)',
					'null' => FALSE,
					),
				'row_id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'column_name' => array(
					'type' => 'VARCHAR(64)',
					'null' => FALSE,
					),

				'old_value' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'new_value' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),

				'event_datetime' => array(
					'type' => 'BIGINT',
					'null' => FALSE,
					),
				'user_id' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),
				'event_comment' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				)
			);

		$this->dbForge->add_key( 'id', TRUE );
		$this->dbForge->create_table( 'jb7_audit' );
	}
}