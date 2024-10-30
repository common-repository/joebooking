<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Ui_Date
{
	public function __construct(
		HC4_Time_Format $tf
	)
	{}

	public function render( JB7_31Bookings_Data_Model $model )
	{
		return $this->tf->formatDateWithWeekday( $model->startDateTime );
	}
}