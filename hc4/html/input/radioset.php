<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_RadioSet
{
	public function __construct(
		HC4_Html_Input_Radio $inputRadio,
		HC4_Html_Input_Helper $helper
	)
	{}

	public function renderInline( $name, array $options = array(), $value = NULL )
	{
		return $this->render( $name, $options, $value, TRUE );
	}

	public function render( $name, array $options = array(), $value = NULL, $inline = FALSE )
	{
		$value = $this->helper->getValue( $name, $value );

		$out = array();
		$out[] = '<div>';
		foreach( $options as $k => $label ){
			if( $inline ){
				$out[] = '<div class="hc-nowrap hc-lg-inline-block hc-lg-mx1">';
			}
			else {
				$out[] = '<div class="hc-nowrap hc-block hc-my1">';
			}

			$checked = ( $value == $k ) ? TRUE : FALSE;
			$out[] = $this->inputRadio->render( $name, $k, $checked, $label );

			// if( $inline ){
				$out[] = '</div>';
			// }
		}
		$out[] = '</div>';
		$out = join( '', $out );

		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}