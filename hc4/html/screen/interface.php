<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Html_Screen_Interface
{
	public function render( $slug, $result );

	public function css( $slugPreg, $path );
	public function js( $slugPreg, $path );
	public function title( $slugPreg, $value );
	public function breadcrumbTitle( $slugPreg, $value );
	public function menu( $slugPreg, $menuLink, $menuLabel );
	public function layout( $slugPreg, $layout );

	public function getCss( $slug );
	public function getJs( $slug );
	public function getTitle( $slug );
	public function getMenu( $slug );
	public function getBreadcrumb( $slug );
}