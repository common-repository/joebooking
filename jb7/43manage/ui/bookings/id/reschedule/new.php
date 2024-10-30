<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Bookings_Id_Reschedule_New
{
	public function __construct(
		JB7_43Manage_Ui_Params $params,
		JB7_43Manage_Ui_New_Params $newParams,

		JB7_43Manage_Data_Repo $repo,

		JB7_11Calendars_Data_Repo $repoCalendars,
		JB7_11Calendars_Ui_Title $viewCalendar,
		JB7_31Bookings_Ui_Title $viewBooking,

		JB7_31Bookings_Data_Repo $repoBookings,

		HC4_Html_Input_Select $inputSelect,
		HC4_Html_Input_Hidden $inputHidden,
		HC4_Html_Input_MultiSet $inputMultiSet,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,

		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug, $id, $newParamString )
	{
		$newParams = $this->newParams->make( $newParamString );

		$startDateTime = $newParams->getStart();
		$endDateTime = $newParams->getEnd();

		$slots = $this->repo
			->findSlots( $startDateTime, $endDateTime )
			;

		$calendar = $newParams->getCalendar();
		if( $calendar ){
			$slots = array_filter( $slots, function($e) use ($calendar) {
				return ($e->calendar->id == $calendar->id);
			});
		}

		$return = $this->render( $slots );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $slots )
	{
		$fistSlot = current( $slots );
		$dateView = $this->tf->formatDateWithWeekday( $fistSlot->startDateTime );

		$calendars = array();
		reset( $slots );
		foreach( $slots as $slot ){
			$calendars[ $slot->calendar->id ] = $slot->calendar;
		}

		$timeOptions = array();
		foreach( $slots as $slot ){
			$optionValue = $slot->startDateTime . '-' . $slot->endDateTime . '-' . $slot->calendar->id;
			$optionLabel = $this->tf->formatTime( $slot->startDateTime ) . ' - ' . $this->tf->formatTime( $slot->endDateTime );

			if( count($calendars) > 1 ){
				$calendarLabel = $this->viewCalendar->render( $slot->calendar );
				$optionLabel .= ' ' . $calendarLabel;
			}

			$timeOptions[ $optionValue ] = $optionLabel;
		}

		ob_start();
?>

<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-form-elements">

		<div class="hc4-form-element">
			<label><?php echo $dateView; ?></label>
			<?php echo $this->inputSelect->render( 'start', $timeOptions ); ?>
		</div>

	</div>

	<div class="hc4-form-buttons">
		<input type="submit" class="hc4-admin-btn-primary" value="__Confirm Reschedule__">
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, $post, $id, $newParamString )
	{
	// VALIDATE POST
		$errors = array();
		if( ! (isset($post['start']) && $post['start']) ){
			$errors['start'] = '__Required Field__';
		}

		if( $errors ){
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		$model = $this->repoBookings->findById( $id );
		$values = $model->toArray();

		list( $startDateTime, $endDateTime, $calendarId ) = explode( '-', $post['start'] );
		$calendar = $this->repoCalendars->findById( $calendarId );

		$duration = $calendar->slotSize;
		$endDateTime = $this->t->setDateTimeDb( $startDateTime )
			->modify( '+ ' . $duration . ' seconds' )
			->getDateTimeDb()
			;

		$values['start_datetime'] = $startDateTime;
		$values['end_datetime'] = $endDateTime;

		$bookings = array();
		$count = 0;

	// DO
		try {
			$model = JB7_31Bookings_Data_Model::fromArray( $values );
			$model = $this->repoBookings->update( $model );
			$count++;
		}
		catch( HC4_App_Exception_DataError $e ){
			throw $e;
			// $to = '-referrer-';
			// $return = array( $to, NULL, $e->getMessage() );
			// return $return;
		}

		$msg = NULL;
		if( $count ){
			$msg = '__Booking Saved__';
			if( $count > 1 ){
				$msg .= ' (' . $count . ')';
			}
		}

		$slugArray = explode( '/', $slug );

		$originalParamString = $slugArray[1];
		$originalParams = $this->params->make( $originalParamString );

		$originalDates = $originalParams->getDates();
		$newDate = $this->t->setDateTimeDb( $startDateTime )
			->getDateDb()
			;

		if( ! in_array($newDate, $originalDates) ){
			$originalParams->date( $newDate );
			$originalParamString = $originalParams->makeString();
			$slugArray[1] = $originalParamString;
		};

		$to = implode( '/', array_slice($slugArray, 0, -5) );
		$return = array( $to, $msg );

		return $return;
	}

	public function getSwap( $slug, $id, $newId )
	{
		$model = $this->repoBookings->findById( $id );
		$newModel = $this->repoBookings->findById( $newId );

		$return = $this->renderSwap( $model, $newModel );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function postSwap( $slug, $post, $id, $newId )
	{
		$return = '';

		$model = $this->repoBookings->findById( $id );
		$newModel = $this->repoBookings->findById( $newId );

		$values = $model->toArray();
		$values['start_datetime'] = $newModel->startDateTime;
		$values['end_datetime'] = $newModel->endDateTime;

		$newValues = $newModel->toArray();
		$newValues['start_datetime'] = $model->startDateTime;
		$newValues['end_datetime'] = $model->endDateTime;

		$count = 0;

	// DO
		try {
			$model = JB7_31Bookings_Data_Model::fromArray( $values );
			$model = $this->repoBookings->update( $model );
			$count++;

			$newModel = JB7_31Bookings_Data_Model::fromArray( $newValues );
			$newModel = $this->repoBookings->update( $newModel );
			$count++;
		}
		catch( HC4_App_Exception_DataError $e ){
			throw $e;
		}

		$msg = NULL;
		if( $count ){
			$msg = '__Booking Saved__';
			if( $count > 1 ){
				$msg .= ' (' . $count . ')';
			}
		}

		$slugArray = explode( '/', $slug );

		$originalParamString = $slugArray[1];
		$originalParams = $this->params->make( $originalParamString );

		$originalDates = $originalParams->getDates();
		$newDate = $this->t->setDateTimeDb( $model->startDateTime )
			->getDateDb()
			;

		if( ! in_array($newDate, $originalDates) ){
			$originalParams->date( $newDate );
			$originalParamString = $originalParams->makeString();
			$slugArray[1] = $originalParamString;
		};

		$to = implode( '/', array_slice($slugArray, 0, -4) );
		$return = array( $to, $msg );

		return $return;
	}

	public function renderSwap( JB7_31Bookings_Data_Model $model, JB7_31Bookings_Data_Model $newModel )
	{
		$modelTitle = $this->viewBooking->render( $model );
		$newModelTitle = $this->viewBooking->render( $newModel );

		ob_start();
?>

<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-form-elements">

		<div class="hc4-form-element">
			<?php echo $modelTitle; ?>
		</div>

		<div class="hc4-form-element">
			&udarr;
		</div>

		<div class="hc4-form-element">
			<?php echo $newModelTitle; ?>
		</div>

	</div>

	<div class="hc4-form-buttons">
		<input type="submit" class="hc4-admin-btn-primary" value="__Confirm Swap__">
	</div>

</form>

<?php 
		return ob_get_clean();
	}
}