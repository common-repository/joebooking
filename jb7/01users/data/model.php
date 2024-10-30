<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_01Users_Data_Model
{
	const NO_EMAIL = 'noemail@hitcode.com';

	protected $id;
	protected $title;
	protected $username;
	protected $email;
	protected $password;
	protected $token;
	protected $status = 'active';	// 'active', 'suspended'
	protected $raw;

	private function __construct(){
	}

	public function toArray()
	{
		$return = array(
			'id'			=> $this->id,
			'title'		=> $this->title,
			'email'		=> $this->email ? $this->email : static::NO_EMAIL,
			'username'	=> $this->username,
			'status'		=> $this->status,
			);
		return $return;
	}

	public static function fromArray( array $array )
	{
		$return = new static;

		if( isset($array['id']) ){
			$return->id = $array['id'];
		}
		if( isset($array['title']) ){
			$return->title = $array['title'];
		}

		if( isset($array['email']) ){
			if( static::NO_EMAIL == $array['email'] ){
				$array['email'] = NULL;
			}
			$return->email = $array['email'];
		}

		if( isset($array['username']) ){
			$return->username = $array['username'];
		}
		if( isset($array['status']) ){
			$return->status = $array['status'];
		}
		if( isset($array['raw']) ){
			$return->raw = $array['raw'];
		}

		return $return;
	}

	public function __get( $name )
	{
		if( property_exists($this, $name) ){
			return $this->{$name};
		}
		else {
			$msg = 'Invalid property: ' . __CLASS__ . ': ' . $name;
			echo $msg . '<br>';
			// throw new HC4_App_Exception_DataError( 'Invalid property: ' . __CLASS__ . ': ' . $name );
		}
	}
}