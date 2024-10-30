<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Session_Interface
{
	public function getFlashdata( $key );
	public function setFlashdata( $key, $value, $append = FALSE );
	public function addFlashdata( $key, $value );

	public function getUserdata( $key );
	public function setUserdata( $key, $value, $append = FALSE );
	public function unsetUserdata( $key );
}