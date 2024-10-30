<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Data_Sync_Crud
	extends HC4_Crud_AbstractSql
{
	public function __construct(
		HC4_Crud_SqlTable $sqlTable
		)
	{
		$this->table = 'jb7_availability_sync';
		$this->idField = 'id';
		$this->mapFields = array(
			'id'						=> 'id',
			'from_calendar_id'	=> 'from_calendar_id',
			'to_calendar_id'		=> 'to_calendar_id',
			);
	}
}