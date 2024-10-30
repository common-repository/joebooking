<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_21Availability_Data_Repo_
{
	public function create( JB7_21Availability_Data_Model $model );
	public function update( JB7_21Availability_Data_Model $model );
	public function delete( JB7_21Availability_Data_Model $model );
	public function findById( $id );
	public function findAll();

	public function isAppliedOnDate( JB7_21Availability_Data_Model $model, $date );

	public function createSync( JB7_11Calendars_Data_Model $toCalendar, JB7_11Calendars_Data_Model $fromCalendar );
	public function deleteSync( JB7_11Calendars_Data_Model $toCalendar );
	public function getSync( JB7_11Calendars_Data_Model $calendar );

	public function findNext( $startDateTime, JB7_11Calendars_Data_Model $calendar );
	public function findByDates( $startDate, $endDate, JB7_11Calendars_Data_Model $calendar );
}

class JB7_21Availability_Data_Repo
	implements JB7_21Availability_Data_Repo_
{
	protected $_loaded = NULL;
	protected $_loadedSync = array();

	public function __construct(
		HC4_Time_Interface $t,

		JB7_11Calendars_Data_Repo $repoCalendars,

		JB7_21Availability_Data_Crud $crud,
		JB7_21Availability_Data_Sync_Crud $crudSync,

		HC4_App_Events $events
	)
	{}

	public function create( JB7_21Availability_Data_Model $model )
	{
	// required
		if( ! $model->calendar->id ){
			$msg = '__Calendar__' . ': ' . '__Required Field__';
			throw new HC4_App_Exception_DataError( $msg );
		}

		if( $model->validToDate && $model->validFromDate && $model->validFromDate > $model->validToDate ){
			$msg = '__Wrong Dates__' . ': ' . $model->validFromDate . ' - ' . $model->validToDate;
			throw new HC4_App_Exception_DataError( $msg );
		}

		$calendarSlotSize = $model->calendar->slotSize;
		$thisSize = $model->toTime - $model->fromTime;
		if( $thisSize < $calendarSlotSize ){
			$msg = '__Time Range Too Short For Slot Size__';
			throw new HC4_App_Exception_DataError( $msg );
		}

		$calendarId = $model->calendar->id;

		$array = $model->toArray();

		$values = $array;

		unset( $values['id'] );
		$values['calendar_id'] = $calendarId;
		unset( $values['calendar'] );

		if( $model->appliedOnDetails ){
			$values['applied_on_details'] = json_encode( $values['applied_on_details'] );
		}

		$id = $this->crud->create( $values );

		$array['id'] = $id;
		$model = JB7_21Availability_Data_Model::fromArray( $array );

		$this
			->events->publish( __CLASS__, $id, array('id' => array(NULL, $id)) )
			;

		return $model;
	}

	public function update( JB7_21Availability_Data_Model $model )
	{
		$id = $model->id;
 
	// required
		if( ! $model->calendar->id ){
			$msg = '__Calendar__' . ': ' . '__Required Field__';
			throw new HC4_App_Exception_DataError( $msg );
		}

		if( $model->validToDate && $model->validFromDate && $model->validFromDate > $model->validToDate ){
			$msg = '__Wrong Dates__' . ': ' . $model->validFromDate . ' - ' . $model->validToDate;
			throw new HC4_App_Exception_DataError( $msg );
		}

		$calendarId = $model->calendar->id;

		$current = $this->findById( $id );
		$currentArray = $current->toArray();
		$currentArray['calendar_id'] = $current->calendar->id;

		$array = $model->toArray();
		unset( $array['id'] );
		$array['calendar_id'] = $calendarId;
		unset( $array['calendar'] );

		if( $model->appliedOnDetails ){
			$array['applied_on_details'] = json_encode( $model->appliedOnDetails );
		}

		$changes = array();
		$keys = array_keys( $array );
		foreach( $keys as $k ){
			if( ! array_key_exists($k, $currentArray) ){
				unset( $array[$k] );
				continue;
			}

			$v = $array[$k];
			if( $v == $currentArray[$k] ){
				unset( $array[$k] );
				continue;
			}

			$changes[ $k ] = array( $currentArray[$k], $v );
		}

		if( $array ){
			$this->crud->update( $id, $array );
		}

		if( $changes ){
			$this
				->events->publish( __CLASS__, $id, $changes )
				;
		}

		return $model;
	}

	public function delete( JB7_21Availability_Data_Model $model )
	{
		if( ! $model->id ){
			return $model;
		}
		$this->crud->delete( $model->id );

		$this
			->events->publish( __CLASS__, $id, array('id' => array($id, NULL)) )
			;

		return $model;
	}

	public function findAll()
	{
		$this->_load();
		return $this->_loaded;
	}

	protected function _load()
	{
		if( NULL !== $this->_loaded ){
			return;
		}

		$this->_loaded = array();

	// LOAD SYNC
		$syncFrom = array();
		$syncTo = array();

		$q = new HC4_Crud_Q;
		$results = $this->crudSync->read( $q );
		foreach( $results as $e ){
			$this->_loadedSync[] = $e;

			$syncTo[ $e['to_calendar_id'] ] = $e['from_calendar_id'];
			$syncFrom[ $e['from_calendar_id'] ] = array();
		}

		$q = new HC4_Crud_Q;
		$q->sortDesc( 'priority' );
		$results = $this->crud->read( $q );

		$count = count( $results );
		foreach( $results as $e ){
			if( isset($syncTo[$e['calendar_id']]) ){
				continue;
			}
			$calendar = $this->repoCalendars->findById( $e['calendar_id'] );
			if( ! $calendar ){
				continue;
			}

			$e['calendar'] = $calendar;
			if( strlen($e['applied_on_details']) ){
				$e['applied_on_details'] = json_decode( $e['applied_on_details'], TRUE );
			}

			$model = JB7_21Availability_Data_Model::fromArray( $e );
			$this->_loaded[] = $model;

			if( isset($syncFrom[$e['calendar_id']]) ){
				$syncFrom[$e['calendar_id']][] = $model;
			}
		}

		foreach( $syncTo as $toCalendarId => $fromCalendarId ){
			$entriesFrom = $syncFrom[ $fromCalendarId ];
			foreach( $entriesFrom as $e ){
				$array = $e->toArray();
				$array['calendar'] = $this->repoCalendars->findById( $toCalendarId );
				$model = JB7_21Availability_Data_Model::fromArray( $array );
				$this->_loaded[] = $model;
			}
		}
	}

	public function findById( $id )
	{
		$return = NULL;

		$all = $this->findAll();
		foreach( $all as $e ){
			if( $id == $e->id ){
				$return = $e;
				break;
			}
		}

		return $return;
	}

	public function findByDates( $fromDate, $toDate, JB7_11Calendars_Data_Model $calendar )
	{
		$return = array();

		$all = $this->findAll();
		$all = array_filter( $all, function($e) use ($calendar){
			return $calendar->id == $e->calendar->id;
		});

		foreach( $all as $e ){
			if( $e->validFromDate && $e->validFromDate > $toDate ){
				continue;
			}
			if( $e->validToDate && $e->validToDate < $fromDate ){
				continue;
			}

			$checkFromDate = ( $e->validFromDate && ($e->validFromDate > $fromDate) ) ? $e->validFromDate : $fromDate;
			$checkToDate = ( $e->validToDate && ($e->validToDate < $toDate) ) ? $e->validToDate : $toDate;

			switch( $e->appliedOn ){
				case 'everyday':
					$date = $checkFromDate;
					$this->t->setDateDb( $date );

					while( $date <= $checkToDate ){
						if( ! isset($return[$date]) ){
							$return[$date] = array();
						}
						$return[$date][] = $e;
						$date = $this->t->modify( '+1 day' )->getDateDb();
					}
					break;

				case 'daysofweek':
					$daysOfWeek = $e->appliedOnDetails;
					$date = $checkFromDate;
					$this->t->setDateDb( $date );
					while( $date <= $checkToDate ){
						$thisDayOfWeek = $this->t->getWeekday();
						if( in_array($thisDayOfWeek, $daysOfWeek) ){
							if( ! isset($return[$date]) ){
								$return[$date] = array();
							}
							$return[$date][] = $e;
						}
						$date = $this->t->modify( '+1 day' )->getDateDb();
					}
					break;
			}
		}

		$dates = array_keys( $return );
		foreach( $dates as $date ){
			 $availabilities = $return[$date];

		// leave availability only with the highest priority
			reset( $availabilities );
			$highestPriority = NULL;
			foreach( $availabilities as $e ){
				if( (NULL === $highestPriority) OR ($e->priority > $highestPriority) ){
					$highestPriority = $e->priority;
				}
			}

			$availabilities = array_filter( $availabilities, function($e) use ($highestPriority){
				return ( $e->priority == $highestPriority );
			});

			$return[$date] = $availabilities;
		}

// _print_r( $return );
// exit;

		return $return;
	}

	public function findNext( $startDateTime, JB7_11Calendars_Data_Model $calendar )
	{
		$return = array();

		$all = $this->findAll();
		$all = array_filter( $all, function($e) use ($calendar){
			return $calendar->id == $e->calendar->id;
		});

		$rexDate = $this->t->setDateTimeDb( $startDateTime )->getDateDb();

		$return = array();
		while( $rexDate ){
		// if we have for rexDate
			reset( $all );
			foreach( $all as $e ){
				if( $this->isAppliedOnDate($e, $rexDate) ){
					$thisEndDateTime = $this->t->setDateDb( $rexDate )
						->modify( '+ ' . $e->toTime . ' seconds' )
						->getDateTimeDb()
						;
					if( $thisEndDateTime <= $startDateTime ){
						continue;
					}

					if( ! isset($return[$rexDate]) ){
						$return[$rexDate] = array();
					}
					$return[$rexDate][] = $e;
				}
			}

			if( $return ){
				break;
			}

		// find next date
			$tomorrow = $this->t->setDateDb( $rexDate )->modify('+1 day')->getDateDb();
			$newRexDate = NULL;

			reset( $all );
			foreach( $all as $e ){
				if( $e->validToDate && ($e->validToDate <= $rexDate) ){
					continue;
				}

				if( $e->validFromDate && $e->validFromDate > $rexDate ){
					$thisRexDate = $e->validFromDate;
				}
				else {
					$thisRexDate = $tomorrow;
				}

				if( ! $newRexDate OR ($thisRexDate < $newRexDate) ){
					$newRexDate = $thisRexDate;
				}
			}

			$rexDate = $newRexDate ? $newRexDate : NULL;
		}

		if( $return ){
			$date = current( array_keys($return) );
			if( count($return[$date]) > 1 ){
				usort( $return[$date], function($a, $b){
					return ($a->fromTime < $b->toTime);
				});
			}
			$return[$date] = $return[$date][0];
		}

		return $return;
	}

	public function isAppliedOnDate( JB7_21Availability_Data_Model $e, $date )
	{
		$return = FALSE;

		if( $e->validFromDate && $e->validFromDate > $date ){
			return $return;
		}

		if( $e->validToDate && $e->validToDate < $date ){
			return $return;
		}

		switch( $e->appliedOn ){
			case 'everyday':
				$return = TRUE;
				break;

			case 'daysofweek':
				$daysOfWeek = $e->appliedOnDetails;
				$thisDayOfWeek = $this->t->setDateDb( $date )->getWeekday();
				if( in_array($thisDayOfWeek, $daysOfWeek) ){
					$return = TRUE;
				}
				break;
		}

		return $return;
	}

	public function createSync( JB7_11Calendars_Data_Model $toCalendar, JB7_11Calendars_Data_Model $fromCalendar )
	{
		$values = $array;
		$values['to_calendar_id'] = $toCalendar->id;
		$values['from_calendar_id'] = $fromCalendar->id;
		$this->crudSync->create( $values );
	}

	public function deleteSync( JB7_11Calendars_Data_Model $toCalendar )
	{
		$q = new HC4_Crud_Q;
		$q->where( 'to_calendar_id', '=', $toCalendar->id );
		$results = $this->crudSync->read( $q );

		$ids = array();
		foreach( $results as $e ){
			$ids[] = $e['id'];
		}
		
		foreach( $ids as $id ){
			$this->crudSync->delete( $id );
		}
	}

	public function getSync( JB7_11Calendars_Data_Model $calendar )
	{
		$return = NULL;

		$this->_load();

		foreach( $this->_loadedSync as $sync ){
			if( $sync['to_calendar_id'] == $calendar->id ){
				$return = $sync['from_calendar_id'];
				break;
			}
		}

		return $return;
	}
}