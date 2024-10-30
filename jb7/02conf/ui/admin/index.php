<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_02Conf_Ui_Admin_Index
{
	public function __construct(
		HC4_Html_Screen_Interface $screen
		)
	{}

	public function get( $slug )
	{
		// $return = $this->render();
		$return = NULL;
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render()
	{
		ob_start();
?>

<?php 
		return ob_get_clean();
	}
}