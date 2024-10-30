<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Data_Migration
{
	public function __construct(
		HC4_Database_Forge $dbForge
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
				'message_id' => array(
					'type' => 'VARCHAR(64)',
					'null' => FALSE,
					),
				'subject' => array(
					'type' => 'VARCHAR(255)',
					'null' => TRUE,
					),
				'body' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'is_disabled' => array(
					'type' => 'TINYINT',
					'null' => TRUE,
					'default' => 0,
					)
				)
			);

		$this->dbForge->add_key( 'id', TRUE );
		$this->dbForge->create_table( 'jb7_notification_templates' );
	}
}