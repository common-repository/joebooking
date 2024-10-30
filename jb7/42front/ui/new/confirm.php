<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_42Front_Ui_New_Confirm
{
	public function __construct(
		JB7_42Front_Data_Repo $repo,

		JB7_11Calendars_Ui_Title $viewCalendar,

		JB7_31Bookings_Data_Repo $repoBookings,
		JB7_31Bookings_Data_Fields_Repo $repoFields,

		HC4_Html_Input_Text $inputText,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug, $startDateTime )
	{
		$startDateTimeArray = explode( '-', $startDateTime );
		$startDateTime = array_shift( $startDateTimeArray );
		$calendarId = $startDateTimeArray ? array_shift( $startDateTimeArray ) : NULL;

		$endDateTime = $this->t->setDateTimeDb( $startDateTime )
			->modify('+5 minutes')
			->getDateTimeDb()
			;

		$slots = $this->repo->findSlots( $startDateTime, $endDateTime );

		if( $calendarId ){
			$slots = array_filter( $slots, function($e) use ($calendarId){
				return ($calendarId == $e->calendar->id);
			});
		}

		if( ! $slots ){
			$msg = '__No Available Slots__';
			$return = array( 'front', NULL, $msg );
			return $return;
		}

		$slot = array_shift( $slots );

		$fields = $this->repoFields->findAll();
		$fields = array_filter( $fields, function($e){
			return ('viewedit' == $e->customerAccess);
		});

		$return = $this->render( $slot, $fields );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( JB7_41Schedule_Data_Model_Slot $slot, array $fields )
	{
		ob_start();
?>

<form method="post" action="HREFPOST:{CURRENT}" data-ajax="1">

	<div class="hc4-form-elements">

		<div class="hc4-form-element">
			<div><?php echo $this->tf->formatDateWithWeekday( $slot->startDateTime ); ?></div>
			<div><?php echo $this->tf->formatTime( $slot->startDateTime ); ?> - <?php echo $this->tf->formatTime( $slot->endDateTime ); ?></div>
			<div><?php echo $this->viewCalendar->render( $slot->calendar ); ?></div>
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
		<input type="submit" value="__Submit Booking__">
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, $post, $startDateTime )
	{
		$startDateTimeArray = explode( '-', $startDateTime );
		$startDateTime = array_shift( $startDateTimeArray );
		$calendarId = $startDateTimeArray ? array_shift( $startDateTimeArray ) : NULL;

		$endDateTime = $this->t->setDateTimeDb( $startDateTime )
			->modify('+5 minutes')
			->getDateTimeDb()
			;

		$slots = $this->repo->findSlots( $startDateTime, $endDateTime );

		if( $calendarId ){
			$slots = array_filter( $slots, function($e) use ($calendarId){
				return ($calendarId == $e->calendar->id);
			});
		}

		if( ! $slots ){
			$msg = '__No Available Slots__';
			throw new HC4_App_Exception_DataError( $msg );
		}

	// VALIDATE POST
		$errors = array();

		$fields = $this->repoFields->findAll();
		$fields = array_filter( $fields, function($e){
			return ('viewedit' == $e->customerAccess);
		});
		$detailsArray = array();
		foreach( $fields as $f ){
			$detailsArray[ $f->name ] = $f->grab( $post );
		}

		if( $errors ){
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		$slot = array_shift( $slots );

		$startDateTime = $slot->startDateTime;
		$calendar = $slot->calendar;
		$duration = $calendar->slotSize;
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
		}
		catch( HC4_App_Exception_DataError $e ){
			throw $e;
		}

		$token = $this->repoBookings->createToken( $model );
		$to = 'front/bookings/' . $token;

		$msg = '__Booking Saved__';

		$return = array( $to, $msg );
		$return = array( $to, NULL );

		return $return;
	}
}