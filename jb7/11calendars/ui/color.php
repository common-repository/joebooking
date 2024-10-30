<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Color
{
	public function render( JB7_11Calendars_Data_Model $model )
	{
		$color = $model->color;
		$bgClass = 'hc-bg-' . $color;
		$return = '<div class="hc-inline-block hc-px1 hc-rounded ' . $bgClass . '">&nbsp;&nbsp;</div>';
		return $return;
	}
}