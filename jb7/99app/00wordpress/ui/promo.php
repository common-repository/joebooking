<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_99App_00WordPress_Ui_Promo
	implements JB7_99App_Ui_Promo
{
	public function __construct(
	)
	{}

	public function render( $slug )
	{
		if( 'front' == substr( $slug, 0, strlen('front') ) ){
			return;
		}

		if( ! is_admin() ){
			return;
		}

		ob_start();
?>

<?php if( is_admin() ) : ?>
	<div class="update-nag hc-block hc-fs4 hc-my3">
<?php else : ?>
	<div class="hc-border hc-border-olive hc-rounded hc-p3 hc-block hc-fs4">
<?php endif; ?>
<span class="dashicons dashicons-star-filled hc-olive"></span> <a target="_blank" href="https://www.joebooking.com/order/"><strong>JoeBooking Pro</strong></a> with nice features like payment management, custom fields and more!
</div>

<?php 
		return ob_get_clean();
	}
}