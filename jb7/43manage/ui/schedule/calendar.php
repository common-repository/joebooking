<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Schedule_Calendar
{
	public function __construct(
		JB7_11Calendars_Ui_Title $viewTitle
	)
	{}

	public function render( JB7_11Calendars_Data_Model $calendar )
	{
		$return = $this->viewTitle->render( $calendar );
		return $return;
	}
}