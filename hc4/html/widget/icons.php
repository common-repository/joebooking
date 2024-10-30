<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Widget_Icons
{
	public function __construct(
	)
	{}

	public function renderQuestion( $title = '?' )
	{
		ob_start();
?>
	<div class="hc-inline-block hc-align-center hc-border hc-border-orange hc-bg-lightorange" title="<?php echo $title; ?>" style="width: 1em;">?</div>

<?php 
		return ob_get_clean();
	}

	public function renderAlert( $title = '!' )
	{
		ob_start();
?>
	<div class="hc-inline-block hc-align-center hc-border hc-border-red hc-bg-lightred" title="<?php echo $title; ?>" style="width: 1em;">!</div>

<?php 
		return ob_get_clean();
	}

	public function renderOk( $title = '__OK__' )
	{
		ob_start();
?>
	<div class="hc-inline-block hc-align-center hc-border hc-border-olive hc-bg-lightgreen" title="<?php echo $title; ?>" style="width: 1em;">&check;</div>

<?php 
		return ob_get_clean();
	}
}