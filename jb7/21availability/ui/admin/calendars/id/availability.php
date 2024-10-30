<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Admin_Calendars_Id_Availability
{
	public function __construct(
		JB7_21Availability_Data_Repo $repo
	)
	{}

	public function menu( $calendarId )
	{
		$return = array();

		$entries = $this->repo->findAll();
		$entries = array_filter( $entries, function($e) use ($calendarId){
			return ($e->calendar->id == $calendarId);
		});

		$count = count( $entries );
		$label = '__Availability__' . ' [' . $count . ']';
		$return[] = array( 'manager/availability/' . $calendarId, $label  );

		return $return;
	}
}