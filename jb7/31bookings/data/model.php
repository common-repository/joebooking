<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Data_Model
{
	private $_set = array();

	private $id;
	private $startDateTime;
	private $endDateTime;
	private $status = 'pending';	// 'pending', 'approve', 'complete', 'cancel', 'noshow'

	private $calendar;	// JB7_11Calendars_Data_Model
	private $details;		// JB7_31Bookings_Data_Model_Details

	private $refno;
	private $token;
	private $tokenExpire;

	private static $statuses = array(
		'pending'	=> array( '__Pending__',	'lightorange',	TRUE ),
		'approve'	=> array( '__Approved__',	'green',		TRUE ),
		'complete'	=> array( '__Completed__',	'olive',		FALSE ),
		'cancel'		=> array( '__Cancelled__',	'gray',		FALSE ),
		'noshow'		=> array( '__No Show__',	'lightred',		FALSE ),
		);

	private static $paymentStatuses = array(
		'notpaid'	=> array( '__Not Paid__',	'orange' ),
		'deposit'	=> array( '__Deposit__',	'aqua' ),
		'paid'		=> array( '__Paid__',		'olive' ),
		);

	public static function getStatuses()
	{
		return static::$statuses;
	}

	public static function getPaymentStatuses()
	{
		return static::$paymentStatuses;
	}

	public function __clone()
	{
		$this->_set = array();
	}

	public function __set( $name, $value )
	{
		if( ! property_exists($this, $name) ){
			$msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			echo $msg;
			return;
		}

		if( array_key_exists($name, $this->_set) ){
			$msg = 'Property already set: ' . __CLASS__ . ': ' . $name;
			echo $msg;
			return;
		}

		$this->{$name} = $value;
		$this->_set[$name] = 1;
	}

	public function __get( $name )
	{
		if( 'lock' == $name ){
			$return = TRUE;
			if( isset(static::$statuses[$this->status]) ){
				$return = static::$statuses[$this->status][2];
			}
			return $return;
		}

		if( ! property_exists($this, $name) ){
			$msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			echo $msg;
		}

		return $this->{$name};
	}
}