<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_42Front_Ui_Index
{
	public function __construct(
		JB7_42Front_Data_Repo $repo,
		HC4_Auth_Interface $auth,
		HC4_Settings_Interface $settings,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function menu()
	{
		$return = array();

		$calendars = $this->repo->getCalendars();
		if( $calendars ){
			$return[] = array( 'front/new', '__New Booking__' );
		}
		$return[] = array( 'front/bookings', '__My Bookings__' );

		return $return;
	}

	public function breadcrumbTitle()
	{
		$return = NULL;

		$menu = $this->screen->getMenu( 'front' );
		if( count($menu) > 1 ){
			$return = '__Menu__';
		}

		return $return;
	}

	public function get( $slug )
	{
		$return = NULL;
		$return = $this->screen->render( $slug, $return );
		return $return;
	}
}