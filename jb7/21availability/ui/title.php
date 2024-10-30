<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Title
{
	public function __construct(
		HC4_Time_Format $tf
	)
	{}

	public function render( JB7_21Availability_Data_Model $e )
	{
		$timeView = $this->tf->formatTimeTimeRange( $e->fromTime, $e->toTime );
		$intervalView = $this->tf->formatDuration( $e->interval );

		$return = $timeView . ' / ' . $intervalView;
		return $return;
	}
}