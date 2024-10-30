<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Colorpicker
{
	public function __construct(
		HC4_Html_Input_Helper $helper,
		HC4_Html_Input_RadioSet $inputRadioSet
	)
	{}

	public function render( $name, $value = NULL )
	{
		$value = $this->helper->getValue( $name, $value );
		$options = array();

		$colors = array(
'black',
'darkgray',
'gray',
'silver',
'aqua',
'blue',
'navy',
'teal',
'green',
'lightgreen',
'olive',
'lightolive',
'lime',
'yellow',
'lightyellow',
'orange',
'lightorange',
// 'red',
'lightred',
'darkred',
'fuchsia',
'purple',
'maroon',
);

		$options = array();
		foreach( $colors as $color ){
			$bgClass = 'hc-bg-' . $color;
			$options[ $color ] = '<div class="hc-inline-block hc-px1 hc-rounded ' . $bgClass . '">&nbsp;&nbsp;</div>';
		}
		return $this->inputRadioSet->renderInline( $name, $options, $value );
	}
}