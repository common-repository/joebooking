<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Crud_Interface
{
	public function read( HC4_Crud_Q $q );
	public function update( $id, array $array );
	public function create( $array );
	public function delete( $id );
}