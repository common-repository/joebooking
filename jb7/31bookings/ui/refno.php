<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Ui_Refno
{
	public function __construct(
	)
	{}

	public function render( $refno )
	{
		$return = array( );

		$return[] = strtoupper( substr($refno, 0, 2) );
		$return[] = substr($refno, 2, 4);

		$return = join( '-', $return );

		return $return;
	}
}