<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Password
{
	public function __construct(
		HC4_Html_Input_Helper $helper
	)
	{}

	public function render( $name, $value = NULL )
	{
		$value = $this->helper->getValue( $name, $value );

		$out = '<input type="password" name="' . $name . '" value="' . $value . '" class="hc4-form-input">';
		$out = $this->helper->afterRender( $name, $out );
		return $out;
	}
}