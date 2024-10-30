<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_03Acl_Ui_NotAllowed
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
<p>
__You are not allowed to view this page.__
</p>

<?php 
		return ob_get_clean();
	}
}