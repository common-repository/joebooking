<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Crud_SqlTable
{
	public function __construct(
		HC4_Database_Interface $db,
		HC4_Database_QueryBuilder $qb
		)
	{}

	public function read( $tableName, HC4_Crud_Q $q, array $mapFields = array() )
	{
		$return = array();

	// SEARCH
		$search = $q->getSearch();
		if( strlen($search) ){
			reset( $this->searchIn );
			$this->qb->or_group_start();
			foreach( $this->searchIn as $k ){
				if( isset($this->mapFields[$k]) ){
					$k = $this->mapFields[$k];
				}
				$this->qb->or_like( $k, $search );
			}
			$this->qb->group_end();
		}

	// WHERE
		$where = $q->getWhere();
		foreach( $where as $w ){
			list( $k, $compare, $v ) = $w;

			if( isset($mapFields[$k]) ){
				$k = $mapFields[$k];
			}

			if( is_array($v) && (count($v) == 1) ){
				$v = array_shift( $v );
			}

			switch( $compare ){
				case 'LIKE':
					$this->qb->like( $k, $v );
					break;

				case '=':
					if( is_array($v) ){
						$this->qb->where_in( $k, $v );
					}
					else {
						$this->qb->where( $k, $v );
					}
					break;

				case '<>':
					if( is_array($v) ){
						$this->qb->where_not_in( $k, $v );
					}
					else {
						$this->qb->where( $k . '<>', $v );
					}
					break;

				default:
					$how = ' ' . $compare;
					$escape = TRUE;
					$this->qb->where( $k . $how, $v, $escape );
					break;
			}
		}

	// SORT
		$sort = $q->getSort();
		foreach( $sort as $k ){
			if( isset($mapFields[$k]) ){
				$k = $mapFields[$k];
			}
			$this->qb->order_by( $k, 'ASC' );
		}

		$sortDesc = $q->getSortDesc();
		foreach( $sortDesc as $k ){
			if( isset($mapFields[$k]) ){
				$k = $mapFields[$k];
			}
			$this->qb->order_by( $k, 'DESC' );
		}

	// LIMIT
		$limit = $q->getLimit();
		$offset = $q->getOffset();

		if( (NULL !== $limit) && (NULL !== $offset) ){
			$this->qb->limit( $limit, $offset );
		}
		elseif( NULL !== $limit ){
			$this->qb->limit( $limit );
		}
		elseif( NULL !== $offset ){
			$this->qb->limit( NULL, $offset );
		}

	// SELECT
		$this->qb->select( $tableName . '.*' );

		$select = $q->getSelect();
		if( $select ){
			foreach( $select as $k ){
				if( isset($mapFields[$k]) ){
					$k = $mapFields[$k];
				}
				$this->qb->select( $k );
			}
		}

		$sql = $this->qb->get_compiled_select( $tableName );
		$results = $this->db->query( $sql );

		$return = array();
		if( $results ){
			foreach( $results as $e ){
				if( $mapFields ){
					$e = $this->_convertFrom( $e, $mapFields );
				}
				$return[ $e['id'] ] = $e;
			}
		}

		return $return;
	}

	public function create( $tableName, array $values, array $mapFields = array() )
	{
		$values = $this->_convertTo( $values, $mapFields );

		$this->qb->set( $values );

		$sql = $this->qb->get_compiled_insert( $tableName );
// echo $sql . '<br>';
		$result = $this->db->query( $sql );
		$id = $this->db->insertId();

		return $id;
	}

	public function createBatch( $tableName, array $values, array $mapFields = array() )
	{
		for( $ii = 0; $ii < count($values); $ii++ ){
			$values[$ii] = $this->_convertTo( $values[$ii], $mapFields );
		}

		$sqls = $this->qb->get_compiled_insert_batch( $tableName, $values );
		foreach( $sqls as $sql ){
// echo $sql . '<br>';
			$result = $this->db->query( $sql );
		}
		return TRUE;
	}

	public function update( $tableName, $id, array $values, array $mapFields = array(), $idField = 'id' )
	{
		$values = $this->_convertTo( $values, $mapFields );

		$this->qb
			->where( $idField, $id )
			->set( $values )
			;

		$sql = $this->qb->get_compiled_update( $tableName );
// echo $sql . '<br>';
		$result = $this->db->query( $sql );

		return $values;
	}

	public function delete( $tableName, $id, $idField = 'id' )
	{
		$this->qb->where( $idField, $id );
		$sql = $this->qb->get_compiled_delete( $tableName );
		$result = $this->db->query( $sql );
		return $id;
	}

	public function deleteAll( $tableName )
	{
		$this->qb->where( 1, 1 );

		$sql = $this->qb->get_compiled_delete( $tableName );
		$result = $this->db->query( $sql );

		$return = TRUE;
		return $return;
	}

	protected function _convertTo( array $array, array $mapFields )
	{
		$array = array_map( 
			function($v){ return is_array($v) ? HC4_App_Functions::glueArray($v) : $v; },
			$array
		);

		if( ! $mapFields ){
			return $array;
		}

		$return = $array;

		reset( $mapFields );
		foreach( $mapFields as $myField => $dbField ){
			if( $myField == $dbField ){
				continue;
			}
			if( ! array_key_exists($myField, $return) ){
				continue;
			}
			$return[ $dbField ] = $array[ $myField ];
			unset( $return[ $myField ] );
		}

		return $return;
	}

	protected function _convertFrom( array $dbArray, array $mapFields )
	{
		if( ! $mapFields ){
			return $dbArray;
		}

		$return = $dbArray;
		reset( $mapFields );
		foreach( $mapFields as $myField => $dbField ){
			if( $myField == $dbField ){
				continue;
			}
			$return[ $myField ] = $dbArray[ $dbField ];
			unset( $return[ $dbField ] );
		}

		return $return;
	}
}