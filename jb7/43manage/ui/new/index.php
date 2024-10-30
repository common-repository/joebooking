<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_New_Index
{
	public function __construct(
		JB7_43Manage_Ui_Params $params,
		JB7_43Manage_Ui_New_Params $newParams,

		JB7_43Manage_Data_Repo $repo,

		JB7_11Calendars_Data_Repo $repoCalendars,
		JB7_11Calendars_Ui_Title $viewCalendar,

		JB7_31Bookings_Data_Fields_Repo $repoFields,

		JB7_31Bookings_Data_Repo $repoBookings,

		HC4_Html_Input_Text $inputText,
		HC4_Html_Input_Select $inputSelect,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,

		HC4_Html_Screen_Interface $screen
	)
	{}

	public function title( $paramString, $newParamString )
	{
		$return = array();

		$return[] = '__New Booking__';
		$newParams = $this->newParams->make( $newParamString );
		$calendar = $newParams->getCalendar();
		if( $calendar ){
			$return[] = $this->viewCalendar->render( $calendar );
		}
		$return = join( ' &middot; ', $return );

		return $return;
	}

	public function get( $slug, $paramString, $newParamString )
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

		$fields = $this->repoFields->findAll();

		$return = $this->render( $slots, $fields );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $slots, array $fields )
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

		<?php foreach( $fields as $f ) : ?>
			<div class="hc4-form-element">
				<label>
					<?php echo $f->label; ?>
				</label>
				<?php echo $f->renderEdit(); ?>
			</div>
		<?php endforeach; ?>

	</div>

	<div class="hc4-form-buttons">
		<input type="submit" class="hc4-admin-btn-primary" value="__Create New Booking__">
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, $post, $paramString, $newParamString )
	{
	// VALIDATE POST
		$errors = array();
		if( ! (isset($post['start']) && $post['start']) ){
			$errors['start'] = '__Required Field__';
		}

		if( $errors ){
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		$detailsArray = array();
		$fields = $this->repoFields->findAll();
		foreach( $fields as $f ){
			$detailsArray[ $f->name ] = $f->grab( $post );
		}

		list( $startDateTime, $endDateTime, $calendarId ) = explode( '-', $post['start'] );
		$calendar = $this->repoCalendars->findById( $calendarId );

		$duration = $calendar->slotSize;
		$startDateTimes = array( $startDateTime );

		$bookings = array();

		$count = 0;
		foreach( $startDateTimes as $startDateTime ){
			$endDateTime = $this->t->setDateTimeDb( $startDateTime )
				->modify( '+ ' . $duration . ' seconds' )
				->getDateTimeDb()
				;

		// DO
			try {
				$details = new JB7_31Bookings_Data_Model_Details;
				foreach( $detailsArray as $k => $v ){
					$details->{$k} = $v;
				}

				$model = new JB7_31Bookings_Data_Model;
				$model->startDateTime = $startDateTime;
				$model->endDateTime = $endDateTime;
				$model->calendar = $calendar;
				$model->details = $details;
				$model->status = $calendar->initialStatus;

				$model = $this->repoBookings->create( $model );
				$count++;
			}
			catch( HC4_App_Exception_DataError $e ){
				throw $e;
				// $to = '-referrer-';
				// $return = array( $to, NULL, $e->getMessage() );
				// return $return;
			}
		}

		$msg = NULL;
		if( $count ){
			$msg = '__Booking Saved__';
			if( $count > 1 ){
				$msg .= ' (' . $count . ')';
			}
		}

		$slugArray = explode( '/', $slug );
		$to = implode( '/', array_slice($slugArray, 0, -2) );
		$return = array( $to, $msg );

		return $return;
	}
}