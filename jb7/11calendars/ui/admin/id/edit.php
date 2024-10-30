<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Admin_Id_Edit
{
	public function __construct(
		JB7_11Calendars_Data_Repo $repo,
		JB7_11Calendars_Ui_Access $viewAccess,

		HC4_Html_Input_Text $inputText,
		HC4_Html_Input_RichTextarea $inputTextarea,
		HC4_Html_Input_Select $inputSelect,
		HC4_Html_Input_Duration $inputDuration,
		HC4_Html_Input_MultiSet $inputMultiSet,
		HC4_Html_Input_Duration2 $inputDuration2,
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

	public function render( JB7_11Calendars_Data_Model $model )
	{
		$accessLabels = $this->viewAccess->getAllOptions();
		$accessOptions = array(
			'public'		=> $this->renderAccessPublic( $model ),
			'login'		=> $this->renderAccessPublic( $model ),
			'private'	=> $this->renderAccessPrivate( $model ),
			);

		$statusOptions = array(
			'pending'	=> '__Pending__',
			'approve'	=> '__Approved__',
		);

		ob_start();
?>
<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-form-elements">

		<div class="hc-grid hc-mxn2">
			<div class="hc-col hc-col-7 hc-px2">
				<div class="hc4-form-element">
					<div class="hc-fs2 hc-muted2">__ID__ <?php echo $model->id; ?></div>
				</div>

				<div class="hc4-form-element">
					<label>
						__Title__
						<?php echo $this->inputText->render( 'title', $model->title ); ?>
					</label>
				</div>

				<div class="hc4-form-element">
					<label>
						__Slot Size__
					</label>
					<?php echo $this->inputDuration->render( 'slot_size', $model->slotSize ); ?>
				</div>
			</div>

			<div class="hc-col hc-col-5 hc-px2">

				<div class="hc4-form-element">
					<label>
						__Access__
					</label>
					<?php echo $this->inputMultiSet->render( 'access', $accessLabels, $accessOptions, $model->access ); ?>
				</div>

				<div class="hc4-form-element">
					<label>
						__Initial Status__
					</label>
					<?php echo $this->inputRadioSet->renderInline( 'initial_status', $statusOptions, $model->initialStatus ); ?>
				</div>

			</div>
		</div>
	</div>

	<div class="hc4-form-buttons">
		<button type="submit" class="hc4-admin-btn-primary" title="__Save__">__Save__</button>
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function renderAccessPublic( JB7_11Calendars_Data_Model $model )
	{
		ob_start();
?>
<div class="hc4-form-element">
	<label>
		__Min From Now__
		<?php echo $this->inputDuration2->render( 'min_from_now', $model->minFromNow ); ?>
	</label>
</div>

<div class="hc4-form-element">
	<label>
		__Max From Now__
		<?php echo $this->inputDuration2->render( 'max_from_now', $model->maxFromNow ); ?>
	</label>
</div>

<?php 
		return ob_get_clean();
	}

	public function renderAccessPrivate( JB7_11Calendars_Data_Model $model )
	{
		ob_start();
?>
<div class="hc-italic">
__Private calendars are not available for booking by customers.__
</div>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, array $post, $id )
	{
	// VALIDATE POST
		$errors = array();
		if( ! ( isset($post['title']) && strlen($post['title']) ) ){
			$errors['title'] = '__Required Field__';
		}
		if( $errors ){
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		$model = $this->repo->findById( $id );
		$model = clone $model;

		$model->title = $post['title'];
		// $model->description = $post['description'];
		$model->slotSize = $this->inputDuration->grab( 'slot_size', $post );
		$model->access = $post['access'];
		$model->minFromNow = $this->inputDuration2->grab( 'min_from_now', $post );
		$model->maxFromNow = $this->inputDuration2->grab( 'max_from_now', $post );
		$model->initialStatus = $post['initial_status'];

	// DO
		try {
			$model = $this->repo->update( $model );
		}
		catch( HC4_App_Exception_DataError $e ){
			$to = '-referrer-';
			$return = array( $to, NULL, $e->getMessage() );
			return $return;
		}

		$slugArray = explode( '/', $slug );
		$to = implode( '/', array_slice($slugArray, 0, -1) );
		$return = array( $to, '__Calendar Saved__' );

		return $return;
	}
}