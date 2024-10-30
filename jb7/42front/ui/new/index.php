<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_42Front_Ui_New_Index
{
	public function __construct(
		JB7_42Front_Data_Repo $repo,

		JB7_11Calendars_Ui_Title $viewCalendar,

		HC4_Auth_Interface $auth,
		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug, $date = NULL )
	{
		if( NULL === $date ){
			$rexDateTime = $this->t->setNow()
				->getDateTimeDb()
				;
		}
		else {
			$rexDateTime = $this->t->setDateDb( $date )
				->getDateTimeDb()
				;
		}

		$slots = array();
		$nextSlot = $this->repo->findNextSlot( $rexDateTime );

		if( $nextSlot ){
			$startDateTime = $nextSlot->startDateTime;
			$endDateTime = $this->t->setDateTimeDb( $startDateTime )
				->setEndDay()
				->getDateTimeDb()
				;
			$slots = $this->repo->findSlots( $startDateTime, $endDateTime );
			$nextSlot = $this->repo->findNextSlot( $endDateTime );
		}

		$return = $this->render( $slots, $nextSlot );
		$return = $this->screen->render( $slug, $return );

		return $return;
	}

	public function render( array $slots, $nextSlot )
	{
		$now = $this->t->setNow()->getDateTimeDb();
		$currentTimeView = array();
		$currentTimeView[] = $this->tf->formatDateWithWeekday( $now );
		$currentTimeView[] = $this->tf->formatTime( $now );
		$currentTimeView = join( ' ', $currentTimeView );

		$calendars = array();
		reset( $slots );
		foreach( $slots as $slot ){
			$calendars[ $slot->calendar->id ] = $slot->calendar;
		}

		if( $slots ){
			$dateView = $this->tf->formatDateWithWeekday( $slots[0]->startDateTime );
		}

		$myId = 'jb7-front-times-' . HC4_App_Functions::generateRand(2);

		ob_start();
?>

<?php if( 0 ) : ?>
<div class="hc-block hc-muted2 hc-px2"><?php echo $currentTimeView; ?></div>
<?php endif; ?>

<?php if( $slots ) : ?>
	<div class="hc-block hc-p2 hc-my2">
		<strong><?php echo $dateView; ?></strong>
	</div>

		<?php foreach( $calendars as $calendar ) : ?>
			<?php
			$calendarSlots = array_filter( $slots, function($e) use ($calendar){
				return ($e->calendar->id == $calendar->id);
			});
			?>
			<div class="hc-block hc-p1 hc-my1">
				<?php echo $this->viewCalendar->render( $calendar ); ?>
				<div class="hc-grid hc-mxn1">
					<?php foreach( $calendarSlots as $slot ) : ?>
						<?php $timeView = $this->tf->formatTime( $slot->startDateTime ); ?>
						<div class="hc-col hc-col-2 hc-px1">
							<a class="jb7-front-link-time hc-block hc-p2 hc-m1 hc-border hc-ronded hc-align-center hc-nowrap" href="HREFGET:front/new/confirm/<?php echo $slot->startDateTime; ?>-<?php echo $slot->calendar->id; ?>" data-ajax="1"><?php echo $timeView; ?></a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

<?php else : ?>
	__Not Available__
<?php endif; ?>

<?php if( $nextSlot ) : ?>
	<?php
	$nextLabel = $this->tf->formatDateWithWeekday( $nextSlot->startDateTime );
	$nextDate = $this->t->setDateTimeDb( $nextSlot->startDateTime )
		->getDateDb()
		;
	?>
	<div>
		<a class="jb7-front-link-date hc-block hc-p2 hc-my2" data-ajax="1" href="HREFGET:front/new/date/<?php echo $nextDate; ?>"><?php echo $nextLabel; ?> &rarr;</a>
	</div>
<?php endif; ?>

<?php 
		return ob_get_clean();
	}

	public function menu()
	{
		$return = array();

		$calendars = $this->repo->getCalendars();
		if( $calendars ){
			$return[] = array( 'front/new', '__New Booking__' );
		}

		return $return;
	}
}