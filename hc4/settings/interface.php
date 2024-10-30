<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Settings_Interface
{
	public function get( $name, $defaultValue2 = NULL );
	public function set( $name, $value );
	public function reset( $name );
	public function resetAll();
	public function init( $name, $value );
}