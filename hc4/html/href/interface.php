<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Html_Href_Interface
{
	public function hrefGet( $slug );
	public function hrefPost( $slug );
	public function hrefApi( $slug );
	public function hrefAsset( $src );
	public function processOutput( $string );
}