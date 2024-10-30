<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_01Users_00WordPress_Data_Repo
	implements JB7_01Users_Data_Repo
{
	public function __construct(
		HC4_Settings_Interface $settings
	)
	{}

	public function fromWordPress( $userdata )
	{
		$array = array(
			'id'			=> $userdata->ID,
			'title'		=> $userdata->display_name,
			'email'		=> $userdata->user_email,
			'username'	=> $userdata->user_login,
			'raw'			=> $userdata,
			);

		$return = JB7_01Users_Data_Model::fromArray( $array );
		return $return;
	}

	public function findAll()
	{
		static $return = NULL;
		if( NULL !== $return ){
			return $return;
		}

		$return = array();

		$q = array();
		$q['orderby'] = 'name';
		$q['order'] = 'ASC';

		$wpUsersQuery = new WP_User_Query( $q );
		$wpUsers = $wpUsersQuery->get_results();

		$return = array();
		$count = count( $wpUsers );
		for( $ii = 0; $ii < $count; $ii++ ){
			$model = $this->fromWordPress( $wpUsers[$ii] );
			$return[ $model->id ] = $model;
		}

		return $return;
	}

	public function findById( $id )
	{
		static $cache = array();
		if( isset($cache[$id]) ){
			return $cache[$id];
		}

		if( $id && ($userdata = get_user_by('id', $id)) ){
			$return = $this->fromWordPress( $userdata );
		}
		else {
			$array = array(
				'id'	=> 0
			);
			$return = JB7_01Users_Data_Model::fromArray( $array );
		}

		$cache[$id] = $return;
		return $return;
	}

	public function findByUsername( $username )
	{
		static $cache = array();
		if( array_key_exists($username, $cache) ){
			return $cache[$username];
		}

		$return = NULL;
		if( $username && ($userdata = get_user_by('login', $username)) ){
			$return = $this->fromWordPress( $userdata );
		}
		else {
			// $array = array(
				// 'id'	=> 0
			// );
			// $return = JB7_01Users_Data_Model::fromArray( $array );
		}

		$cache[$username] = $return;
		return $return;
	}

	public function checkPassword( JB7_01Users_Data_Model $model, $password )
	{
		$return = FALSE;

		$id = $model->id;

		$q = new HC4_Crud_Q;
		$q->where( 'id', '=', $id );
		$q->limit( 1 );
		$results = $this->crud->read( $q );

		if( ! $results ){
			return $return;
		}

		$results = array_shift( $results );

		$storedHashed = $results['password'];
		$salt = substr( $storedHashed, 0, self::SALT_LENGTH );
		$hashed = $this->hashPassword( $password, $salt );

		if( $hashed == $storedHashed ){
			$return = TRUE;
		}

		return $return;
	}

	public function create( JB7_01Users_Data_Model $model )
	{
	// title required
		if( ! strlen($model->title) ){
			$msg = '__Display Name__' . ': ' . '__Required Field__';
			throw new HC4_App_Exception_DataError( $msg );
		}

		$newPassword = $info['new_password'];
		$info = $this->convertTo( $info );
		$info['user_pass'] = $newPassword;

		$return = wp_insert_user( $info );
		if( is_wp_error($return) ){
			$msg = 'WordPress' . ': ' . $return->get_error_message();
			throw new HC4_App_Exception_DataError( $msg );
		}

		return $return;
	}

	public function setPassword( JB7_01Users_Data_Model $model, $password )
	{
		$return = TRUE;

		$id = $model->id;
		wp_set_password( $password, $id );

		return $return;
	}
}