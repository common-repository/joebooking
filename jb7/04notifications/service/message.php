<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Service_Message
{
	private $id;
	private $subject;
	private $body;

	public function __construct( $id, $subject, $body )
	{
		$this->id = $id;
		$this->subject = $subject;
		$this->body = $body;
	}

	public function __get( $name )
	{
		if( property_exists($this, $name) ){
			return $this->{$name};
		}
		else {
			$msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			echo $msg . '<br>';
			// throw new HC4_App_Exception_DataError( 'Invalid property: ' . __CLASS__ . ': ' . $name );
		}
	}
}