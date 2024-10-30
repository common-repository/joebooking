<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Translate_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();

		$translate = NULL;
		$myAppName = $appConfig['app-name'];

		switch( $appConfig['platform'] ){
			case 'standalone':
			case 'joomla':
				$langDir = $appConfig['app-dir'] . '/languages';
				$locale = '';
				$translate = new HC4_Translate_GetText( $myAppName, $langDir, $locale );
				break;

			case 'wordpress':
				$translate = new HC4_Translate_Wordpress( $myAppName, $appConfig['app-dir'] );
				break;
		}

		$bind['HC4_Translate_Interface'] = $translate;

		return $bind;
	}
}