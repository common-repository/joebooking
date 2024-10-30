<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_42Front_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_App_Router $router,
		HC4_Html_Screen_Interface $screen
	)
	{
	// UI
		$router
			->add( 'GET/front/new/confirm/[:dt]',	'JB7_42Front_Ui_New_Confirm@get' )
			->add( 'POST/front/new/confirm/[:dt]',	'JB7_42Front_Ui_New_Confirm@post' )

			->add( 'GET/front/new',						'JB7_42Front_Ui_New_Index@get' )
			->add( 'GET/front/new/date/[:date]',	'JB7_42Front_Ui_New_Index@get' )

			->add( 'GET/front',			'JB7_42Front_Ui_Index@get' )

			->add( 'GET/front/new/thankyou',			'JB7_42Front_Ui_New_ThankYou@get' )

			->add( 'GET/front/bookings',		'JB7_42Front_Ui_Bookings_Index@get' )
			->add( 'POST/front/bookings',		'JB7_42Front_Ui_Bookings_Index@post' )

			->add( 'GET/front/bookings/[:token]',		'JB7_42Front_Ui_Bookings_Token@get' )
			;

		$screen
			->title( 'front/new/thankyou',		'__Thank You__' )
			->menu( 'front',		'JB7_42Front_Ui_Index@menu' )
			->breadcrumbTitle( 'front',		'JB7_42Front_Ui_Index@breadcrumbTitle' )
			->title( 'front/bookings',		'__My Bookings__' )
			->title( 'front/bookings/[:token]',		'JB7_42Front_Ui_Bookings_Token@title' )
			;

		$screen
			->js( 'front',	'jb7/42front/assets/front.js' )
			;
	}
}