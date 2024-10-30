<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Migration_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();
		$bind['HC4_Migration_Interface'] = 'HC4_Migration_Settings';
		return $bind;
	}
}