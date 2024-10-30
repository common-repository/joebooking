<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_04Audit_Data_Repo_
{
	public function create( $tableName, $rowId, array $changes );
	public function delete( $tableName, $rowId );
	public function read( $tableName, $rowId );
}

class JB7_04Audit_Data_Repo
	implements JB7_04Audit_Data_Repo_
{
	public function __construct(
		HC4_Time_Interface $t,
		HC4_Auth_Interface $auth,

		JB7_04Audit_Data_Crud $crud
	)
	{}

	public function create( $tableName, $rowId, array $changes )
	{
		$eventDateTime = $this->t->setNow()->getDateTimeDb();
		$userId = $this->auth->getCurrentUserId();

		$values = array();

		foreach( $changes as $k => $v ){
			if( is_array($v[0]) ){
				$vs = $v;
			}
			else {
				$vs = array( $v );
			}

			foreach( $vs as $v ){
				list( $old, $new ) = $v;
				$values[] = array(
					'table_name'		=> $tableName,
					'row_id'				=> $rowId,
					'column_name'		=> $k,
					'old_value'			=> $old,
					'new_value'			=> $new,
					'event_datetime'	=> $eventDateTime,
					'user_id'			=> $userId
					);
			}
		}

		if( $values ){
			foreach( $values as $v ){
				$this->crud->create( $v );
			}
		}

		return $this;
	}

	public function delete( $tableName, $rowId )
	{
		$ids = array();

		$results = $this->read( $tableName, $rowId );
		foreach( $results as $e ){
			$ids[] = $e['id'];
		}

		foreach( $ids as $id ){
			$this->crud->delete( $id );
		}

		return $this;
	}

	public function read( $tableName, $rowId )
	{
		$ids = array();

		$q = new HC4_Crud_Q;
		$q
			->where( 'table_name', '=', $tableName )
			->where( 'row_id', '=', (int) $rowId )
			->sortDesc( 'event_datetime' )
			->sortDesc( 'id' )
			;

		$results = $this->crud->read( $q );

		return $results;
	}
}