<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Time_Interface
{
	public function smartModifyDown( $modify );
	public function smartModifyUp( $modify );

	public static function convertToDatabaseDateTime( $from );
	public static function convertFromDatabaseDateTime( $from );

	public function setTimezone( $tz );
	public function setNow();
	public function formatToDatepicker();

	public function getSortedWeekdays();

	public function formatDateDb();
	public function setDateDb( $date );
	public function setDateTimeDb( $datetime );

	public function getDateTimeDb();
	public function getDateDb();
	public function getTimeDb();

	public function formatDateTimeDb2();

	public function setStartDay();
	public function setEndDay();

	public function setStartWeek();
	public function setEndWeek();

	public function setStartMonth();
	public function setEndMonth();

	public function setStartYear();
	public function setEndYear();

	public function getWeekStartsOn();
	public function getYear();
	public function getDay();
	public function getWeekday();
	public function formatDateRange( $date1, $date2, $with_weekday = FALSE );
	public function getMonthMatrix( $skipWeekdays = array() );
	public function getParts();
	public function getWeekdays();
	public function sortWeekdays( $wds );
	public function getTimeInDay();

	public function getDuration( $otherDateTimeDb );
	public function getWeekNo();

	public function getDifferenceInDays( $date1, $date2 );
}