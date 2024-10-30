<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Hidden
{
	public function __construct(
		HC4_Html_Input_Helper $helper
	)
	{}

	public function render( $name, $value = NULL )
	{
		$value = $this->helper->getValue( $name, $value );
		$out = '<input type="hidden" name="' . $name . '" value="' . $value . '">';
		// $out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}