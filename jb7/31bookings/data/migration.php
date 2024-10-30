<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Data_Migration
{
	public function __construct(
		HC4_Database_Forge $dbForge,
		HC4_Database_Interface $db,

		JB7_31Bookings_Data_Crud $crud,
		JB7_31Bookings_Data_Repo $repo
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

				'start_datetime' => array(
					'type' => 'BIGINT',
					'null' => FALSE,
					),
				'end_datetime' => array(
					'type' => 'BIGINT',
					'null' => FALSE,
					),

				'status' => array(
					'type' => 'VARCHAR(16)',
					'null' => TRUE,
					'default'	=> 'pending',
					),

				'calendar_id' => array(
					'type'	=> 'INTEGER',
					'null'	=> FALSE,
					),

				'details' => array(
					'type'	=> 'TEXT',
					'null'	=> FALSE,
					),
				)
			);

		$this->dbForge->add_key( 'id', TRUE );
		$this->dbForge->create_table( 'jb7_bookings' );
	}

	public function version2()
	{
		if( ! $this->dbForge->field_exists('refno', 'jb7_bookings') ){
			$this->dbForge->add_column(
				'jb7_bookings',
				array(
					'refno' => array(
						'type'		=> 'VARCHAR(16)',
						'null'		=> FALSE,
						'default'	=> '',
						),
					)
				);
		}

		if( ! $this->dbForge->field_exists('token', 'jb7_bookings') ){
			$this->dbForge->add_column(
				'jb7_bookings',
				array(
					'token' => array(
						'type'		=> 'VARCHAR(32)',
						'null'		=> TRUE
						),
					)
				);
		}

		if( ! $this->dbForge->field_exists('token_expire', 'jb7_bookings') ){
			$this->dbForge->add_column(
				'jb7_bookings',
				array(
					'token_expire' => array(
						'type'		=> 'BIGINT',
						'null'		=> TRUE
						),
					)
				);
		}

	/* generate refno for existing bookings */
		$q = new HC4_Crud_Q;
		$q->where( 'refno', '=', '' );

		$results = $this->crud->read( $q );
		foreach( $results as $e ){
			$id = $e['id'];
			$refno = $this->repo->generateRefno();
			$values = array(
				'refno'	=> $refno
				);
			$this->crud->update( $id, $values );
		}
	}

	public function version3()
	{
		if( ! $this->dbForge->field_exists('details', 'jb7_bookings') ){
			$this->dbForge->add_column(
				'jb7_bookings',
				array(
					'details' => array(
						'type'		=> 'TEXT',
						'null'		=> FALSE,
						'default'	=> '',
						),
					)
				);

			$sql = 'UPDATE {PREFIX}jb7_bookings SET details = customer';
			$this->db->query( $sql );
			$this->dbForge->drop_column( 'jb7_bookings', 'customer' );
		}
	}
}