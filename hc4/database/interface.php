<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Database_Interface
{
	public function query( $sql );
	public function insertId();
}