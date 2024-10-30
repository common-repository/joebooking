<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Time_Format_
{
	public function formatTime( $dateTimeDb );
	public function formatTimeRange( HC4_Time_Range $range );
	public function formatDate( $dateTimeDb );
	public function formatDateDate( $dateDb );
	public function formatDateWithWeekday( $dateTimeDb );
	public function formatDateRange( $date1, $date2 );
	public function formatWeekday( $weekday );
	public function formatMonthName( $monthNo );
	public function formatDuration( $seconds );
}

class HC4_Time_Format implements HC4_Time_Format_
{
	public $timeFormat;
	public $dateFormat;

	protected $_months = array();
	protected $_localizeMonths = array();
	protected $_weekdays = array();

	function __construct(
		HC4_Time_Interface $t,
		$dateFormat = 'j M Y',
		$timeFormat = 'g:ia'
		)
	{
		$this->dateFormat = $dateFormat;
		$this->timeFormat = $timeFormat;

		$this->_months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
		$this->_weekdays = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );

		$this->_localizeMonths = array();
		foreach( $this->_months as $m ){
			$this->_localizeMonths[ $m ] = '__' . $m . '__';
		}
	}

	public function formatTime( $dateTimeDb )
	{
		if( $dateTimeDb < 24*60*60 ){
			$dateTimeDb = $this->t->setNow()->setStartDay()->modify( '+ ' . $dateTimeDb . ' seconds' )
				->getDateTimeDb()
				;
		}

		$return = $this->t->setDateTimeDb( $dateTimeDb )
			->format( $this->timeFormat )
			;

		return $return;
	}

	public function formatTimeRange( HC4_Time_Range $range )
	{
		$start = $this->formatTime( $range->getStart() );
		$end = $this->formatTime( $range->getEnd() );
		$return = $start . ' - ' . $end;
		return $return;
	}

	public function formatTimeTimeRange( $startTime, $endTime )
	{
		$startDateTime = $this->t
			->setDateDb('20190210')
			->modify( '+' . $startTime . ' seconds' )
			->getDateTimeDb()
			;
		$endDateTime = $this->t
			->setDateDb('20190210')
			->modify( '+' . $endTime . ' seconds' )
			->getDateTimeDb()
			;

		$range2 = new HC4_Time_Range( $startDateTime, $endDateTime );
		return $this->formatTimeRange( $range2 );
	}

	public function formatDate( $dateTimeDb )
	{
		$return = $this->t->setDateTimeDb( $dateTimeDb )
			->format( $this->dateFormat )
			;

	// replace English months to localized ones
		$replaceFrom = array_keys( $this->_localizeMonths );
		$replaceTo = array_values( $this->_localizeMonths );
		$return = str_replace( $replaceFrom, $replaceTo, $return );

		return $return;
	}

	public function formatDateDate( $dateDb )
	{
		$dateTimeDb = $this->t->setDateDb( $dateDb )->getDateTimeDb();
		return $this->formatDate( $dateTimeDb );
	}

	public function formatDateWithWeekday( $dateTimeDb )
	{
		$wd = $this->t->setDateTimeDb( $dateTimeDb )->getWeekday();
		$weekdayView = $this->formatWeekday( $wd );
		$dateView = $this->formatDate( $dateTimeDb );
		$return = $weekdayView . ', ' . $dateView;

		return $return;
	}

	public function formatWeekday( $wd )
	{
		$wd = (string) $wd;
		$return = '__' . $this->_weekdays[$wd] . '__';
		return $return;
	}

	public function formatDuration( $seconds )
	{
		$seconds = (string) $seconds;

		$hours = floor( $seconds / (60 * 60) );
		$remain = $seconds - $hours * (60 * 60);
		$minutes = floor( $remain / 60 );

		$hoursView = $hours;
		$minutesView = sprintf( '%02d', $minutes );

		$return = $hoursView . ':' . $minutesView;
		return $return;
	}

	public function formatMonthName( $monthNo )
	{
		$return = $this->_months[ $monthNo - 1 ];
		$return = '__' . $return . '__';
		return $return;
	}

	public function formatDateRange( $date1, $date2, $with_weekday = FALSE )
	{
		$return = array();
		$skip = array();

		if( $date1 == $date2 ){
			$viewDate1 = $this->formatDate( $date1 );
			if( $with_weekday ){
				$viewDate1 = $this->formatWeekdayShort() . ', ' . $viewDate1;
			}
			$return = $viewDate1;
			return $return;
		}

	// WHOLE MONTH?
		$day2 = $this->t->setDateDb( $date2 )->modify('+1 day')->getDay();
		if( 1 == $day2 ){
			$day1 = $this->t->setDateDb( $date1 )->getDay();
			if( 1 == $day1 ){
		// BINGO!
				$year1 = $this->t->getYear();
				$month1 = $this->t->format('n');
				$return = $this->formatMonthName( $month1 ) . ' ' . $year1;
				return $return;
			}
		}

		$this->t->setDateDb( $date1 );
		$year1 = $this->t->getYear();
		$month1 = $this->t->format('n');

		$this->t->setDateDb( $date2 );
		$year2 = $this->t->getYear();
		$month2 = $this->t->format('n');

		if( $year2 == $year1 )
			$skip['year'] = TRUE;
		if( $month2 == $month1 )
			$skip['month'] = TRUE;

		$skip['date'] = TRUE;

		$pos_y = NULL;
		if( $skip ){
			$dateFormat = $this->dateFormat;
			$dateFormatShort = $dateFormat;

			$tags = array('m', 'n', 'M');
			foreach( $tags as $t ){
				$pos_m_original = strpos($dateFormatShort, $t);
				if( $pos_m_original !== FALSE )
					break;
			}

			if( isset($skip['year']) ){
				$pos_y = strpos($dateFormatShort, 'Y');
				if( $pos_y == 0 ){
					$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_y, 2 );
				}
				else {
					$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_y - 1, 2 );
				}
			}

			if( isset($skip['month']) ){
				$tags = array('m', 'n', 'M');
				foreach( $tags as $t ){
					$pos_m = strpos($dateFormatShort, $t);
					if( $pos_m !== FALSE )
						break;
				}

				// month going first, do not replace
				if( $pos_m_original == 0 ){
					// $dateFormatShort = substr_replace( $dateFormatShort, '', $pos_m, 2 );
				}
				else {
					// month going first, do not replace
					if( $pos_m == 0 ){
						$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_m, 2 );
					}
					else {
						$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_m - 1, 2 );
					}
				}
			}

			if( $pos_y == 0 ){ // skip year in the second part
				$dateFormat1 = $dateFormat;
				$dateFormat2 = $dateFormatShort;
			}
			else {
				$dateFormat1 = $dateFormatShort;
				$dateFormat2 = $dateFormat;
			}

			$this->t->setDateDb( $date1 );

			$viewDate1 = $this->t->format( $dateFormat1 );
			if( $with_weekday ){
				$viewDate1 = $this->formatWeekdayShort() . ', ' . $viewDate1;
			}
			$return[] = $viewDate1;

			$this->t->setDateDb( $date2 );
			$viewDate2 = $this->t->format( $dateFormat2 );
			if( $with_weekday ){
				$viewDate2 = $this->formatWeekdayShort() . ', ' . $viewDate2;
			}
			$return[] = $viewDate2;
		}
		else {
			$viewDate1 = $this->formatDate( $date1 );
			if( $with_weekday ){
				$viewDate1 = $this->formatWeekdayShort() . ', ' . $viewDate1;
			}
			$return[] = $viewDate1;

			$viewDate2 = $this->formatDate( $date2 );
			if( $with_weekday ){
				$viewDate2 = $this->formatWeekdayShort() . ', ' . $viewDate2;
			}
			$return[] = $viewDate2;
		}

		if( $viewDate2 ){
			$return = $viewDate1 . ' - ' . $viewDate2;
		}
		else {
			$return = $viewDate1;
		}
		return $return;
	}
}