<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_99App_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_App_Config $appConfig,

		HC4_Settings_Interface $settings,
		HC4_Time_Interface $t,
		HC4_Time_Format $tf,

		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
		$tf->dateFormat = $settings->get( 'datetime_date_format', 'j M Y' );
		$tf->timeFormat = $settings->get( 'datetime_time_format', 'g:ia' );
		$t->weekStartsOn = $settings->get( 'datetime_week_starts', 0 );

		$screen
			->css( '*',	'hc4/assets/css/hc.css' )
			->css( '*',	'hc4/assets/css/hc4-theme.css' )
			->layout( '*',	'JB7_99App_Ui_Layout' )
			;

		switch( $appConfig['platform'] ){
			case 'standalone':
				$screen
					->css( '*',	'hc4/assets/css/hc-start.css' )
					->css( '*',	'https://fonts.googleapis.com/css?family=PT+Sans' )
					;

				if( 0 OR (! defined('HC4_DEV_INSTALL')) ){
					$screen
						->css( '*',	'https://fonts.googleapis.com/css?family=PT+Sans' )
						;
				}
				break;
		}

		$router
			->add( 'GET',	'JB7_99App_Ui_Index@get' )
			;

		$screen
			// ->title( '',	'__Home__' )
			->title( '', 	'JB7_99App_Ui_Index@title' )
			;
	}
}