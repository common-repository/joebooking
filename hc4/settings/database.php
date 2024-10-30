<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Settings_Database
	extends HC4_Settings_Abstract
	implements HC4_Settings_Interface
{
	protected $loaded = array();

	public function __construct(
		HC4_Settings_Database_Crud $crud
	)
	{
		$results = $crud->read();
		if( $results ){
			foreach( $results as $va ){
				$k = $va['name'];
				$v = $va['value'];

				$v2 = json_decode( $v, TRUE );
				if( JSON_ERROR_NONE == json_last_error() ){
					$v = $v2;
				}

				$this->loaded[$k] = $v;
			}
		}
	}

	public function set( $name, $value )
	{
		if( is_array($value) ){
			$value = json_encode( $value );
		}

		if( array_key_exists($name, $this->loaded) ){
			if( $value != $this->loaded[$name] ){
				$this->crud->update( $name, $value );
			}
		}
		else {
			$this->crud->create( $name, $value );
		}

		$this->loaded[ $name ] = $value;
		return $this;
	}

	public function reset( $name )
	{
		if( array_key_exists($name, $this->loaded) ){
			$this->crud->deleteByName( $name );
		}
		unset( $this->loaded[$name] );
		return $this;
	}

	public function resetAll()
	{
		$names = array_keys( $this->loaded );
		foreach( $names as $name ){
			$this->reset( $name );
		}
		return $this;
	}

	public function get( $name, $defaultValue2 = NULL )
	{
		$return = NULL;

		if( array_key_exists( $name, $this->_defaults ) ){
			$defaultValue = $this->_defaults[$name];
		}
		else {
			$defaultValue = $defaultValue2;
		}

		if( array_key_exists($name, $this->loaded) ){
			$return = $this->loaded[$name];
		}
		else {
			$return = $defaultValue;
		}

		if( is_array($defaultValue) && (! is_array($return)) ){
			$return = ( NULL === $return ) ? array() : array($return);
		} 

		// if( (! is_array($defaultValue)) && is_array($return) ){
			// $return = array_shift( $return );
		// }

		return $return;
	}
}