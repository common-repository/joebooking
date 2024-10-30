<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_31Bookings_Data_Fields_Repo_
{
	public function findAll();
	public function findFirstRequired();
}

class JB7_31Bookings_Data_Fields_Repo
	implements JB7_31Bookings_Data_Fields_Repo_
{
	protected $fields = array();

	public function __construct(
		JB7_31Bookings_Data_Fields_Type_Text $typeText
	)
	{
		$name = clone $typeText;
		$name->name = 'name';
		$name->label = '__Name__';
		$name->sortWeight = 0;
		$name->details = array( 'required' => TRUE );
		$this->register( $name );

		$email = clone $typeText;
		$email->name = 'email';
		$email->label = '__Email__';
		$email->sortWeight = 0;
		$email->details = array( 'required' => TRUE );
		$this->register( $email );
	}

	public function register( JB7_31Bookings_Data_Fields_Type $field )
	{
		$this->fields[ $field->name ] = $field;
		return $this;
	}

	public function findFirstRequired()
	{
		$return = NULL;

		$fields = $this->findAll();
		foreach( $fields as $e ){
			if( 'viewedit' != $e->customerAccess ) {
				continue;
			}

			if( isset($e->details['required']) && $e->details['required'] ){
				$return = $e;
				break;
			}
		}
		return $return;
	}

	public function findAll()
	{
		return $this->fields;
	}
}