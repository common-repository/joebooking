<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Csrf_Interface
{
	public function checkInput();
	public function render( $output );
}