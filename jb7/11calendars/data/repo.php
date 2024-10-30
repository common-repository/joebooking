<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_11Calendars_Data_Repo_
{
	public function create( JB7_11Calendars_Data_Model $model );
	public function update( JB7_11Calendars_Data_Model $model );
	public function delete( JB7_11Calendars_Data_Model $model );
	public function findById( $id );
	public function findAll();
}

class JB7_11Calendars_Data_Repo
	implements JB7_11Calendars_Data_Repo_
{
	protected $_loaded = NULL;

	public function __construct(
		JB7_11Calendars_Data_Crud $crud,
		HC4_App_Events $events
	)
	{}

	protected function _toTable( JB7_11Calendars_Data_Model $model )
	{
		$return = array(
			'title'				=> $model->title,
			'description'		=> $model->description,
			'status'				=> $model->status,
			'slot_size'			=> $model->slotSize,
			'capacity'			=> $model->capacity,
			'access'				=> $model->access,
			'min_from_now'		=> $model->minFromNow,
			'max_from_now'		=> $model->maxFromNow,
			'initial_status'	=> $model->initialStatus,
			);
		return $return;
	}

	protected function _fromTable( array $array )
	{
		$return = new JB7_11Calendars_Data_Model;

		$return->id = $array['id'];
		$return->title = $array['title'];
		$return->description = $array['description'];
		$return->status = $array['status'];
		$return->slotSize = $array['slot_size'];
		$return->capacity = $array['capacity'];
		$return->access = $array['access'];
		$return->minFromNow = $array['min_from_now'];
		$return->maxFromNow = $array['max_from_now'];
		$return->initialStatus = $array['initial_status'];

		return $return;
	}

	public function create( JB7_11Calendars_Data_Model $model )
	{
	// required
		if( ! strlen($model->title) ){
			$msg = '__Title__' . ': ' . '__Required Field__';
			throw new HC4_App_Exception_DataError( $msg );
		}

	// duplicated titles
		$q = new HC4_Crud_Q;
		$q->where( 'title', '=', $model->title );
		$q->limit( 1 );
		$already = $this->crud->read( $q );
		if( $already ){
			$msg = '__This value is already used__' . ': ' . strip_tags( $model->title );
			throw new HC4_App_Exception_DataError( $msg );
		}

		$values = $this->_toTable( $model );
		$id = $this->crud->create( $values );

		$model->id = $id;

		$this
			->events->publish( __CLASS__, $id, array('id' => array(NULL, $id)) )
			;

		return $model;
	}

	public function update( JB7_11Calendars_Data_Model $model )
	{
		$id = $model->id;
 
	// required
		if( ! strlen($model->title) ){
			$msg = '__Title__' . ': ' . '__Required Field__';
			throw new HC4_App_Exception_DataError( $msg );
		}

	// duplicated titles
		$q = new HC4_Crud_Q;
		$q->where( 'title', '=', $model->title );
		$q->where( 'id', '<>', $id );
		$q->limit( 1 );
		$already = $this->crud->read( $q );
		if( $already ){
			$msg = '__This value is already used__' . ': ' . strip_tags( $model->title );
			throw new HC4_App_Exception_DataError( $msg );
		}

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
		}

		if( $changes ){
			$this
				->events->publish( __CLASS__, $id, $changes )
				;
		}

		return $model;
	}

	public function delete( JB7_11Calendars_Data_Model $model )
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

	public function findById( $id )
	{
		$return = NULL;

		$all = $this->findAll();
		if( ! isset($all[$id]) ){
			return $return;
		}

		return $all[$id];
	}

	protected function _load()
	{
		if( NULL === $this->_loaded ){
			$this->_loaded = array();

			$q = new HC4_Crud_Q;
			$q->sort( 'title' );
			$results = $this->crud->read( $q );

			foreach( $results as $e ){
				$model = $this->_fromTable( $e );
				$this->_loaded[ $model->id ] = $model;
			}
		}
	}
}