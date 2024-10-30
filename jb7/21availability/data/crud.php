<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Data_Crud
	extends HC4_Crud_AbstractSql
{
	public function __construct(
		HC4_Crud_SqlTable $sqlTable
		)
	{
		$this->table = 'jb7_availability';
		$this->idField = 'id';
		$this->mapFields = array(
			'id'				=> 'id',
			'from_time'		=> 'from_time',
			'to_time'		=> 'to_time',
			'interval'		=> 'interval',
			'applied_on'	=> 'applied_on',
			'applied_on_details'	=> 'applied_on_details',
			'valid_from_date'	=> 'valid_from_date',
			'valid_to_date'	=> 'valid_to_date',
			'calendar_id'	=> 'calendar_id',
			'priority'		=> 'priority'
			);
	}
}