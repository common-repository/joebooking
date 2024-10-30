<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Admin_Id
{
	public function __construct(
		JB7_11Calendars_Data_Repo $repo,
		JB7_11Calendars_Ui_Title $viewTitle
	)
	{}

	public function title( $id )
	{
		$model = $this->repo->findById( $id );

		$return = $this->viewTitle->render( $model );
		return $return;
	}
}