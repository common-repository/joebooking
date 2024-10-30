<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Helper
{
	public function __construct(
		HC4_Session_Interface $session
	)
	{}

	public function getValue( $name, $value )
	{
		$post = $this->session->getFlashdata( 'post' );
		if( isset($post[$name]) ){
			$value = $post[$name];
		}

		return $value;
	}

	public function afterRender( $name, $out )
	{
		$errors = $this->session->getFlashdata( 'form_errors' );

		if( isset($errors[$name]) ){
			$error = $errors[$name];
			$errorView = '<div class="hc4-form-input-error">' . $error . '</div>';
			$out .= $errorView;
		}

		return $out;
	}
}