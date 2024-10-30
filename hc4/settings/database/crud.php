<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Settings_Database_Crud
{
	/* TO DO: CONFIGURE TABLE NAME */
	// protected $table = 'sh5_conf';

	public function __construct(
		HC4_Database_Interface $db,
		HC4_Database_QueryBuilder $q,
		HC4_Settings_Database_Crud_Table $table
	)
	{}

	public function read()
	{
		$sql = $this->q->get_compiled_select( $this->table );
		$return = $this->db->query( $sql );
		return $return;
	}

	public function create( $name, $value )
	{
		$array = array(
			'name'	=> $name,
			'value'	=> $value,
			);
		$this->q
			->set( $array )
			;
		$sql = $this->q->get_compiled_insert( $this->table );
		$return = $this->db->query( $sql );

		return $return;
	}

	public function deleteByName( $name )
	{
		$this->q
			->where( 'name', $name )
			;
		$sql = $this->q->get_compiled_delete( $this->table );
		$return = $this->db->query( $sql );
		return $return;
	}

	public function update( $name, $value )
	{
		$array = array(
			'value'	=> $value,
			);
		$this->q
			->set( $array )
			->where( 'name', $name )
			;
		$sql = $this->q->get_compiled_update( $this->table );
		$return = $this->db->query( $sql );
		return $return;
	}

	public function deleteByNameValue( $name, $value )
	{
		$this->q
			->where( 'name', $name )
			->where( 'value', $value )
			;
		$sql = $this->q->get_compiled_delete( $this->table );
		$return = $this->db->query( $sql );
		return $return;
	}
}