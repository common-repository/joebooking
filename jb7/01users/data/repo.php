<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_01Users_Data_Repo
{
	public function findAll();
	public function findById( $id );
	public function findByUsername( $username );

	public function create( JB7_01Users_Data_Model $model );
	public function setPassword( JB7_01Users_Data_Model $model, $password );
	public function checkPassword( JB7_01Users_Data_Model $model, $password );
}