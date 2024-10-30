<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Radio
{
	public function __construct(
		HC4_Html_Input_Helper $helper
	)
	{}

	public function render( $name, $k, $checked = FALSE, $label = NULL )
	{
		// $checked = $this->helper->getValue( $name, $checked );

		$out = array();

		$out[] = '<label class="hc-block hc-xs-py1">';

		$out[] = '<input type="radio" class="hc4-input-radio" name="' . $name . '" value="' . $k . '"';
		if( $checked ){
			$out[] = ' checked="checked"';
		}
		$out[] = '>';

		if( NULL !== $label ){
			$out[] = $label;
		}

		$out[] = '</label>';

		$out = join( '', $out );
		// $out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}