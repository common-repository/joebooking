<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Data_Audit
{
	public function __construct(
		JB7_04Audit_Data_Repo $auditRepo
	)
	{}

	public function log( $id, array $changes )
	{
		if( isset($changes['id']) && (NULL === $changes['id'][1]) ){
			$this->auditRepo->delete( 'bookings', $id );
		}
		else {
			$this->auditRepo->create( 'bookings', $id, $changes );
		}
		return $this;
	}
}