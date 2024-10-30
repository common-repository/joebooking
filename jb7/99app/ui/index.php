<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_99App_Ui_Index
{
	public function __construct(
		HC4_Auth_Interface $auth,
		JB7_01Users_Data_Repo $repoUsers,
		JB7_03Acl_Data_Repo $repoAcl,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug )
	{
		$return = '';
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function title()
	{
		$return = NULL;

		$currentUserId = $this->auth->getCurrentUserId();
		if( ! $currentUserId ){
			return;
		}

		$return = '__Menu__';
		return $return;
	}
}