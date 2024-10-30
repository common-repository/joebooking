<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
abstract class JB7_31Bookings_Data_Fields_Type
{
	protected $_set = array();

	protected $name;
	protected $label;
	protected $customerAccess = 'viewedit'; // view, viewedit, none

	protected $type = 'text';
	protected $typeLabel = '__Text__';
	protected $details = array();
	protected $value = NULL;
	protected $sortWeight = 1;

	abstract public function renderEdit();
	abstract public function grab( $post );

	public function __clone()
	{
		$this->_set = array();
	}

	public function __set( $name, $value )
	{
		if( ! property_exists($this, $name) ){
			$msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			echo $msg;
			return;
		}

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

	public function render( $value )
	{
		$return = $value;
		if( NULL === $return ){
			if( isset($this->details['default']) ){
				$return = $this->details['default'];
			}
		}
		return $return;
	}
}