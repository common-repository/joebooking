<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_App_Data_Model
{
	protected static $props = array(
		'id'				=> 0,
		'title'			=> '',
		'description'	=> '',
		'status'			=> array( 'active', 'pending', 'archived', 'suspended' ),
		);
	protected $data = array();

	public function __get( $name )
	{
		if( ! array_key_exists($name, static::$props) ){
			$msg = "PROPERTY '$name' IS NOT DEFINED FOR " . __CLASS__;
			echo $msg . '<br>';
			// throw new HC4_App_Exception_DataError( "PROPERTY '$name' IS NOT DEFINED FOR " . __CLASS__ );
		}

		if( array_key_exists($name, $this->data) ){
			return $this->data[$name];
		}

		$thisProp = static::$props[$name];

	// OBJECT?
		if( is_string($thisProp) && ('{' == substr($thisProp, 0, 1)) && ('}' == substr($thisProp, -1, 1)) ){
			$className = substr( $thisProp, 1, -1 );
			if( $className == get_class($this) ){
				$return = NULL;
			}
			else {
				$return = new $className;
			}
			return $return;
		}

	// ARRAY OF OBJECTS?
		if( is_array($thisProp) && (count($thisProp) == 1) && ('{' == substr($thisProp[0], 0, 1)) && ('}' == substr($thisProp[0], -1, 1)) ){
			$className = substr( $thisProp[0], 1, -1 );
			$return = array();
			return $return;
		}

		if( is_string($thisProp) OR is_int($thisProp) OR is_float($thisProp) ){
			return $thisProp;
		}

		if( is_array($thisProp) && (count($thisProp) > 1) ){
			reset( $thisProp );
			return current( $thisProp );
		}

		$msg = "PROPERTY '$name' IS NOT SET FOR " . __CLASS__;
		echo $msg . '<br>';
		// throw new HC4_App_Exception_DataError( "PROPERTY '$name' IS NOT SET FOR " . __CLASS__ );
	}

	public function __set( $name, $value )
	{
		if( ! array_key_exists($name, static::$props) ){
			$msg = "PROPERTY '$name' IS NOT DEFINED FOR " . __CLASS__;
			echo $msg . '<br>';
			// throw new HC4_App_Exception_DataError( "PROPERTY '$name' IS NOT DEFINED FOR " . __CLASS__ );
		}

		$thisProp = static::$props[$name];

		if( is_int($thisProp) ){
			if( is_int($value) OR is_string($value) OR is_null($value) ){
				$this->data[ $name ] = (int) $value;
				return;
			}

			$msg = __CLASS__ . ":$name SHOULD BE INTEGER! " . var_export($value, TRUE);
			echo $msg . '<br>';
			// throw new HC4_App_Exception_DataError( __CLASS__ . ":$name SHOULD BE INTEGER! " . var_export($value, TRUE) );
		}

	// OBJECT?
		if( is_string($thisProp) && ('{' == substr($thisProp, 0, 1)) && ('}' == substr($thisProp, -1, 1)) ){
			$className = substr( $thisProp, 1, -1 );
			if( $value instanceof $className ){
				$this->data[ $name ] = $value;
				return;
			}

			$msg = __CLASS__ . ":$name SHOULD BE INSTANCE OF " . $className . ' ' . var_export($value, TRUE);
			echo $msg . '<br>';
		}

	// ARRAY OF OBJECTS?
		if( is_array($thisProp) && (count($thisProp) == 1) && ('{' == substr($thisProp[0], 0, 1)) && ('}' == substr($thisProp[0], -1, 1)) ){
			$className = substr( $thisProp[0], 1, -1 );
			if( ! is_array($value) ){
				$msg = __CLASS__ . ":$name SHOULD BE ARRAY OF " . $className . ': ' . var_export($value, TRUE);
				echo $msg . '<br>';
			}

			$prop = array();
			foreach( $value as $v ){
				if( ! ($v instanceof $className) ){
					$msg = __CLASS__ . ":$name SHOULD BE INSTANCE OF " . $className . ': ' . var_export($v, TRUE);
					echo $msg . '<br>';
				}

				$prop[] = $v;
			}

			$this->data[ $name ] = $prop;
			return;
		}

		if( is_string($thisProp) ){
			if( is_int($value) OR is_string($value) OR is_null($value) ){
				$this->data[ $name ] = '' . $value;
				return;
			}

			$msg = __CLASS__ . ":$name SHOULD BE STRING! " . var_export($value, TRUE);
			echo $msg . '<br>';
		}

		if( is_array($thisProp) && (count($thisProp) > 1) ){
			if( in_array($value, $thisProp) ){
				$this->data[ $name ] = $value;
				return;
			}

			$msg = __CLASS__ . ":$name SHOULD BE FROM " . join(', ', static::$props[$name]) . ': ' . var_export($value, TRUE);
			echo $msg . '<br>';
		}
	}
}