<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Data_Crud
	extends HC4_Crud_AbstractSql
{
	public function __construct(
		HC4_Crud_SqlTable $sqlTable
		)
	{
		$this->table = 'jb7_notification_templates';
		$this->idField = 'id';
		$this->mapFields = array(
			'id'				=> 'id',
			'message_id'	=> 'message_id',
			'subject'		=> 'subject',
			'body'			=> 'body',
			'is_disabled'	=> 'is_disabled'
		);
	}
}