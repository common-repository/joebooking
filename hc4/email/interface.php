<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Email_Interface
{
	public function send( $to, $subj, $msg );
}