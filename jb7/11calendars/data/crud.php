<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Data_Crud
	extends HC4_Crud_AbstractSql
{
	public function __construct(
		HC4_Crud_SqlTable $sqlTable
		)
	{
		$this->table = 'jb7_calendars';
		$this->idField = 'id';
		$this->mapFields = array(
			'id'			=> 'id',
			'title'		=> 'title',
			'status'		=> 'status',
			'description'	=> 'description',
			'slot_size'		=> 'slot_size',
			'capacity'		=> 'capacity',
			'initial_status'	=> 'initial_status',
		);
	}
}