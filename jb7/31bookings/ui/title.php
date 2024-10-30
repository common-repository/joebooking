<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Ui_Title
{
	public function __construct(
		JB7_11Calendars_Ui_Title $viewCalendar,
		JB7_31Bookings_Ui_Date $viewDate,
		JB7_31Bookings_Ui_Time $viewTime
	)
	{}

	public function render( JB7_31Bookings_Data_Model $model )
	{
		$return = array();

		$return[] = $this->viewDate->render( $model );
		$return[] = $this->viewTime->render( $model );
		$return[] = $this->viewCalendar->render( $model->calendar );

		$return = join( ', ', $return );

		return $return;
	}
}