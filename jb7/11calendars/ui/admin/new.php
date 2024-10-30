<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Admin_New
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

	public function get( $slug )
	{
		$return = $this->render();
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render()
	{
		$accessLabels = $this->viewAccess->getAllOptions();
		$accessOptions = array(
			'public'		=> $this->renderAccessPublic(),
			'login'		=> $this->renderAccessPublic(),
			'private'	=> $this->renderAccessPrivate(),
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
			<div class="hc-col hc-col-8 hc-px2">
				<div class="hc4-form-element">
					<label>
						__Title__
						<?php echo $this->inputText->render( 'title' ); ?>
					</label>
				</div>

				<div class="hc4-form-element">
					<label>
						__Slot Size__
					</label>
					<?php echo $this->inputDuration->render( 'slot_size', 30 * 60 ); ?>
				</div>
			</div>

			<div class="hc-col hc-col-4 hc-px2">

				<div class="hc4-form-element">
					<label>
						__Access__
					</label>
					<?php echo $this->inputMultiSet->render( 'access', $accessLabels, $accessOptions, 'public' ); ?>
				</div>

				<div class="hc4-form-element">
					<label>
						__Initial Status__
					</label>
					<?php echo $this->inputRadioSet->renderInline( 'initial_status', $statusOptions, 'pending' ); ?>
				</div>

			</div>
		</div>

	</div>

	<div class="hc4-form-buttons">
		<button class="hc4-admin-btn-primary">__Save__</button>
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function renderAccessPublic()
	{
		ob_start();
?>
<div class="hc4-form-element">
	<label>
		__Min Advance Booking__
		<?php echo $this->inputDuration2->render( 'min_from_now', '3 hours' ); ?>
	</label>
</div>

<div class="hc4-form-element">
	<label>
		__Max Advance Booking__
		<?php echo $this->inputDuration2->render( 'max_from_now', '8 weeks' ); ?>
	</label>
</div>

<?php 
		return ob_get_clean();
	}

	public function renderAccessPrivate()
	{
		ob_start();
?>
<div class="hc-italic">
__Private calendars are not available for booking by customers.__
</div>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, array $post )
	{
	// VALIDATE POST
		$values = array(
			'title'			=> $post['title'],
			// 'description'	=> $post['description'],
			'slot_size'		=> $this->inputDuration->grab( 'slot_size', $post ),
			'access'			=> $post['access'],
			'min_from_now'	=> $this->inputDuration2->grab( 'min_from_now', $post ),
			'max_from_now'	=> $this->inputDuration2->grab( 'max_from_now', $post ),
			'initial_status'	=> $post['initial_status'],
			);

		$errors = array();
		if( ! strlen($values['title']) ){
			$errors['title'] = '__Required Field__';
		}
		if( ! $values['slot_size'] ){
			$errors['slot_size'] = '__Required Field__';
		}
		if( $errors ){
			throw new HC4_App_Exception_FormErrors( $errors );
		}

	// DO
		try {
			$model = new JB7_11Calendars_Data_Model;
			$model->title = $values['title'];
			// $model->description = $values['description'];
			$model->slotSize = $values['slot_size'];
			$model->access = $values['access'];
			$model->minFromNow = $values['min_from_now'];
			$model->maxFromNow = $values['max_from_now'];
			$model->initialStatus = $values['initial_status'];

			$model = $this->repo->create( $model );
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