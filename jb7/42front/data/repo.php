<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_42Front_Data_Repo_
{
	public function findNextSlot( $startDateTime );
	public function findSlots( $startDateTime, $endDateTime );
}

class JB7_42Front_Data_Repo
	implements JB7_42Front_Data_Repo_
{

	public function __construct(
		HC4_Time_Interface $t,

		JB7_41Schedule_Data_Repo $repoSchedule,
		JB7_11Calendars_Data_Repo $repoCalendars,

		HC4_Auth_Interface $auth
	)
	{}

	public function getCalendars()
	{
		$return = $this->repoCalendars->findAll();

		$return = array_filter( $return, function($e){
			return ( 'active' == $e->status );
		});
		$return = array_filter( $return, function($e){
			return ( 'private' != $e->access );
		});

		$currentUserId = $this->auth->getCurrentUserId();
		if( $currentUserId ){
		}
		else {
			$return = array_filter( $return, function($e){
				return ( 'public' == $e->access );
			});
		}
		return $return;
	}

	public function findNextSlot( $startDateTime )
	{
		$return = NULL;

		$calendars = $this->getCalendars();
		reset( $calendars );
		foreach( $calendars as $calendar ){
			$thisStartDateTime = $this->t->setNow()
				->smartModifyDown( $calendar->minFromNow )
				->getDateTimeDb()
				;

			$thisStartDateTime = ( $startDateTime > $thisStartDateTime ) ? $startDateTime : $thisStartDateTime;

			$thisNextSlot = $this->repoSchedule->findNextSlot( $thisStartDateTime, $calendar );
			if( ! $thisNextSlot ){
				continue;
			}

			$thisMaxStartDateTime = $this->t->setNow()
				->smartModifyUp( $calendar->maxFromNow )
				->getDateTimeDb()
				;

			if( $thisNextSlot->startDateTime > $thisMaxStartDateTime ){
				continue;
			}

			if( (! $return) OR ($return->startDateTime > $thisNextSlot->startDateTime) ){
				$return = $thisNextSlot;
			}
		}

		return $return;
	}

	public function findSlots( $startDateTime, $endDateTime )
	{
		$return = array();

		$calendars = $this->getCalendars();
		reset( $calendars );
		foreach( $calendars as $calendar ){
			$thisSlots = $this->repoSchedule->findSlots( $startDateTime, $endDateTime, $calendar );
			$return = array_merge( $return, $thisSlots );
		}

		usort( $return, function($a, $b){
			return ( $a->startDateTime > $b->startDateTime );
		});

		return $return;
	}
}