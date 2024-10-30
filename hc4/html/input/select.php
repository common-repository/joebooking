<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Select
{
	public function __construct(
		HC4_Html_Input_Helper $helper
	)
	{}

	public function render( $name, array $options = array(), $value = NULL )
	{
		$value = $this->helper->getValue( $name, $value );

		$out = array();
		$out[] = '<select name="' . $name . '" class="hc4-form-input">';
		foreach( $options as $k => $label ){
			$kHtml = htmlspecialchars( $k );
			$labelHtml = htmlspecialchars( $label );

			$out[] = '<option value="' . $kHtml . '"';
			if( $value == $k ){
				$out[] = ' selected="selected"';
			}
			$out[] = '>';
			$out[] = $label;
			$out[] = '</option>';
		}
		$out[] = '</select>';
		$out = join( '', $out );

		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}