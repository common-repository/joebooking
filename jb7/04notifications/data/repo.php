<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Data_Repo
{
	private $templates = array();
	protected $_loaded = NULL;
	private $disabled = array();

	public function __construct(
		JB7_04Notifications_Data_Crud $crud
	)
	{}

	public function register( $msgId, $subject, $body )
	{
		$msg = new JB7_04Notifications_Service_Message( $msgId, $subject, $body );
		$this->templates[ $msgId ] = $msg;
		return $this;
	}

	public function saveById( $msgId, $subject, $body )
	{
		$this->_load();

		$values = array(
			'message_id'	=> $msgId,
			'subject'		=> $subject,
			'body'			=> $body,
			);

		$q = new HC4_Crud_Q;
		$q->where( 'message_id', '=', $msgId );
		$results = $this->crud->read( $q );
		if( $results ){
			$results = array_shift( $results );
			$id = $results['id'];
			$this->crud->update( $id, $values );
		}
		else {
			$this->crud->create( $values );
		}
	}

	public function isDisabled( $msdId )
	{
		$this->_load();
		return isset( $this->disabled[$msdId] );
	}

	public function disable( $msgId )
	{
		$this->_load();
		$values = array(
			'is_disabled'	=> 1,
			);

		if( isset($this->templates[$msgId]) ){
			$q = new HC4_Crud_Q;
			$q->where( 'message_id', '=', $msgId );
			$results = $this->crud->read( $q );
			if( $results ){
				$results = array_shift( $results );
				$id = $results['id'];
				$this->crud->update( $id, $values );
			}
		}
	}

	public function enable( $msgId )
	{
		$this->_load();
		$values = array(
			'is_disabled'	=> 0,
			);

		if( isset($this->templates[$msgId]) ){
			$q = new HC4_Crud_Q;
			$q->where( 'message_id', '=', $msgId );
			$results = $this->crud->read( $q );
			if( $results ){
				$results = array_shift( $results );
				$id = $results['id'];
				$this->crud->update( $id, $values );
			}
		}
	}

	public function findById( $id )
	{
		$this->_load();
		$return = NULL;
		if( isset($this->templates[$id]) ){
			$return = $this->templates[$id];
		}

		return $return;
	}

	protected function _load()
	{
		if( NULL === $this->_loaded ){
			$this->_loaded = array();

			$q = new HC4_Crud_Q;
			$results = $this->crud->read( $q );

			$count = count( $results );
			foreach( $results as $e ){
				$msg = new JB7_04Notifications_Service_Message( $e['message_id'], $e['subject'], $e['body'] );
				$this->templates[ $msg->id ] = $msg;

				if( $e['is_disabled'] ){
					$this->disabled[ $msg->id ] = $msg->id;
				}
			}
		}
	}
}