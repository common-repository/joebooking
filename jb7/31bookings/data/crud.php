<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Data_Crud
	extends HC4_Crud_AbstractSql
{
	public function __construct(
		HC4_Crud_SqlTable $sqlTable
		)
	{
		$this->table = 'jb7_bookings';
		$this->idField = 'id';
		$this->mapFields = array(
			'id'					=> 'id',
			'start_datetime'	=> 'start_datetime',
			'end_datetime'		=> 'end_datetime',
			'status'				=> 'status',
			'calendar_id'		=> 'calendar_id',
			'details'			=> 'details',
			'refno'				=> 'refno',
			'token'				=> 'token',
			'token_expire'		=> 'token_expire',
		);
	}
}