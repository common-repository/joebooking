<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Time_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();
		$bind['HC4_Time_Interface'] = 'HC4_Time_Implementation';
		return $bind;
	}
}