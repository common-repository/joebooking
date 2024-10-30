<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
/*
blocks are availabilities: fromTime, toTime, numberOfSeats
ticks are selectable timestamps
*/

class JB7_41Schedule_Data_Model_Timeline
{
	protected $blocks = array();
	protected $ticks = array();
	protected $totalSeats = 1;

	protected $_timeline = NULL;
	protected $_ticks = FALSE;

	public function __construct( $totalSeats = 1 )
	{
		$this->totalSeats = $totalSeats;
		$this->blocks = array();
		$this->_timeline = NULL;
	}

	public function addBlock( $start, $end, $seats )
	{
		$this->blocks[] = array( $start, $end, $seats );
		return $this;
	}

	public function addTick( $tickTimeStart, $tickTimeEnd )
	{
		$this->ticks[ $tickTimeStart ] = $tickTimeEnd;
		return $this;
	}

	public function getAvailable()
	{
		$return = array();

		$timeline = $this->_getTimeline();

		$currentBlock = array();
		foreach( $timeline as $dt => $seats ){
			list( $seatsAvailable, $seatsTaken ) = $seats;

			if( $seatsAvailable > $seatsTaken ){
				if( $currentBlock ){
					$currentBlock[1] = $dt;
				}
				else {
					$currentBlock = array( $dt, $dt );
				}
			}
			else {
				if( $currentBlock ){
					$currentBlock[1] = $dt;
					$return[] = $currentBlock;
					$currentBlock = array();
				}
			}
		}

		if( $currentBlock ){
			$currentBlock[1] = $dt;
			$return[] = $currentBlock;
		}

		return $return;
	}

	public function getTaken()
	{
		$return = array();

		$timeline = $this->_getTimeline();

		$currentBlock = array();
		foreach( $timeline as $dt => $seats ){
			list( $seatsAvailable, $seatsTaken ) = $seats;

			if( $seatsTaken > 0 ){
				if( $currentBlock ){
					$currentBlock[1] = $dt;
				}
				else {
					$currentBlock = array( $dt, $dt );
				}
			}
			else {
				if( $currentBlock ){
					$currentBlock[1] = $dt;
					$return[] = $currentBlock;
					$currentBlock = array();
				}
			}
		}

		if( $currentBlock ){
			$currentBlock[1] = $dt;
			$return[] = $currentBlock;
		}

		return $return;
	}

	public function getOverbooked()
	{
		$return = array();

		$timeline = $this->_getTimeline();

		$currentBlock = array();
		foreach( $timeline as $dt => $seats ){
			list( $seatsAvailable, $seatsTaken ) = $seats;

			if( $seatsTaken > $this->totalSeats ){
				if( $currentBlock ){
					$currentBlock[1] = $dt;
				}
				else {
					$currentBlock = array( $dt, $dt );
				}
			}
			else {
				if( $currentBlock ){
					$currentBlock[1] = $dt;
					$return[] = $currentBlock;
					$currentBlock = array();
				}
			}
		}

		if( $currentBlock ){
			$currentBlock[1] = $dt;
			$return[] = $currentBlock;
		}

		return $return;
	}

	public function getAvailableTicks()
	{
		$return = array();

		$availableBlocks = $this->getAvailable();
		$ticks = $this->_getTicks();

		foreach( $ticks as $tickStart => $tickEnd ){
			// CHECK IF THE TICK WITHIN AN AVAILABLE BLOCK
			reset( $availableBlocks );
			foreach( $availableBlocks as $b ){
				if( $b[0] >= $tickEnd ){
					break;
				}
				if( $b[1] <= $tickStart ){
					continue;
				}
				
				if( ($b[0] <= $tickStart) && ($b[1] >= $tickEnd) ){
					// echo "TICK $tickStart - $tickEnd IS OK FOR $b[0] - $b[1]<br>";
					$return[] = $tickStart;
				}
				// $return[] = array( $tick, $b[1] );
			}
		}

		return $return;
	}

	protected function _getTicks()
	{
		if( ! $this->_ticks ){
			ksort( $this->ticks );
			$this->_ticks = TRUE;
		}
		$return = $this->ticks;
		return $return;
	}

	protected function _getTimeline()
	{
		if( NULL !== $this->_timeline ){
			return $this->_timeline;
		}

		$return = array();
		reset( $this->blocks );
		foreach( $this->blocks as $block ){
			list( $start, $end, $seats ) = $block;

			if( ! isset($return[$start]) ){
				$return[$start] = array( 0, 0 ); // array( totalAvailable, taken )
			}
			if( ! isset($return[$end]) ){
				$return[$end] = array( 0, 0 );
			}
		}

		ksort( $return );
		$discreteChanges = array_keys( $return );

		reset( $this->blocks );
		foreach( $this->blocks as $block ){
			list( $start, $end, $seats ) = $block;

			foreach( $discreteChanges as $time ){
				if( $time > $end ){
					break;
				}
				if( $time < $start ){
					continue;
				}

			// availability
				if( $seats > 0 ){
					$return[ $time ][0] += $seats;
					if( $time == $end ){
						$return[ $time ][0] -= $seats;
					}
				}
			// taken
				else {
					$return[ $time ][1] += (-$seats);
					if( $time == $end ){
						$return[ $time ][1] -= (-$seats);
					}
				}
			}
		}

		$this->_timeline = $return;
		return $return;
	}
}