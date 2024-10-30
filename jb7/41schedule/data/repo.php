<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_41Schedule_Data_Repo_
{
	public function groupSlots( array $slots );
	public function findNextSlot( $startDateTime, JB7_11Calendars_Data_Model $calendar );
	public function findSlots( $startDateTime, $endDateTime, JB7_11Calendars_Data_Model $calendar );
}

class JB7_41Schedule_Data_Repo
	implements JB7_41Schedule_Data_Repo_
{
	public function __construct(
		HC4_Time_Interface $t,

		JB7_11Calendars_Data_Repo $repoCalendars,
		JB7_21Availability_Data_Repo $repoAvailability,
		JB7_31Bookings_Data_Repo $repoBookings,

		HC4_App_Events $events
	)
	{}

	public function findNextSlot( $startDateTime, JB7_11Calendars_Data_Model $calendar )
	{
		$return = NULL;

		$startDate = $this->t->setDateTimeDb( $startDateTime )->getDateDb();

		$nextAvailability = $this->repoAvailability->findNext( $startDateTime, $calendar );
		while( $nextAvailability ){
			foreach( $nextAvailability as $date => $availability ){
				$foundStartDateTime = $this->t->setDateDb( $date )
					->modify( '+ ' . $availability->fromTime . ' seconds' )
					->getDateTimeDb()
					;
				if( $foundStartDateTime > $startDateTime ){
					$startDateTime = $foundStartDateTime;
				}
				$endDateTime = $this->t->setDateDb( $date )
					->modify( '+ ' . $availability->toTime . ' seconds' )
					->getDateTimeDb()
					;
				break;
			}

			$slots = $this->findSlots( $startDateTime, $endDateTime, $calendar );
			if( $slots ){
				$return = array_shift( $slots );
				$nextAvailability = array();
			}
			else {
				$startDateTime = $endDateTime;
				$nextAvailability = $this->repoAvailability->findNext( $startDateTime, $calendar );
			}
		}

 		return $return;
	}

	public function findSlots( $startDateTime, $endDateTime, JB7_11Calendars_Data_Model $calendar )
	{
		$return = array();

		$timeline = new JB7_41Schedule_Data_Model_Timeline;

		$startDate = $this->t->setDateTimeDb( $startDateTime )->getDateDb();
		$endDate = $this->t->setDateTimeDb( $endDateTime )->getDateDb();

		$availabilities = $this->repoAvailability
			->findByDates( $startDate, $endDate, $calendar )
			;

		foreach( $availabilities as $date => $dateAvailabilities ){
			foreach( $dateAvailabilities as $availability ){
				$thisStartDateTime = $this->t->setDateDb( $date )
					->modify( '+ ' . $availability->fromTime . ' seconds' )
					->getDateTimeDb()
					;
				$thisEndDateTime = $this->t->setDateDb( $date )
					->modify( '+' . $availability->toTime . ' seconds' )
					->getDateTimeDb()
					;

				$timeline
					->addBlock( $thisStartDateTime, $thisEndDateTime, 1 )
					;

			// ADD TICKS
				$slotSize = $availability->calendar->slotSize;
				$interval = $availability->interval;

				$rexDateTime = $thisStartDateTime;
				$thisEndDateTime = $this->t->setDateTimeDb( $thisEndDateTime )
					->modify( '- ' . $slotSize . ' seconds' )
					->getDateTimeDb()
					;

				$this->t->setDateTimeDb( $rexDateTime );
				while( $rexDateTime <= $thisEndDateTime ){
					$rexDateTimeEnd = $this->t
						->modify( '+' . $slotSize . ' seconds' )
						->getDateTimeDb()
						;
					$timeline
						->addTick( $rexDateTime, $rexDateTimeEnd )
						;

					$rexDateTime = $this->t
						->setDateTimeDb( $rexDateTime )
						->modify( '+' . $interval . ' seconds' )
						->getDateTimeDb()
						;
				}
			}
		}

	// BOOKINGS
		$bookings = $this->repoBookings->find( $startDateTime, $endDateTime );

		reset( $bookings );
		foreach( $bookings as $booking ){
			if( ! $booking->lock ){
				continue;
			}

			$bookingCalendarId = $booking->calendar->id;
			if( $bookingCalendarId != $calendar->id ){
				continue;
			}

			$timeline
				->addBlock( $booking->startDateTime, $booking->endDateTime, -1 )
				;
		}

	// BUILD SLOTS
		$ticks = $timeline->getAvailableTicks();
		foreach( $ticks as $tick ){
			if( $tick < $startDateTime ){
				continue;
			}
			if( $tick >= $endDateTime ){
				break;
			}

			$slotStartDateTime = $tick;
			if( $slotStartDateTime >= $endDateTime ){
				continue;
			}

			$slotDuration = $calendar->slotSize;
			$slotEndDateTime = $this->t->setDateTimeDb( $tick )
				->modify( '+' . $slotDuration . ' seconds' )
				->getDateTimeDb()
				;

			// if( $slotEndDateTime > $endDateTime ){
				// continue;
			// }

			$slot = new JB7_41Schedule_Data_Model_Slot;

			$slot->startDateTime = $slotStartDateTime;
			$slot->duration = $slotDuration;
			$slot->endDateTime = $slotEndDateTime;
			$slot->calendar = $calendar;

			$return[] = $slot;
		}

		usort( $return, function( $a, $b ){
			$aStartDateTime = $a->startDateTime;
			$bStartDateTime = $b->startDateTime;
			if( $aStartDateTime == $bStartDateTime ){
				return 0;
			}
			return ( $aStartDateTime > $bStartDateTime );
		});

		return $return;
	}

	public function groupSlots( array $slots )
	{
		$return = array();

		usort( $slots, function($a, $b){
			return ( $a->startDateTime > $b->startDateTime );
		});

		$current = NULL;
		foreach( $slots as $slot ){
			if( $current && ($current->endDateTime < $slot->startDateTime) ){
				$return[] = $current;
				$current = NULL;
			}
			if( ! $current ){
				$current = new JB7_41Schedule_Data_Model_SlotsGroup;
			}
			$current->add( $slot );
		}

		if( $current ){
			$return[] = $current;
		}

		return $return;
	}
}