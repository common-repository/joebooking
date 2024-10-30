<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_04Notifications_Service_Parser
{
	public function __construct(
		JB7_31Bookings_Ui_Date $viewDate,
		JB7_31Bookings_Ui_Time $viewTime,
		JB7_31Bookings_Ui_Status $viewStatus,
		JB7_31Bookings_Ui_Refno $viewRefno,
		JB7_31Bookings_Ui_Details $viewDetails,
		JB7_11Calendars_Ui_Title $viewCalendar
	)
	{}

	public function parse( $string, JB7_31Bookings_Data_Model $booking )
	{
		$tags = $this->getTags();
		foreach( $tags as $tag => $func ){
			$string = str_replace( $tag, call_user_func($func, $booking), $string );
		}
		return $string;
	}

	public function getTags()
	{
		$return = array();
		$return['{CALENDAR}'] = array( $this, 'parseCalendar' );
		$return['{DATE}'] = array( $this, 'parseDate' );
		$return['{TIME}'] = array( $this, 'parseTime' );
		$return['{STATUS}'] = array( $this, 'parseStatus' );
		$return['{DETAILS}'] = array( $this, 'parseDetails' );
		$return['{REFNO}'] = array( $this, 'parseRefno' );
		$return['{ID}'] = array( $this, 'parseId' );
		return $return;
	}

	public function parseId( JB7_31Bookings_Data_Model $booking )
	{
		return $booking->id;
	}

	public function parseRefno( JB7_31Bookings_Data_Model $booking )
	{
		$return = $this->viewRefno->render( $booking->refno );
		return $return;
	}

	public function parseCalendar( JB7_31Bookings_Data_Model $booking )
	{
		$return = $this->viewCalendar->render( $booking->calendar );
		return $return;
	}

	public function parseDate( JB7_31Bookings_Data_Model $booking )
	{
		$return = $this->viewDate->render( $booking );
		return $return;
	}

	public function parseTime( JB7_31Bookings_Data_Model $booking )
	{
		$return = $this->viewTime->render( $booking );
		return $return;
	}

	public function parseStatus( JB7_31Bookings_Data_Model $booking )
	{
		$return = $this->viewStatus->renderText( $booking->status );
		return $return;
	}

	public function parseDetails( JB7_31Bookings_Data_Model $booking )
	{
		$return = $this->viewDetails->renderText( $booking->details );
		return $return;
	}
}