<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Redirect_Interface
{
	public function call( $to );
}