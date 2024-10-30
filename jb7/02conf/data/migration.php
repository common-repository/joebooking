<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_02Conf_Data_Migration
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
				'name' => array(
					'type' => 'VARCHAR(255)',
					'null' => FALSE,
					),
				'value' => array(
					'type'		=> 'TEXT',
					'null'		=> TRUE,
					),
				)
			);
		$this->dbForge->add_key( 'id', TRUE );
		$this->dbForge->create_table( 'jb7_conf' );
	}
}