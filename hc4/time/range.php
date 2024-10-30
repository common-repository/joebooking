<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Time_Range
{
	/** @var string */
	protected $start;

	/** @var string */
	protected $end;

	public function __construct( $start, $end )
	{
		if( strlen($start) != 12 ){
			echo "HC4_Time_Range accepts 12 digits datetime, '$start' given<br>";
		}
		if( strlen($end) != 12 ){
			echo "HC4_Time_Range accepts 12 digits datetime, '$end' given<br>";
		}

		$this->start = $start;
		$this->end = $end;
	}

	public function getStart()
	{
		return $this->start;
	}

	public function getEnd()
	{
		return $this->end;
	}
}