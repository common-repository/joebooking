<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_43Manage_Ui_New_Params_
{
	public function make( $paramString = NULL );
	
// SET
	public function start( $set );
	public function end( $set );
	public function calendarId( $set );

// GET
	public function getStart();
	public function getEnd();
	public function getCalendar();

// STRING
	public function makeString();
}

class JB7_43Manage_Ui_New_Params
	implements JB7_43Manage_Ui_New_Params_
{
	protected $start = NULL;
	protected $end = NULL;
	protected $calendar = NULL;

	public function __construct(
		JB7_11Calendars_Data_Repo $repoCalendars,
		$paramString = NULL
	)
	{
		$this->repoCalendars = $repoCalendars;
		if( strlen($paramString) ){
			$this->_parseParamString( $paramString );
		}
	}

	public function make( $paramString = NULL )
	{
		return new static( $this->repoCalendars, $paramString );
	}

	public function start( $set )
	{
		$this->start = $set;
		return $this;
	}

	public function end( $set )
	{
		$this->end = $set;
		return $this;
	}

	public function calendarId( $set )
	{
		$calendar = $this->repoCalendars->findById( $set );
		$this->calendar = $calendar;
		return $this;
	}

	public function makeString()
	{
		$return = array();

		$return[] = $this->start;
		$return[] = $this->end;

		if( $this->calendar ){
			$return[] = $this->calendar->id;
		}

		$return = join( '-', $return );
		return $return;
	}

	public function getStart()
	{
		return $this->start;
	}

	public function getEnd()
	{
		return $this->end;
	}

	public function getCalendar()
	{
		return $this->calendar;
	}

	protected function _parseParamString( $paramString )
	{
		$paramString = trim( $paramString );
		$paramsArray = $paramString ? explode( '-', $paramString ) : array();

		$start = array_shift( $paramsArray );
		$end = array_shift( $paramsArray );

		$this
			->start( $start )
			->end( $end )
			;

		if( $paramsArray ){
			$calendarId = array_shift( $paramsArray );
			$this
				->calendarId( $calendarId )
				;
		}
	}
}