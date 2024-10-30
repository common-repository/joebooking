<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Crud_Q_
{
	public function where( $column, $compare, $value );
	public function whereOr( $column, $compare, $value );
	public function sort( $column );
	public function sortDesc( $column );
	public function limit( $limit );
	public function offset( $offset );
	public function search( $search );
	public function select( $field );

	public function getWhere();
	public function getWhereOr();
	public function getSort();
	public function getSortDesc();
	public function getLimit();
	public function getOffset();
	public function getSearch();
	public function getSelect();
}

class HC4_Crud_Q implements HC4_Crud_Q_
{
	protected $where = array();
	protected $whereOr = array();
	protected $sort = array();
	protected $sortDesc = array();
	protected $limit = NULL;
	protected $offset = NULL;
	protected $search = NULL;
	protected $select = array();

	public function search( $search )
	{
		$this->search = $search;
		return $this;
	}

	public function select( $field )
	{
		$this->select[] = $field;
		return $this;
	}

	public function getSelect()
	{
		return $this->select;
	}

	public function getSearch()
	{
		return $this->search;
	}

	public function limit( $limit )
	{
		$this->limit = $limit;
		return $this;
	}

	public function getLimit()
	{
		return $this->limit;
	}

	public function offset( $offset )
	{
		$this->offset = $offset;
		return $this;
	}

	public function getOffset()
	{
		return $this->offset;
	}

	public function sort( $column )
	{
		$this->sort[] = $column;
		return $this;
	}

	public function sortDesc( $column )
	{
		$this->sortDesc[] = $column;
		return $this;
	}

	public function getSort()
	{
		return $this->sort;
	}

	public function getSortDesc()
	{
		return $this->sortDesc;
	}

	public function where( $column, $compare, $value )
	{
		$this->_checkCompare( $compare );
		$this->whereOr = array();
		$this->where[] = array( $column, $compare, $value );
		return $this;
	}

	public function whereOr( $column, $compare, $value )
	{
		$this->_checkCompare( $compare );
		$this->where = array();
		$this->whereOr[] = array( $column, $compare, $value );
		return $this;
	}

	public function getWhere()
	{
		return $this->where;
	}

	public function getWhereOr()
	{
		return $this->whereOr;
	}

	protected function _checkCompare( $compare )
	{
		$allowed = array( '=', '<>', '>', '<', '>=', '<=', 'LIKE' );
		if( ! in_array($compare, $allowed) ){
			$msg = "compare by '$compare' is not allowed";
			throw new Exception( $msg );
		}

		return TRUE;
	}
}