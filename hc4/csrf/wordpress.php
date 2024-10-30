<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Csrf_Wordpress
	implements HC4_Csrf_Interface
{
	protected $actionName = 'post';
	protected $tokenName = 'hc-csrf';
	protected $disabled = FALSE;

	public function checkInput()
	{
		if( $this->disabled ){
			return;
		}

		if( ! isset($_POST[$this->tokenName])){
			// echo "want token name " . $this->tokenName . '<br>';
// _print_r( $_POST );
			echo 'csrf: no token';
			exit;
		}

		$nonce = $_POST[$this->tokenName];
		if( ! wp_verify_nonce( $nonce, $this->actionName ) ){
			echo 'csrf: token mismatch';
			exit;
		}

		// We kill this since we're done and we don't want to polute the _POST array
		unset( $_POST[$this->tokenName] );
		return $this;
	}

	public function render( $output )
	{
		$hidden = wp_nonce_field( $this->actionName, $this->tokenName, TRUE, FALSE );
		$output = str_replace( '</form>', $hidden . '</form>', $output );
		return $output;
	}
}