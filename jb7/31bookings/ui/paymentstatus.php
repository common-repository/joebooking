<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Ui_PaymentStatus
{
	public function render( $status )
	{
		$options = JB7_31Bookings_Data_Model::getPaymentStatuses();

		if( isset($options[$status]) ){
			$label = $options[$status][0];
			$icon = $this->renderIcon( $status );
			$return = $icon . ' ' . $label;
		}
		else {
			$return = $status;
		}

		return $return;
	}

	public function renderIcon( $status )
	{
		$return = NULL;

		$options = JB7_31Bookings_Data_Model::getPaymentStatuses();
		if( isset($options[$status]) ){
			$label = $options[$status][0];
			$color = $this->getColor( $status );

			$bgClass = 'hc-bg-' . $color;
			$colorView = '<div class="hc-px1 hc-pos-relative hc-inline-block hc-rounded hc-border ' . $bgClass . '" title="' . $label . '">&nbsp;</div>';
			$return = $colorView;
		}

		return $return;
	}


	public function getColor( $status )
	{
		$return = NULL;
		$options = JB7_31Bookings_Data_Model::getPaymentStatuses();
		if( isset($options[$status]) ){
			$return = $options[$status][1];
		}
		return $return;
	}
}