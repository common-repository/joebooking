<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Data_Model
{
	private $id;
	private $fromTime;			// seconds in day
	private $toTime;				// seconds in day
	private $interval;			// seconds
	private $validFromDate;
	private $validToDate;
	private $appliedOn;			// 'everyday', 'daysofweek'
	private $appliedOnDetails;
	private $priority = 2;
	private $calendar;			// JB7_11Calendars_Data_Model

	private function __construct(
		
	)
	{}

	public function toArray()
	{
		$return = array(
			'id'						=> $this->id,
			'from_time'				=> $this->fromTime,
			'to_time'				=> $this->toTime,
			'interval'				=> $this->interval,
			'applied_on'			=> $this->appliedOn,
			'applied_on_details'	=> $this->appliedOnDetails,
			'valid_from_date'		=> $this->validFromDate,
			'valid_to_date'		=> $this->validToDate,
			'calendar'				=> $this->calendar,
			'priority'				=> $this->priority,
			);
		return $return;
	}

	public static function fromArray( array $array )
	{
		$return = new static;

		if( isset($array['id']) ){
			$return->id = $array['id'];
		}

		if( isset($array['applied_on']) ){
			$return->appliedOn = $array['applied_on'];
		}
		if( isset($array['applied_on_details']) ){
			$return->appliedOnDetails = $array['applied_on_details'];
		}

		if( isset($array['from_time']) ){
			$return->fromTime = $array['from_time'];
		}
		if( isset($array['to_time']) ){
			$return->toTime = $array['to_time'];
		}
		if( isset($array['interval']) ){
			$return->interval = $array['interval'];
		}

		if( isset($array['valid_from_date']) ){
			$return->validFromDate = $array['valid_from_date'];
		}
		if( isset($array['valid_to_date']) ){
			$return->validToDate = $array['valid_to_date'];
		}

		if( isset($array['calendar']) ){
			if( is_array($array['calendar']) ){
				$array['calendar'] = JB7_11Calendars_Data_Model::fromArray( $array['calendar'] );
			}
			$return->calendar = $array['calendar'];
		}
		else {
			$msg = __CLASS__ . ': MISSING: calendar';
			echo $msg;
			// throw new HC4_App_Exception_DataError( $msg );
		}

		if( isset($array['priority']) ){
			$return->priority = $array['priority'];
		}

		return $return;
	}

	public function __get( $name )
	{
		if( property_exists($this, $name) ){
			return $this->{$name};
		}
		else {
			$msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			echo $msg;
			// throw new HC4_App_Exception_DataError( $msg );
		}
	}
}