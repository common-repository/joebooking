<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Auth_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();

		switch( $appConfig['platform'] ){
			case 'standalone':
				$bind['HC4_Auth_Interface'] = 'HC4_Auth_Standalone';
				break;

			case 'joomla':
				$bind['HC4_Auth_Interface'] = 'HC4_Auth_Joomla';
				break;

			case 'wordpress':
				$bind['HC4_Auth_Interface'] = 'HC4_Auth_Wordpress';
				break;
		}

		return $bind;
	}
}