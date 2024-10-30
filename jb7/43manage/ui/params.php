<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_43Manage_Ui_Params_
{
// SET
	public function range( $set );
	public function grouping( $set );
	public function presentation( $set );
	public function date( $date );
	public function calendarId( $calendarId );

// GET
	public function isGroupingCalendar();
	public function isGroupingNone();

	public function getDates();
	public function getNextDate();
	public function getPrevDate();

	public function getTitle();
	public function getTitleDates();
	public function getCalendarId();

// STRING
	public function makeString();
}

class JB7_43Manage_Ui_Params
	implements JB7_43Manage_Ui_Params_
{
	protected $grouping = self::GROUPING_NONE;
	protected $range = self::RANGE_WEEK;
	protected $presentation = self::PRESENTATION_CALENDAR;
	protected $calendarId = NULL;

	protected $t;
	protected $tf;

	protected $date = '19700101';

	const GROUPING_NONE = 'n';
	const GROUPING_CALENDAR = 'c';

	const RANGE_DAY = 'd';
	const RANGE_WEEK = 'w';
	const RANGE_NEXT7 = 'n';
	const RANGE_MONTH = 'm';

	const PRESENTATION_CALENDAR = 'c';
	const PRESENTATION_LIST = 'l';

	public function __construct(
		JB7_43Manage_Data_Repo $repo,
		JB7_21Availability_Data_Repo $repoAvailability,
		JB7_31Bookings_Data_Repo $repoBookings,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		$paramString = NULL
	)
	{
		$this->repo = $repo;
		$this->repoAvailability = $repoAvailability;
		$this->repoBookings = $repoBookings;

		$this->t = $t;
		$this->tf = $tf;
		if( strlen($paramString) ){
			$this->_parseParamString( $paramString );
		}
	}

	public function make( $paramString = NULL )
	{
		return new static(
			$this->repo,
			$this->repoAvailability,
			$this->repoBookings,
			$this->t, $this->tf, $paramString
			);
	}

	public function range( $set )
	{
		$allowed = array( self::RANGE_NEXT7, self::RANGE_WEEK, self::RANGE_MONTH, self::RANGE_DAY );
		if( ! in_array($set, $allowed) ){
			$default = current( $allowed );
			echo "RANGE '$set' IS NOT ALLOWED, REVERTING TO '" . $default . "'";
			$set = $default;
		}

		$this->range = $set;
		return $this;
	}

	public function grouping( $set )
	{
		$allowed = array( self::GROUPING_NONE, self::GROUPING_CALENDAR );
		if( ! in_array($set, $allowed) ){
			$default = current( $allowed );
			echo "GROUPING '$set' IS NOT ALLOWED, REVERTING TO '" . $default . "'";
			$set = $default;
		}

		$this->grouping = $set;
		return $this;
	}

	public function calendarId( $set )
	{
		$this->calendarId = $set;
		return $this;
	}

	public function presentation( $set )
	{
		$allowed = array( self::PRESENTATION_CALENDAR, self::PRESENTATION_LIST );
		if( ! in_array($set, $allowed) ){
			$default = current( $allowed );
			echo "PRESENTATION '$set' IS NOT ALLOWED, REVERTING TO '" . $default . "'";
			$set = $default;
		}

		$this->presentation = $set;
		return $this;
	}

	public function date( $date )
	{
		$this->date = $date;
		return $this;
	}

	public function makeString()
	{
		$return = array();

		$return1 = array();
		$return1[] = $this->grouping;
		$return1[] = $this->range;
		$return1[] = $this->presentation;
		$return1 = join( '', $return1 );

		$return[] = $return1;
		$return[] = $this->date;

		// reset( $this->terms );
		// foreach( $this->terms as $term ){
			// $return[] = $term->id;
		// }

		$return = join( '-', $return );
		return $return;
	}

	public function getCalendarId()
	{
		return $this->calendarId;
	}

	public function getTitle()
	{
  		$return = array();

		// if( $terms = $this->getTerms() ){
			// foreach( $terms as $term ){
				// $return[] = $term->title;
			// }
		// }
		$return[] = $this->getTitleDates();

		$return = join( ' &middot; ', $return );

		return $return;
	}

	public function getTitleDates()
	{
		$return = NULL;
		$dates = $this->getDates();
		if( $dates ){
			$startDate = $dates[0];
			$endDate = $dates[ count($dates) - 1 ];
			$return = $this->tf->formatDateRange( $startDate, $endDate );
		}
		return $return;
	}

	public function getDates()
	{
		$return = array();
		$date = $this->date;

		switch( $this->range ){
			case self::RANGE_DAY:
				$return = array( $date );
				break;

			case self::RANGE_WEEK:
				$rexDate = $this->t->setDateDb( $date )->setStartWeek()->getDateDb();
				$endDate = $this->t->modify('+6 days')->getDateDb();

				$this->t->setDateDb( $rexDate );
				while( $rexDate <= $endDate ){
					$return[] = $rexDate;
					$rexDate = $this->t->modify('+1 day')->getDateDb();
				}
				break;

			case self::RANGE_NEXT7:
				$rexDate = $date;

				while( (count($return) < 7) && $rexDate ){
					$rexDateTime = $this->t->setDateDb( $rexDate )->getDateTimeDb();
					$rexDate = NULL;

					$nextReturn = $this->_findNextEventDateTime( $rexDateTime );
					if( $nextReturn ){
						$rexDate = $this->t->setDateTimeDb( $nextReturn )->getDateDb();
						$return[] = $rexDate;
						$rexDate = $this->t->modify('+1 day')->getDateDb();
					}
				}
				break;

			case self::RANGE_MONTH:
				$startDate = $this->t->setDateDb( $date )->setStartMonth()->getDateDb();
				$endDate = $this->t->modify('+1 month')->modify('-1 day')->getDateDb();

				$this->t->setDateDb( $rexDate );
				while( $rexDate <= $endDate ){
					$return[] = $rexDate;
					$rexDate = $this->t->modify('+1 day')->getDateDb();
				}
				break;
		}

		return $return;
	}

	public function getNextDate()
	{
		$return = NULL;

		$dates = $this->getDates();
		if( ! $dates ){
			return $return;
		}

		$startDate = $dates[0];
		$endDate = $dates[ count($dates) - 1 ];

		switch( $this->range ){
			case self::RANGE_NEXT7:
				$date = $this->date;
				$tomorrow = $this->t->setDateDb( $endDate )->modify('+1 day')->getDateDb();
				$this->date( $tomorrow );
				$nextDates = $this->getDates();
				if( $dates ){
					$return = array_shift( $nextDates );
				}
				$this->date( $date );
				break;

			default:
				$return = $this->t->setDateDb( $endDate )->modify('+1 day')->getDateDb();
				break;
		}

		return $return;
	}

	public function getPrevDate()
	{
		$return = NULL;

		$dates = $this->getDates();
		if( ! $dates ){
			return $return;
		}
		$startDate = array_shift( $dates );

		switch( $this->range ){
			case self::RANGE_WEEK:
				$return = $this->t->setDateDb( $startDate )->modify('-1 week')->getDateDb();
				break;

			case self::RANGE_MONTH:
				$return = $this->t->setDateDb( $startDate )->modify('-1 month')->getDateDb();
				break;

			case self::RANGE_NEXT7:
				// $days = 7;

				// $date = $this->date;

				// $rexDateTime = $this->t->setDateDb( $startDate )->modify('-1 day')->getDateTimeDb();
				// $rexDateTime = $this->repo->findPrevSomething( $rexDateTime );

				// $found = 0;
				// while( ($found < $days) && $rexDateTime ){
					// $rexDate = $this->t->setDateTimeDb( $rexDateTime )->getDateDb();
					// $return = $rexDate;

					// $rexDateTime = $this->t->setDateDb( $rexDate )->modify('-1 day')->getDateTimeDb();
					// $rexDateTime = $this->repo->findPrevSomething( $rexDateTime );
					// $found++;
				// }
				break;
		}

		return $return;
	}

	public function getPrevDates()
	{
		$dates = $this->getDates();
		$startDate = $dates[0];
		$endDate = $dates[ count($dates) - 1 ];

		$endDate = $this->t->setDateDb( $startDate )->modify('-1 day')->getDateDb();
		switch( $this->range ){
			case self::RANGE_WEEK:
				$startDate = $this->t->modify('-6 days')->getDateDb();
				break;

			case self::RANGE_NEXT7:
				$startDate = $this->t->modify('-6 days')->getDateDb();
				break;

			case self::RANGE_MONTH:
				$startDate = $this->t->setDateDb( $startDate )->modify('-1 month')->getDateDb();
				break;
		}

		$return = array( $startDate, $endDate );
		return $return;
	}

	public function isGroupingCalendar()
	{
		return ( $this->grouping == self::GROUPING_CALENDAR );
	}

	public function isGroupingNone()
	{
		return ( $this->grouping == self::GROUPING_NONE );
	}

	protected function _parseParamString( $paramString )
	{
		$paramString = trim( $paramString );
		$paramsArray = $paramString ? explode( '-', $paramString ) : array();

		$paramsString1 = NULL;
		$date = NULL;

		if( $paramsArray ){
			$paramsString1 = array_shift( $paramsArray );

			$grouping = ( strlen($paramsString1) > 2 ) ? substr($paramsString1, 0, 1) : self::GROUPING_NONE;
			$range = ( strlen($paramsString1) > 2 ) ? substr($paramsString1, 1, 1) : self::RANGE_NEXT7;
			$presentation = ( strlen($paramsString1) > 2 ) ? substr($paramsString1, 2, 1) : self::PRESENTATION_CALENDAR;

			$this->grouping( $grouping );
			$this->range( $range );
			$this->presentation( $presentation );
		}

		if( $paramsArray ){
			$date = array_shift( $paramsArray );
			$this->date( $date );
		}

		// $termIds = array();
		// while( $paramsArray ){
			// $termIds[] = array_shift( $paramsArray );
		// }
		// if( $termIds ){
			// $this->termIds( $termIds );
		// }
	}

	public function getPresentationOptions()
	{
		$return = array();
		$return[ self::PRESENTATION_CALENDAR ] = '__Calendar__';
		// $return[ self::PRESENTATION_LIST ] = '__List__';
		return $return;
	}

	public function getGroupingOptions()
	{
		$return = array();
		$return[ self::GROUPING_CALENDAR ] = '__Calendar__';
		$return[ self::GROUPING_NONE ] = '__None__';
		return $return;
	}

	public function getRangeOptions()
	{
		$return = array();
		$return[ self::RANGE_WEEK ] = '__Week__';
		$return[ self::RANGE_NEXT7 ] = '__Next 7 Days__';
		// $return[ self::RANGE_MONTH ] = '__Month__';
		// $return[ self::RANGE_DAY ] = '__Day__';
		return $return;
	}

	protected function _findNextEventDateTime( $startDateTime )
	{
		static $cache = array();
		if( array_key_exists($startDateTime, $cache) ){
			return $cache[ $startDateTime ];
		}

		$return = NULL;
		$lookForBookings = TRUE;

		$calendars = $this->repo->getCalendars();
		if( $calendarId = $this->getCalendarId() ){
			if( isset($calendars[$calendarId]) ){
				$calendars = array( $calendarId => $calendars[$calendarId] );
			}
			else {
				$calendars = array();
			}
		}

		if( ! $calendars ){
			return $return;
		}

		$startDate = $this->t->setDateTimeDb( $startDateTime )->getDateDb();

		$nextAvailabilityDateTime = NULL;
		reset( $calendars );
		foreach( $calendars as $calendar ){
			$thisNextAvailability = $this->repoAvailability->findNext( $startDateTime, $calendar );
			if( $thisNextAvailability ){
				foreach( $thisNextAvailability as $date => $availability ){
					$thisNextAvailabilityDateTime = $this->t->setDateDb( $date )
						->modify( '+' . $availability->fromTime . ' seconds' )
						->getDateTimeDb()
						;
					break;
				}

				if( (! $nextAvailabilityDateTime) OR ($thisNextAvailabilityDateTime < $nextAvailabilityDateTime) ){
					$nextAvailabilityDateTime = $thisNextAvailabilityDateTime;
				}
			}
		}

		if( $nextAvailabilityDateTime ){
			$return = $nextAvailabilityDateTime;
			$date = $this->t->setDateTimeDb( $nextAvailabilityDateTime )
				->formatDateDb()
				;
			if( $date == $startDate ){
				$lookForBookings = FALSE;
			}
		}

		if( $lookForBookings ){
			$nextBooking = $this->repoBookings->findNext( $startDateTime, $calendars );
			if( $nextBooking ){
				if( (! $return) OR ($nextBooking->startDateTime < $return) ){
					$return = $nextBooking->startDateTime;
				}
			}
		}

		$cache[ $startDateTime ] = $return;
 		return $return;
	}
}