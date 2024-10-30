<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_03Acl_Data_Repo
{
	public function isAdmin( JB7_01Users_Data_Model $user );

	public function findAdmins();
	public function findCustomers();
}