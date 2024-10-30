<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Title
{
	public function __construct(
		JB7_11Calendars_Ui_Status $viewStatus
	)
	{}

	public function render( JB7_11Calendars_Data_Model $model )
	{
		$return = $model->title;

		if( 'active' != $model->status ){
			$return .= ' &mdash; ' . $this->viewStatus->render( $model->status );
		}

		return $return;
	}

	public function renderFull( JB7_11Calendars_Data_Model $model )
	{
		$return = array();
		$return[] = $this->render( $model );
		$return = join( ' / ', $return );
		return $return;
	}
}