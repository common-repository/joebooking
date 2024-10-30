<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Time_Implementation
	extends DateTime
	implements HC4_Time_Interface
{
	public $timeFormat = 'g:ia';
	public $dateFormat = 'j M Y';
	public $weekStartsOn = 0;
	public $timezone = '';

	protected $_months = array();
	protected $_weekdays = array();

	function __construct( $weekStartsOn = 0 )
	{
		parent::__construct();
		$this->weekStartsOn = $weekStartsOn;

		$this->_months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$this->_weekdays = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

		if( defined('WPINC') ){
			$tz = get_option('timezone_string');
			if( ! strlen($tz) ){
				$offset = get_option('gmt_offset');
				if( $offset ){
					$tz = 'Etc/GMT';
					if( $offset > 0 ){
						$tz .= '+' . $offset;
					}
					else {
						$tz .= '-' . -$offset;
					}
				}
			}

			$this->setTimezone( $tz );
		}
	}

	public function modify( $modify )
	{
		parent::modify( $modify );
		return $this;
	}

	public function smartModifyDown( $modify )
	{
		$this->modify( $modify );

		list( $qty, $measure ) = explode( ' ', $modify );
		switch( $measure ){
			case 'days':
				$this->setStartDay();
				break;
			case 'weeks':
				$this->setStartWeek();
				break;
			case 'months':
				$this->setStartMonth();
				break;
		}

		return $this;
	}

	public function smartModifyUp( $modify )
	{
		$this->modify( $modify );

		list( $qty, $measure ) = explode( ' ', $modify );
		switch( $measure ){
			case 'days':
				$this->setEndDay();
				break;
			case 'weeks':
				$this->setEndWeek();
				break;
			case 'months':
				$this->setEndMonth();
				break;
		}

		return $this;
	}

	// 201810161215 -> 2018-10-16 12:15:15
	public static function convertToDatabaseDateTime( $from )
	{
		$year = substr( $from, 0, 4 );
		$month = substr( $from, 4, 2 );
		$day = substr( $from, 6, 2 );
		$hour = substr( $from, 8, 2 );
		$minute = substr( $from, 10, 2 );
		$second = '00';

		$return = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $second;
		return $return;
	}

	// 2018-10-16 12:15:15 -> 201810161215
	public static function convertFromDatabaseDateTime( $from )
	{
		$year = substr( $from, 0, 4 );
		$month = substr( $from, 5, 2 );
		$day = substr( $from, 8, 2 );
		$hour = substr( $from, 11, 2 );
		$minute = substr( $from, 14, 2 );

		$return = $year . $month . $day . $hour . $minute;
		return $return;
	}

	/* date2 - date1 */
	public function getDifferenceInDays( $date1, $date2 )
	{
		$ts1 = $this->setDateDb( $date1 )->getTimestamp();
		$ts2 = $this->setDateDb( $date2 )->getTimestamp();

		$tsDiff = $ts2 - $ts1;
		$day = 24 * 60 * 60;

		$dayDiff = floor( $tsDiff / $day );
		return $dayDiff;
	}

	public function setTimezone( $tz )
	{
		if( is_array($tz) )
			$tz = $tz[0];

		if( ! $tz )
			$tz = date_default_timezone_get();

		$this->timezone = $tz;
		$tz = new DateTimeZone($tz);
		parent::setTimezone( $tz );
	}

	public function setTimestamp( $ts )
	{
		if( ! strlen($ts) ){
			$ts = 0;
		}

		if( function_exists('date_timestamp_set') ){
			parent::setTimestamp( $ts );
		}
		else {
			parent::__construct( '@' . $ts );
		}

		return $this;
	}

	public function setNow()
	{
		$this->setTimestamp( time() );
		return $this;
	}

	public function formatToDatepicker()
	{
		$dateFormat = $this->dateFormat;

		$pattern = array(
			//day
			'd',	//day of the month
			'j',	//3 letter name of the day
			'l',	//full name of the day
			'z',	//day of the year

			//month
			'F',	//Month name full
			'M',	//Month name short
			'n',	//numeric month no leading zeros
			'm',	//numeric month leading zeros

			//year
			'Y', //full numeric year
			'y'	//numeric year: 2 digit
			);

		$replace = array(
			'dd','d','DD','o',
			'MM','M','m','mm',
			'yyyy','y'
		);
		foreach($pattern as &$p){
			$p = '/'.$p.'/';
		}
		return preg_replace( $pattern, $replace, $dateFormat );
	}

	public function formatDateDb()
	{
		return $this->getDateDb();
	}

	public function getDateDb()
	{
		$dateFormat = 'Ymd';
		$return = $this->format( $dateFormat );
		return $return;
	}

	public function setDateDb( $date )
	{
		list( $year, $month, $day ) = $this->_splitDate( $date );
		$year = (int) $year;
		$month = (int) $month;
		$day = (int) $day;

		$this->setDate( $year, $month, $day );
		$this->setTime( 0, 0, 0 );
		return $this;
	}

	public function setDateTimeDb( $datetime )
	{
		$date = substr($datetime, 0, 8);
		$this->setDateDb( $date );

		$hours = substr($datetime, 8, 2);
		$minutes = substr($datetime, 10, 2);
		$this->setTime( (int) $hours, (int) $minutes, 0 );

		return $this;
	}

	protected function _splitDate( $string )
	{
		$year = substr( $string, 0, 4 );
		$month = substr( $string, 4, 2 );
		$day = substr( $string, 6, 4 );
		$return = array( $year, $month, $day );
		return $return;
	}

	public function getDateTimeDb()
	{
		$date = $this->getDateDb();
		$time = $this->getTimeDb();
		$return = $date . $time;
		return $return;
	}

	public function formatDateTimeDb2()
	{
		$return = $this->format('Y-m-d H:i:s');
		return $return;
	}

	public function getTimeDb()
	{
		$h = $this->format('G');
		$m = $this->format('i');

		$h = str_pad( $h, 2, 0, STR_PAD_LEFT );
		$m = str_pad( $m, 2, 0, STR_PAD_LEFT );

		$return = $h . $m;
		return $return;
	}

	public function setStartDay()
	{
		$this->setTime( 0, 0, 0 );
		return $this;
	}

	public function setEndDay()
	{
		$this
			->setStartDay()
			->modify('+1 day')
			;
		return $this;
	}

	public function getTimeInDay()
	{
		$timestamp = $this->getTimestamp();

		$this->setStartDay();
		$timestamp2 = $this->getTimestamp();

		$return = $timestamp - $timestamp2;

		$this->setTimestamp( $timestamp );
		return $return;
	}

	public function getWeekStartsOn()
	{
		return $this->weekStartsOn;
	}

	public function setStartWeek()
	{
		$this->setStartDay();
		$weekDay = $this->getWeekday();

		while( $weekDay != $this->weekStartsOn ){
			$this->modify( '-1 day' );
			$weekDay = $this->getWeekday();
		}

		return $this;
	}

	public function setEndWeek()
	{
		$this->setStartDay();
		$this->modify( '+1 day' );
		$weekDay = $this->getWeekday();

		while( $weekDay != $this->weekStartsOn ){
			$this->modify( '+1 day' );
			$weekDay = $this->getWeekday();
		}

		$this
			->modify( '-1 day' )
			->setEndDay()
			;
		return $this;
	}

	public function setStartMonth()
	{
		$year = $this->format('Y');
		$month = $this->format('m');
		$day = '01';

		$date = $year . $month . $day;
		$this
			->setDateDb( $date )
			->setTime( 0, 0, 0 )
			;

		return $this;
	}

	public function setEndMonth()
	{
		$this->modify('+1 month');

		$year = $this->format('Y');
		$month = $this->format('m');
		$day = '01';

		$date = $year . $month . $day;
		$this
			->setDateDb( $date )
			->modify('-1 day')
			->setEndDay()
			;

		return $this;
	}

	public function setStartYear()
	{
		$year = $this->format('Y');
		$month = '01';
		$day = '01';

		$date = $year . $month . $day;
		$this
			->setDateDb( $date )
			->setTime( 0, 0, 0 )
			;

		return $this;
	}

	public function setEndYear()
	{
		$this
			->setStartYear()
			->modify('+1 year')
			->modify('-1 day')
			;

		return $this;
	}

	public function getYear()
	{
		$return = $this->format('Y');
		return $return;
	}

	public function getDay()
	{
		$return = $this->format('j');
		return $return;
	}

	public function getWeekday()
	{
		$return = $this->format('w');
		return $return;
	}

	public function formatDateRange( $date1, $date2, $withWeekday = FALSE, $skipYear = FALSE )
	{
		list( $start_date_view, $end_date_view ) = $this->_formatDateRange( $date1, $date2, $withWeekday, $skipYear );

		if( $end_date_view ){
			$return = $start_date_view . ' - ' . $end_date_view;
		}
		else {
			$return = $start_date_view;
		}
		return $return;
	}

	protected function _formatDateRange( $date1, $date2, $with_weekday = FALSE, $skipYear = FALSE )
	{
		$return = array();
		$skip = array();

		if( $date1 == $date2 ){
			$this->setDateDb( $date1 );
			$view_date1 = $this->formatDate();
			if( $with_weekday ){
				$view_date1 = $this->getWeekdayName() . ', ' . $view_date1;
			}
			$return[] = $view_date1;
			$return[] = NULL;
			return $return;
		}

		$this->setDateDb( $date1 );
		$year1 = $this->getYear();
		$month1 = $this->format('n');

		$this->setDateDb( $date2 );
		$year2 = $this->getYear();
		$month2 = $this->format('n');

		if( $skipYear ){
			$skip['year'] = TRUE;
		}

		if( $year2 == $year1 )
			$skip['year'] = TRUE;
		if( $month2 == $month1 )
			$skip['month'] = TRUE;

		if( $skip ){
			$date_format = $this->dateFormat;
			$date_format_short = $date_format;

			$tags = array('m', 'n', 'M');
			foreach( $tags as $t ){
				$pos_m_original = strpos($date_format_short, $t);
				if( $pos_m_original !== FALSE )
					break;
			}

			if( isset($skip['year']) ){
				$pos_y = strpos($date_format_short, 'Y');
				if( $pos_y == 0 ){
					$date_format_short = substr_replace( $date_format_short, '', $pos_y, 2 );
				}
				else {
					$date_format_short = substr_replace( $date_format_short, '', $pos_y - 1, 2 );
				}

				$date_format_wo_year = $date_format_short;
			}

			if( isset($skip['month']) ){
				$tags = array('m', 'n', 'M');
				foreach( $tags as $t ){
					$pos_m = strpos($date_format_short, $t);
					if( $pos_m !== FALSE )
						break;
				}

				// month going first, do not replace
				if( $pos_m_original == 0 ){
					// $date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
				}
				else {
					// month going first, do not replace
					if( $pos_m == 0 ){
						$date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
					}
					else {
						$date_format_short = substr_replace( $date_format_short, '', $pos_m - 1, 2 );
					}
				}
			}

			if( $pos_y == 0 ){ // skip year in the second part
				$date_format1 = $date_format;
				$date_format2 = $date_format_short;
			}
			else {
				$date_format1 = $date_format_short;
				$date_format2 = $date_format;
				if( $skipYear ){
					$date_format2 = $date_format_wo_year;
				}
			}

			$this->setDateDb( $date1 );

			$view_date1 = $this->formatDate( $date_format1 );
			if( $with_weekday ){
				$view_date1 = $this->getWeekdayName() . ', ' . $view_date1;
			}
			$return[] = $view_date1;

			$this->setDateDb( $date2 );
			$view_date2 = $this->formatDate( $date_format2 );

			if( $with_weekday ){
				$view_date2 = $this->getWeekdayName() . ', ' . $view_date2;
			}
			$return[] = $view_date2;
		}
		else {
			$this->setDateDb( $date1 );
			$view_date1 = $this->formatDate();
			if( $with_weekday ){
				$view_date1 = $this->getWeekdayName() . ', ' . $view_date1;
			}
			$return[] = $view_date1;

			$this->setDateDb( $date2 );
			$view_date2 = $this->formatDate();
			if( $with_weekday ){
				$view_date2 = $this->getWeekdayName() . ', ' . $view_date2;
			}
			$return[] = $view_date2;
		}

		return $return;
	}

	public function getMonthMatrix( $skipWeekdays = array(), $overlap = FALSE )
	{
		// $overlap = TRUE; // if to show dates of prev/next month
		// $overlap = FALSE; // if to show dates of prev/next month

		$matrix = array();
		$currentMonthDay = 0;

		$currentDate = $this->formatDateDb();
		$thisMonth = substr( $currentDate, 4, 2 );

		$this->setStartMonth();
		if( $overlap ){
			$this->setStartWeek();
		}
		$startDate = $this->formatDateDb();

// echo "END DATE = $endDate<br>";

		$this
			->setDateDb( $currentDate )
			->setEndMonth()
			;
		if( $overlap ){
			$this->setEndWeek();
		}
		$this->modify('-1 second');

		$endDate = $this->formatDateDb();
// echo "START/END DATE = $startDate/$endDate<br>";

		$rexDate = $startDate;
		if( $overlap ){
			$this->setDateDb( $startDate );
			$this->setStartWeek();
			$rexDate = $this->formatDateDb();
		}

		$this->setDateDb( $startDate );
		$this->setStartWeek();
		$rexDate = $this->formatDateDb();

// echo "START DATE = $startDate, END DATE = $endDate, REX DATE = $rexDate<br>";

		$this->setDateDb( $rexDate );
		while( $rexDate <= $endDate ){
			$week = array();
			$weekSet = FALSE;
			$thisWeekStart = $rexDate;

			for( $weekDay = 0; $weekDay <= 6; $weekDay++ ){
				$thisWeekday = $this->getWeekday();
				$setDate = $rexDate;

				if( ! $overlap ){
					if( 
						( $rexDate > $endDate ) OR
						( $rexDate < $startDate )
						){
						$setDate = NULL;
						}
				}

				// $week[ $thisWeekday ] = $setDate;

				if( (! $skipWeekdays) OR (! in_array($thisWeekday, $skipWeekdays)) ){
					if( NULL !== $setDate ){
						$rexMonth = substr( $setDate, 4, 2 );
// echo "$rexMonth VS $thisMonth<br>";

						if( ! $overlap ){
							if( $rexMonth != $thisMonth ){
								$setDate = NULL;
							}
						}
					}

					$wki = $this->getWeekday();
					$week[ $wki ] = $setDate;
					if( NULL !== $setDate ){
						$weekSet = TRUE;
					}
				}

				$this->modify('+1 day');
				$rexDate = $this->formatDateDb();

// echo "R = $rexDate<br>";
				// if( $exact && ($rexDate >= $endDate) ){
					// break;
				// }
			}

			if( $weekSet )
				$matrix[$thisWeekStart] = $week;
		}

		return $matrix;
	}

	public function getParts()
	{
		$full = $this->formatDateTimeDb();

		$year = substr( $full, 0, 4 );
		$month = substr( $full, 4, 2 );
		$day = substr( $full, 6, 2 );
		$hour = substr( $full, 8, 2 );
		$min = substr( $full, 10, 2 );

		$return = array( $year, $month, $day, $hour, $min );
		return $return;
	}

	public function getSortedWeekdays()
	{
		$return = array( 0, 1, 2, 3, 4, 5, 6 );
		$return = $this->sortWeekdays( $return );
		return $return;
	}

	public function getWeekdays()
	{
		$return = array();

		$wkds = array( 0, 1, 2, 3, 4, 5, 6 );
		$wkds = $this->sortWeekdays( $wkds );

		reset( $wkds );
		foreach( $wkds as $wkd ){
			$return[ $wkd ] = $this->_weekdays[$wkd];
		}
		return $return;
	}

	public function sortWeekdays( $wds )
	{
		$return = array();
		$later = array();

		sort( $wds );
		reset( $wds );
		foreach( $wds as $wd ){
			if( $wd < $this->weekStartsOn )
				$later[] = $wd;
			else
				$return[] = $wd;
		}
		$return = array_merge( $return, $later );
		return $return;
	}

	public function getDuration( $otherDateTimeDb )
	{
		$timestamp1 = $this->getTimestamp();
		$timestamp2 = $this->setDateTimeDb( $otherDateTimeDb )->getTimestamp();

		$return = abs( $timestamp2 - $timestamp1 );
		return $return;
	}

	public function getMonth()
	{
		$month = $this->format('n');
		return $month;
	}

	public function getWeekNo()
	{
		$return = $this->format('W'); // but it works out of the box for week starts on monday
		$weekday = $this->getWeekday();
		if( ! $weekday ){ // sunday
			if( ! $this->weekStartsOn ){
				$return = $return + 1;
			}
		}
		return $return;
	}
}