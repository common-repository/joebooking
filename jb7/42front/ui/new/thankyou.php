<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_42Front_Ui_New_ThankYou
{
	public function __construct(
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug )
	{
		$return = $this->render();
		$return = $this->screen->render( $slug, $return );

		return $return;
	}

	public function render()
	{
		ob_start();
?>

__Thank you for your booking!__

<?php 
		return ob_get_clean();
	}
}