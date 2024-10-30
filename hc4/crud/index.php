<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Crud_Index
	implements HC4_App_Module_Interface
{
	public static function import( array $appConfig )
	{
		$r = array();
		$r[] = 'hc4_database';
		return $r;
	}
}