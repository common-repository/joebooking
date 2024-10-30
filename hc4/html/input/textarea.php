<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Textarea
	implements HC4_Html_Input_RichTextarea
{
	public function __construct(
		HC4_Html_Input_Helper $helper
	)
	{}

	public function render( $name, $value = NULL, $rows = 6 )
	{
		$value = $this->helper->getValue( $name, $value );
		$out = '<textarea name="' . $name . '" value="' . $value . '" class="hc4-form-input" rows="' . $rows . '">' . $value . '</textarea>';
		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}