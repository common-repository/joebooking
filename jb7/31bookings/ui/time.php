<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Ui_Time
{
	public function __construct(
		HC4_Time_Format $tf
	)
	{}

	public function render( JB7_31Bookings_Data_Model $model )
	{
		$range = new HC4_Time_Range( $model->startDateTime, $model->endDateTime );
		$return = $this->tf->formatTimeRange( $range );
		return $return;
	}
}