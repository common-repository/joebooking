<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Settings_Database_Crud_Table
{}

class HC4_Settings_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();

		switch( $appConfig['platform'] ){
			case 'standalone':
			case 'joomla':
				$bind['HC4_Settings_Interface'] = 'HC4_Settings_Database';
				break;

			case 'wordpress':
				$prefix = $appConfig['app-short-name'];
				$settings = new HC4_Settings_Wordpress( $prefix . '_' );
				$bind['HC4_Settings_Interface'] = $settings;
				break;
		}

		return $bind;
	}
}