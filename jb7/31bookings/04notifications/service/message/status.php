<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_04Notifications_Service_Message_Status
{
	public function __construct(
		JB7_31Bookings_Data_Repo $repoBookings,
		JB7_03Acl_Data_Repo $repoAcl,
		JB7_04Notifications_Data_Repo $repo,

		JB7_31Bookings_04Notifications_Service_Parser $parser,
		JB7_04Notifications_Service_Sender $sender
	)
	{}

	public function listen( $bookingId, array $changes )
	{
		if( ! isset($changes['status']) ){
			return;
		}

		$booking = $this->repoBookings->findById( $bookingId );

	/* TO ADMINS */
		if( ! $this->repo->isDisabled('email-booking-status-manager') ){
			$msg = $this->repo
				->findById( 'email-booking-status-manager' )
				;
			$subject = $this->parser->parse( $msg->subject, $booking );
			$body = $this->parser->parse( $msg->body, $booking );
			$msg = new JB7_04Notifications_Service_Message( $msg->id, $subject, $body );

			$admins = $this->repoAcl->findAdmins();
			foreach( $admins as $user ){
				$this->sender->send( $user, $msg );
			}
		}

	/* TO CUSTOMER */
		if( ! $this->repo->isDisabled('email-booking-status-customer') ){
			if( property_exists($booking->details, 'email') ){
				$customer = new stdClass;
				$customer->email = $booking->details->email;

				$msg = $this->repo
					->findById( 'email-booking-status-customer' )
					;
				$subject = $this->parser->parse( $msg->subject, $booking );
				$body = $this->parser->parse( $msg->body, $booking );
				$msg = new JB7_04Notifications_Service_Message( $msg->id, $subject, $body );

				$this->sender->send( $customer, $msg );
			}
		}
	}
}