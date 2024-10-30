<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Html_Screen_Layout_Interface
{
	public function render( $slug, $content, $title = NULL, array $menu = array(), array $breadcrumb = array() );
}