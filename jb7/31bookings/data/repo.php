<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_31Bookings_Data_Repo_
{
	public function generateRefno();
	public function normalizeRefno( $refno );
	public function createToken( JB7_31Bookings_Data_Model $model );

	public function create( JB7_31Bookings_Data_Model $model );
	public function update( JB7_31Bookings_Data_Model $model );
	public function delete( JB7_31Bookings_Data_Model $model );

	public function find( $startDateTime, $endDateTime );
	public function findById( $id );
	public function findByRefno( $refno );
	public function findByToken( $token );

	public function findNext( $startDateTime, array $calendars = array() );
	public function findPrev( $startDateTime, array $calendars = array() );
}

class JB7_31Bookings_Data_Repo
	implements JB7_31Bookings_Data_Repo_
{
	protected $_loaded = array();
	protected $_loadedByToken = array();
	protected $_loadedStart = NULL;
	protected $_loadedEnd = NULL;

	public function __construct(
		HC4_Time_Interface $t,
		JB7_31Bookings_Data_Crud $crud,
		JB7_11Calendars_Data_Repo $repoCalendars,
		JB7_31Bookings_Data_Fields_Repo $repoFields,
		HC4_App_Events $events
	)
	{}

	public function normalizeRefno( $refno )
	{
		$return = $refno;
		$return = str_replace( '-', '', $return );
		$return = strtolower( $return );
		$return = substr( $return, 0, 6 );
		return $return;
	}

	public function generateRefno()
	{
		$parts = array();
		$parts[] = HC4_App_Functions::generateRand( 2, array('letters' => TRUE, 'digits' => FALSE, 'caps' => FALSE) );
		$parts[] = HC4_App_Functions::generateRand( 4, array('letters' => FALSE, 'digits' => TRUE, 'caps' => FALSE) );

		$return = join( '', $parts );
		return $return;
	}

	public function createToken( JB7_31Bookings_Data_Model $model )
	{
		$token = HC4_App_Functions::generateRand( 16 );
		$expire = $this->t->setNow()->modify('+1 day')->getDateTimeDb();

		$values = array(
			'token'			=> $token,
			'token_expire'	=> $expire,
			);
		$this->crud->update( $model->id, $values );
		return $token;
	}

	protected function _toTable( JB7_31Bookings_Data_Model $model )
	{
		$return = array(
			'status'				=> $model->status,
			'start_datetime'	=> $model->startDateTime,
			'end_datetime'		=> $model->endDateTime,
			'calendar_id'		=> $model->calendar->id,
			'refno'				=> $model->refno,
			'details'			=> json_encode( $this->_detailsToTable($model->details) ),
			);
		return $return;
	}

	protected function _detailsToTable( JB7_31Bookings_Data_Model_Details $details )
	{
		$return = array();
		$fields = $this->repoFields->findAll();
		foreach( $fields as $f ){
			$return[ $f->name ] = $details->{$f->name};
		}
		return $return;
	}

	protected function _fromTable( array $array )
	{
		$return = new JB7_31Bookings_Data_Model;

		$return->id = $array['id'];
		$return->status = $array['status'];
		$return->startDateTime = $array['start_datetime'];
		$return->endDateTime = $array['end_datetime'];
		$return->refno = $array['refno'];
		$return->token = $array['token'];
		$return->tokenExpire = $array['token_expire'];

		$return->calendar = $this->repoCalendars->findById( $array['calendar_id'] );
		$return->details = $this->_detailsFromTable( json_decode($array['details'], TRUE) );

		return $return;
	}

	protected function _detailsFromTable( array $array )
	{
		$return = new JB7_31Bookings_Data_Model_Details;

		$fields = $this->repoFields->findAll();
		foreach( $fields as $f ){
			$return->{$f->name} = isset( $array[$f->name] ) ? $array[$f->name] : NULL;
		}

		return $return;
	}

	public function create( JB7_31Bookings_Data_Model $model )
	{
	// required
		if( ! $model->calendar->id ){
			$msg = '__Calendar__' . ': ' . '__Required Field__';
			throw new HC4_App_Exception_DataError( $msg );
		}
		if( $model->startDateTime >= $model->endDateTime ){
			$msg = '__Wrong Times__' . ': ' . $model->startDateTime . ' - ' . $model->endDateTime;
			throw new HC4_App_Exception_DataError( $msg );
		}

		if( ! $model->refno ){
			$model->refno = $this->generateRefno();
		}

		$values = $this->_toTable( $model );

		$id = $this->crud->create( $values );
		$model->id = $id;

		$this
			->events->publish( __CLASS__, $id, array('id' => array(NULL, $id)) )
			;

		return $model;
	}

	public function update( JB7_31Bookings_Data_Model $model )
	{
		$id = $model->id;
 
	// required
		if( ! $model->calendar->id ){
			$msg = '__Calendar__' . ': ' . '__Required Field__';
			throw new HC4_App_Exception_DataError( $msg );
		}
		if( $model->startDateTime >= $model->endDateTime ){
			$msg = '__Wrong Times__' . ': ' . $model->startDateTime . ' - ' . $model->endDateTime;
			throw new HC4_App_Exception_DataError( $msg );
		}

		$calendarId = $model->calendar->id;

		$current = $this->findById( $id );

		$currentArray = $this->_toTable( $current );
		$array = $this->_toTable( $model );

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
			$this->_loaded[ $model->id ] = $model;
		}

		if( $changes ){
			$this
				->events->publish( __CLASS__, $id, $changes )
				;
		}

		return $model;
	}

	public function delete( JB7_31Bookings_Data_Model $model )
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

	public function find( $startDateTime, $endDateTime )
	{
		$return = array();

	// if already loaded
		if( (NULL !== $this->_loadedStart) && (NULL !== $this->_loadedEnd) ){
			if( ($startDateTime >= $this->_loadedStart) && ($endDateTime <= $this->_loadedEnd) ){
				reset( $this->_loaded );
				foreach( $this->_loaded as $id => $shift ){
					if( $shift->startDateTime >= $endDateTime ){
						break;
						// continue;
					}
					if( $shift->endDateTime <= $startDateTime ){
						continue;
					}
					$return[ $id ] = $shift;
				}
				return $return;
			}
		}

		$q = new HC4_Crud_Q;
		$q->sort( 'start_datetime' );
		$q->where( 'start_datetime', '<', $endDateTime );
		$q->where( 'end_datetime', '>', $startDateTime );

		$results = $this->crud->read( $q );

		foreach( $results as $e ){
			$model = $this->_fromTable( $e );
			if( ! $model ){
				continue;
			}
			$return[ $model->id ] = $model;
			$this->_loaded[ $model->id ] = $model;
		}

		if( (NULL === $this->_loadedStart) && (NULL === $this->_loadedEnd) ){
			$this->_loadedStart = $startDateTime;
			$this->_loadedEnd = $endDateTime;
		}
		if( $startDateTime < $this->_loadedStart ){
			$this->_loadedStart = $startDateTime;
		}
		if( $endDateTime > $this->_loadedEnd ){
			$this->_loadedEnd = $endDateTime;
		}

		return $return;
	}

	public function findById( $id )
	{
		if( isset($this->_loaded[$id]) ){
			return $this->_loaded[$id];
		}

		$return = NULL;

		$q = new HC4_Crud_Q;
		$q->where( 'id', '=', (int) $id );
		$q->limit( 1 );

		$results = $this->crud->read( $q );
		if( $results ){
			$e = array_shift( $results );
			$return = $this->_fromTable( $e );
		}

		$this->_loaded[$id] = $return;
		return $return;
	}

	public function findByRefno( $refno )
	{
		$return = NULL;

		$refno = $this->normalizeRefno( $refno );

		$q = new HC4_Crud_Q;
		$q->where( 'refno', '=', $refno );
		$q->limit( 1 );

		$results = $this->crud->read( $q );
		if( $results ){
			$e = array_shift( $results );
			$return = $this->_fromTable( $e );
			$this->_loaded[ $return->id ] = $return;
		}

		return $return;
	}

	public function findByToken( $token )
	{
		if( isset($this->_loadedByToken[$token]) ){
			return $this->_loadedByToken[$token];
		}

		$return = NULL;

		$now = $this->t->setNow()->getDateTimeDb();

		$q = new HC4_Crud_Q;
		$q->where( 'token', '=', $token );
		$q->where( 'token_expire', '>', $now );
		$q->limit( 1 );

		$results = $this->crud->read( $q );
		if( $results ){
			$e = array_shift( $results );
			$return = $this->_fromTable( $e );
			$this->_loaded[ $return->id ] = $return;
		}
		else {
			$msg = 'Booking Not Found';
			throw new HC4_App_Exception_DataError( $msg );
		}

		$this->_loadedByToken[$token] = $return;
		return $return;
	}

	public function findNext( $startDateTime, array $calendars = array() )
	{
		$return = NULL;

		if( ! $calendars ){
			$calendars = $this->repoCalendars->findAll();
		}
		$calendars = array_filter( $calendars, function($e){
			return ( 'active' == $e->status );
		});
		$calendarIds = array();
		foreach( $calendars as $calendar ){
			$calendarIds[ $calendar->id ] = $calendar->id;
		}

		$q = new HC4_Crud_Q;
		$q->where( 'start_datetime', '>=', $startDateTime );
		$q->where( 'calendar_id', '=', $calendarIds );
		$q->sort( 'start_datetime' );
		$q->limit( 1 );

		$results = $this->crud->read( $q );
		if( $results ){
			$e = array_shift( $results );
			$return = $this->_fromTable( $e );
			$this->_loaded[ $return->id ] = $return;
		}

		return $return;
	}

	public function findPrev( $startDateTime, array $calendars = array() )
	{
	}
}