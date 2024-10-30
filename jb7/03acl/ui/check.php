<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_03Acl_Ui_Check
{
	public function __construct(
		JB7_01Users_Data_Repo $repoUsers,
		JB7_03Acl_Data_Repo $repoAcl,
		HC4_Auth_Interface $auth
	)
	{}

	public function checkAdmin( $slug )
	{
		$return = FALSE;

		$currentUserId = $this->auth->getCurrentUserId();
		if( ! $currentUserId ){
			return $return;
		}

		$user = $this->repoUsers->findById( $currentUserId );
		if( ! $user ){
			return $return;
		}

		if( ! $this->repoAcl->isAdmin($user) ){
			return $return;
		}

		$return = TRUE;
		return $return;
	}
}