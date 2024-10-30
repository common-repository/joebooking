<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Access
{
	public function render( $access )
	{
		$labels = $this->getAllOptions();
		$return = isset( $labels[$access] ) ? $labels[$access] : $access;
		return $return;
	}

	public function getAllOptions()
	{
		$return = array(
			'public'	=> '__Public__',
			// 'login'		=> '__Login Required__',
			'private'	=> '__Private__',
			);
		return $return;
	}
}