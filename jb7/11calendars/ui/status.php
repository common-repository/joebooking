<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Status
{
	public function render( $status )
	{
		$labels = $this->getAllOptions();
		$return = isset( $labels[$status] ) ? $labels[$status] : $status;
		return $return;
	}

	public function getAllOptions()
	{
		$return = array(
			'active'		=> '__Active__',
			'archived'	=> '__Archived__',
			);
		return $return;
	}
}