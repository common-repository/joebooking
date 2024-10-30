<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Ui_Announce
{
	public function render( $message, $error, $debug )
	{
		if( ! ( $message OR strlen($message) OR $debug OR $error OR strlen($error)) ){
			return;
		}

		$out = array();

		$returnView = array();

		if( $debug ){
			if( is_array($debug) ){
				$debug = join( '<br/>', $debug );
			}
			$debugView = array();
			$debugView[] = '<div class="hc-auto-dismiss hc-p2 hc-my2 hc-border hc-rounded hc-border-orange hc-bg-lightorange hc-black">';
			$debugView[] = $debug;
			$debugView[] = '</div>';
			$debugView = join( "\n", $debugView );

			$returnView[] = $debugView;
		}

		if( $message ){
			if( is_array($message) ){
				$message = join( '<br/>', $message );
			}
			$messageView = array();
			$messageView[] = '<div class="hc-auto-dismiss hc-muted1 hc-p2 hc-my2 hc-bg-lightgreen hc-border hc-rounded hc-border-olive hc-black">';
			$messageView[] = $message;
			$messageView[] = '</div>';
			$messageView = join( "\n", $messageView );

			$returnView[] = $messageView;
		}

		$returnView = join( "\n", $returnView );

		$thisOut = array();
		$thisOut[] = '<div style="position: absolute; left: .5em; top: .5em; right: .5em; z-index: 1000;">';
		$thisOut[] = $returnView;
		$thisOut[] = '</div>';
		$thisOut = join( "\n", $thisOut );

		array_unshift( $out, $thisOut );

		if( $error ){
			if( is_array($error) ){
				$error = join( "", $error );
			}
			$errorView = array();
			// $errorView[] = '<div class="hc-auto-dismiss hc-muted2 hc-p2 hc-my2 hc-bg-lightred hc-rounded hc-border hc-border-maroon hc-black">';
			$errorView[] = '<div class="hc-p2 hc-my2 hc-bg-lightred hc-rounded hc-border hc-border-maroon hc-black">';
			$errorView[] = $error;
			$errorView[] = '</div>';
			$errorView = join( "\n", $errorView );

			array_unshift( $out, $errorView );
		}

		$out = join( "\n", $out );
		return $out;
	}
}