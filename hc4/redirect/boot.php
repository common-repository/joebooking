<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Redirect_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();

		switch( $appConfig['platform'] ){
			case 'standalone':
			case 'joomla':
				$bind['HC4_Redirect_Interface'] = 'HC4_Redirect_Header';
				break;

			case 'wordpress':
				$bind['HC4_Redirect_Interface'] = 'HC4_Redirect_Wordpress';
				break;
		}

		return $bind;
	}
}