<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Data_Model_Details
{
	private $_set = array();

	public function __clone()
	{
		$this->_set = array();
	}

	public function __set( $name, $value )
	{
		// if( ! property_exists($this, $name) ){
			// $msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			// echo $msg;
			// return;
		// }

		if( array_key_exists($name, $this->_set) ){
			$msg = 'Property already set: ' . __CLASS__ . ': ' . $name;
			echo $msg;
			return;
		}

		$this->{$name} = $value;
		$this->_set[$name] = 1;
	}

	public function __get( $name )
	{
		if( ! property_exists($this, $name) ){
			$msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			echo $msg;
		}

		return $this->{$name};
	}
}