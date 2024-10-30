<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Service_Sender_Email
{
	public function __construct(
		HC4_Email_Interface $email
	)
	{}

	public function send(
		$user,
		JB7_04Notifications_Service_Message $message
	)
	{
		$to = $user->email;
		if( ! $to ){
			return;
		}

		$this->email
			->send( $to, $message->subject, $message->body )
			;
	}
}