<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Time_TimeRange
{
	/** @var string */
	protected $startInDay;

	/** @var string */
	protected $endInDay;

	public function __construct( $startInDay, $endInDay )
	{
		$minStart = 0;
		$maxStart = 24 * 60 * 60;
		$minEnd = $startInDay;
		$maxEnd = $startInDay + 24 * 60 * 60;

		if( ($startInDay < $minStart) OR ($startInDay > $maxStart) ){
			echo "HC4_Time_Range accepts $minStart - $maxStart range, '$startInDay' given<br>";
		}
		if( ($endInDay < $minEnd) OR ($endInDay > $maxEnd) ){
			echo "HC4_Time_Range accepts $minEnd - $maxEnd range, '$endInDay' given<br>";
		}

		$this->startInDay = $startInDay;
		$this->endInDay = $endInDay;
	}

	public function getStart()
	{
		return $this->startInDay;
	}

	public function getEnd()
	{
		return $this->endInDay;
	}

	public function toString()
	{
		$return = $this->startInDay . '-' . $this->endInDay;
		return $return;
	}

	public static function fromString( $string )
	{
		list( $start, $end ) = explode( '-', $string );
		return new static( $start, $end );
	}
}