<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface JB7_03Acl_00WordPress_Data_Repo_
extends JB7_03Acl_Data_Repo
{
	public function getDefaultAdminRoles();
	public function getAdminRoles();
	public function getAllRoles();
}

class JB7_03Acl_00WordPress_Data_Repo
	// implements JB7_03Acl_Data_Repo
	implements JB7_03Acl_00WordPress_Data_Repo_
{
	protected $_defaultAdminRoles = array( 'administrator', 'developer', 'jb7_admin' );

	public function __construct(
		HC4_Settings_Interface $settings,
		JB7_01Users_00WordPress_Data_Repo $repoWpUsers
	)
	{}

	public function getAllRoles()
	{
		global $wp_roles;
		if( ! isset($wp_roles) ){
			$wp_roles = new WP_Roles();
		}

		$return = array();
		$names = $wp_roles->get_names();
		foreach( $names as $k => $v ){
			$k = str_replace(' ', '_', $k);
			$return[ $k ] = $v;
		}

		return $return;
	}

	public function getDefaultAdminRoles()
	{
		return $this->_defaultAdminRoles;
	}

	public function getAdminRoles()
	{
		$return = $this->getDefaultAdminRoles();

		global $wp_roles;
		if( ! isset($wp_roles) ){
			$wp_roles = new WP_Roles();
		}

		$wpRoles = array();
		$names = $wp_roles->get_names();
		foreach( $names as $k => $v ){
			$k = str_replace(' ', '_', $k);
			$wpRoles[ $k ] = $v;
		}

		foreach( array_keys($wpRoles) as $wpRoleName ){
			$pName = 'users_wp_' . $wpRoleName . '_' . 'admin';
			$value = $this->settings->get($pName);
			if( $value ){
				$return[] = $wpRoleName;
			}
		}

		return $return;
	}

	public function isAdmin( JB7_01Users_Data_Model $user )
	{
		static $results = array();
		if( array_key_exists($user->id, $results) ){
			return $results[ $user->id ];
		}

		$return = FALSE;

		$adminWpRoles = $this->getAdminRoles();

		$userdata = $user->raw;
		$thisWpRoles = $userdata->roles;

		if( array_intersect($adminWpRoles, $thisWpRoles) ){
			$return = TRUE;
		}

		return $return;
	}

	public function findAdmins()
	{
		static $return = NULL;
		if( NULL !== $return ){
			return $return;
		}

		$return = array();

		$q = array();

		$adminWpRoles = $this->getAdminRoles();
		$q['role__in'] = $adminWpRoles;

		$q['orderby'] = 'name';
		$q['order'] = 'ASC';

		$wpUsersQuery = new WP_User_Query( $q );
		$wpUsers = $wpUsersQuery->get_results();

		$return = array();
		$count = count( $wpUsers );
		for( $ii = 0; $ii < $count; $ii++ ){
			$model = $this->repoWpUsers->fromWordPress( $wpUsers[$ii] );
			$return[ $model->id ] = $model;
		}

		return $return;
	}

	public function findCustomers()
	{
		$return = array();

		$q = array();

		$adminWpRoles = $this->getAdminRoles();
		$q['role__not_in'] = $adminWpRoles;

		$q['orderby'] = 'name';
		$q['order'] = 'ASC';

		$wpUsersQuery = new WP_User_Query( $q );
		$wpUsers = $wpUsersQuery->get_results();

		$return = array();
		$count = count( $wpUsers );
		for( $ii = 0; $ii < $count; $ii++ ){
			$model = $this->repoWpUsers->fromWordPress( $wpUsers[$ii] );
			$return[ $model->id ] = $model;
		}

		return $return;
	}
}