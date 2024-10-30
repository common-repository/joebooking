<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
abstract class HC4_Settings_Abstract
{
	protected $_defaults = array();

	public function init( $name, $value )
	{
		$this->_defaults[ $name ] = $value;
		return $this;
	}
}