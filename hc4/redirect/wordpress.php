<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Redirect_Wordpress
	implements HC4_Redirect_Interface
{
	public function call( $to )
	{
		if( ! headers_sent() ){
			wp_redirect( $to );
		}
		else {
			$html = "<META http-equiv=\"refresh\" content=\"0;URL=$to\">";
			echo $html;
		}
		exit;
	}
}