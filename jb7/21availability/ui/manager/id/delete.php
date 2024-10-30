<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Manager_Id_Delete
{
	public function __construct(
		JB7_21Availability_Data_Repo $repo
	)
	{}

	public function post( $slug, $post, $id )
	{
		$model = $this->repo->findById( $id );
		$this->repo->delete( $model );

		$slugArray = explode( '/', $slug );
		$to = implode( '/', array_slice($slugArray, 0, -2) );
		$return = array( $to, '__Availability Deleted__' );

		return $return;
	}
}