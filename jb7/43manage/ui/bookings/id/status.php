<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Bookings_Id_Status
{
	public function __construct(
		JB7_31Bookings_Data_Repo $repo,
		JB7_31Bookings_Ui_Status $viewStatus,

		HC4_Html_Input_RadioSet $inputRadioSet,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug, $id )
	{
		$model = $this->repo->findById( $id );

		$return = $this->render( $model );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( JB7_31Bookings_Data_Model $model )
	{
		$statuses = JB7_31Bookings_Data_Model::getStatuses();
		$statuses = array_keys( $statuses );

		$statusOptions = array();
		foreach( $statuses as $status ){
			$statusOptions[ $status ] = $this->viewStatus->render( $status );
		}

		ob_start();
?>
<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-form-elements">

		<div class="hc4-form-element">
			<?php echo $this->inputRadioSet->render( 'status', $statusOptions, $model->status ); ?>
		</div>
	</div>

	<div class="hc4-form-buttons">
		<input type="submit" class="hc4-admin-btn-primary" value="__Save__">
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, array $post, $id )
	{
		$model = $this->repo->findById( $id );

		try {
			$model = clone $model;
			$model->status = $post['status'];
			$model = $this->repo->update( $model );
		}
		catch( HC4_App_Exception_DataError $e ){
			$to = '-referrer-';
			$return = array( $to, NULL, $e->getMessage() );
			return $return;
		}

		$slugArray = explode( '/', $slug );
		$to = implode( '/', array_slice($slugArray, 0, -2) );
		$return = array( $to, '__Booking Saved__' );

		return $return;
	}
}