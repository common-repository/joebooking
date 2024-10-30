<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_98Demo_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_App_Router $router
	)
	{
		$router
			->add( 'GET/demo/*',			'JB7_98Demo_Ui_Init@get' )
			->add( 'GET/demo/massage',	'JB7_98Demo_Ui_Massage@get' )
			;
	}
}