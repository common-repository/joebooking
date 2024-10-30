<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Settings_Wordpress
	extends HC4_Settings_Abstract
	implements HC4_Settings_Interface
{
	protected $prefix = NULL;
	protected $loaded = array();

	public function __construct( $prefix )
	{
		$this->prefix = $prefix;
	}

	public function set( $name, $value )
	{
		update_option( $this->prefix . $name, $value );

		$this->loaded[ $name ] = $value;
		return $this;
	}

	public function reset( $name )
	{
		delete_option( $this->prefix . $name );
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
			$return = get_option( $this->prefix . $name, $defaultValue );
			$this->loaded[$name] = $return;
		}

		if( is_array($defaultValue) && (! is_array($return)) ){
			$return = ( NULL === $return ) ? array() : array($return);
		} 

		if( (! is_array($defaultValue)) && is_array($return) ){
			$return = array_shift( $return );
		}

		return $return;
	}
}