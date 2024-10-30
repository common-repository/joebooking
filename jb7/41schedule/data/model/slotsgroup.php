<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_41Schedule_Data_Model_SlotsGroup
{
	private $slots = array();
	private $startDateTime = NULL;
	private $endDateTime = NULL;
	private $count = NULL;

	public function add( JB7_41Schedule_Data_Model_Slot $slot )
	{
		$this->slots[] = $slot;
		if( (NULL === $this->startDateTime) OR ($this->startDateTime > $slot->startDateTime) ){
			$this->startDateTime = $slot->startDateTime;
		}
		if( (NULL === $this->endDateTime) OR ($this->endDateTime < $slot->endDateTime) ){
			$this->endDateTime = $slot->endDateTime;
		}
		$this->count++;
		return $this;
	}

	public function __get( $name )
	{
		if( property_exists($this, $name) ){
			return $this->{$name};
		}
		else {
			// throw new HC4_App_Exception_DataError( 'Invalid property: ' . __CLASS__ . ': ' . $name );
			echo 'Invalid property: ' . __CLASS__ . ': ' . $name;
		}
	}
}