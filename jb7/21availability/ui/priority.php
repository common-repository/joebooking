<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Priority
{
	public function render( $priority )
	{
		$options = array(
			3	=> '__High Priority__',
			2	=> '__Normal Priority__',
			1	=> '__Low Priority__',
			);

		$return = isset( $options[$priority] ) ? $options[$priority] : $priority;
		return $return;
	}
}