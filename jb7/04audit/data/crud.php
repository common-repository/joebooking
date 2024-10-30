<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Audit_Data_Crud
	extends HC4_Crud_AbstractSql
{
	public function __construct(
		HC4_Crud_SqlTable $sqlTable
		)
	{
		$this->table = 'jb7_audit';
		$this->idField = 'id';
		$this->mapFields = array(
			'id'					=> 'id',
			'table_name'		=> 'table_name',
			'row_id'				=> 'row_id',
			'column_name'		=> 'column_name',
			'old_value'			=> 'old_value',
			'new_value'			=> 'new_value',
			'event_datetime'	=> 'event_datetime',
			'user_id'			=> 'user_id',
			'event_comment'	=> 'event_comment'
		);
	}
}