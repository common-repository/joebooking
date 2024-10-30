<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_App_Exception_FormErrors extends Exception
{
	protected $errors = array();

	public function __construct( array $errors )
	{
		$this->errors = $errors;
	}

	public function getErrors()
	{
		return $this->errors;
	}
}