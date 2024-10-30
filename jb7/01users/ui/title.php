<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_01Users_Ui_Title
{
	public function __construct(
		HC4_Auth_Interface $auth
	)
	{}

	public function render( JB7_01Users_Data_Model $model )
	{
		$return = $model->username . ' (' . $model->title . ')';

		$currentUserId = $this->auth->getCurrentUserId();
		if( $model->id && ($currentUserId == $model->id) ){
			$return = '* ' . $return;
		}

		return $return;
	}
}