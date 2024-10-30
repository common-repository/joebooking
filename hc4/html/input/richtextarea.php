<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Html_Input_RichTextarea
{
	public function render( $name, $value = NULL, $rows = 6 );
}