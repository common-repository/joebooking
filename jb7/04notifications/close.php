<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Close
{
	public function __construct(
		JB7_04Notifications_Service_Sender $sender
	)
	{
		$sender->sendQueued();
	}
}