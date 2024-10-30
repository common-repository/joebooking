<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Ui_Status
{
	public function render( $status )
	{
		$options = JB7_31Bookings_Data_Model::getStatuses();

		if( isset($options[$status]) ){
			$label = $this->renderText( $status );
			$color = $this->getColor( $status );

			$bgClass = 'hc-bg-' . $color;
			$bgClass2 = '';

			$lock = $options[$status][2];
			if( ! $lock ){
				$bgClass2 .= 'hc-bg-striped2';
			}

			$colorView = '<div class="hc-pos-relative hc-inline-block hc-rounded hc-border ' . $bgClass . '" title="' . $label . '"><div class="hc-px1 ' . $bgClass2 . '" style="top: 0; bottom: 0; left: 0; right: 0; z-index: -1;">&nbsp;</div></div>';
			$return = $colorView . ' ' . $label;
		}
		else {
			$return = $status;
		}

		return $return;
	}

	public function renderText( $status )
	{
		$options = JB7_31Bookings_Data_Model::getStatuses();

		if( isset($options[$status]) ){
			$label = $options[$status][0];
			return $label;
		}
		else {
			$return = $status;
		}

		return $return;
	}

	public function getColor( $status )
	{
		$return = NULL;
		$options = JB7_31Bookings_Data_Model::getStatuses();
		if( isset($options[$status]) ){
			$return = $options[$status][1];
		}
		return $return;
	}
}