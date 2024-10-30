<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Redirect_Ajax
	implements HC4_Redirect_Interface
{
	public function call( $to )
	{
		$out = array( 'redirect' => $to );
		$out = json_encode( $out );
		echo $out;
		exit;
	}
}