<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_21Availability_Ui_Manager_Loader_
{
	public function getManagedCalendars();
}

class JB7_21Availability_Ui_Manager_Loader
	implements JB7_21Availability_Ui_Manager_Loader_
{
	public function __construct(
		JB7_11Calendars_Data_Repo $repoCalendars,
		HC4_Auth_Interface $auth
	)
	{}

	public function getManagedCalendars()
	{
		$return = $this->repoCalendars->findAll();
		$return = array_filter( $return, function( $a ){
			return ( 'active' == $a->status );
		});
		return $return;
	}
}