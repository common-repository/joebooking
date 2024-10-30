<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Bookings_Id_Reschedule
{
	public function __construct(
		JB7_31Bookings_Data_Repo $repoBookings,

		JB7_43Manage_Data_Repo $repo,
		JB7_43Manage_Ui_Params $params,

	// subviews
		JB7_43Manage_Ui_Schedule_Cell $viewCell,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function title( $paramString )
	{
		$params = $this->params->make( $paramString );
		$return = $params->getTitle();
		return $return;
	}

	public function menu( $pString1, $id, $paramString )
	{
		$return = array();

		$model = $this->repoBookings->findById( $id );

	// PREV
		$params = $this->params->make( $paramString );
		$prevStartDate = $params->getPrevDate();
		if( $prevStartDate ){
			$params->date( $prevStartDate );

			$toPrev = $params->makeString();
			$toPrev = 'manage/' . $pString1 . '/' . $id . '/reschedule/' . $toPrev;
			$labelPrev = $params->getTitleDates();
			$labelPrev = '&larr; ' . $labelPrev;
			$return[] = array( $toPrev, $labelPrev );
		}

	// NEXT
		$params = $this->params->make( $paramString );
		$nextStartDate = $params->getNextDate();
		if( $nextStartDate ){
			$params->date( $nextStartDate );

			$toNext = $params->makeString();
			$toNext = 'manage/' . $pString1 . '/' . $id . '/reschedule/' . $toNext;
			$labelNext = $params->getTitleDates();
			$labelNext = $labelNext . ' &rarr;';
			$return[] = array( $toNext, $labelNext );
		}

		return $return;
	}

	public function get( $slug, $id, $paramString = NULL )
	{
		$model = $this->repoBookings->findById( $id );

		$today = $this->t->setDateTimeDb( $model->startDateTime )
			->getDateDb()
			;

		$params = $this->params->make( $paramString );
		$params
			->calendarId( $model->calendar->id )
			;

		$dates = $params->getDates();
		if( ! $dates ){
			$return = '__No bookings or availability__';
			$return = $this->screen->render( $slug, $return );
			return $return;
		}

		$startDate = $dates[0];
		$endDate = $dates[ count($dates) - 1 ];

		$startDateTime = $this->t->setDateDb( $startDate )->getDateTimeDb();
		$endDateTime = $this->t->setDateDb( $endDate )->modify('+1 day')->getDateTimeDb();

		$bookings = $this->repoBookings->find( $startDateTime, $endDateTime );
		$bookings = array_filter( $bookings, function($e) use ($model) {
			if( $model->id == $e->id ){
				return FALSE;
			}
			return ($e->calendar->id == $model->calendar->id);
		});

		$slots = $this->repo->findSlots( $startDateTime, $endDateTime );
		$slots = array_filter( $slots, function($e) use ($model) {
			return ($e->calendar->id == $model->calendar->id);
		});

		$return = $this->render( $dates, $bookings, $slots, $today );

		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $dates, array $bookings, array $slots, $today = NULL )
	{
	// DATE LABELS
		$dateLabels = array();
		foreach( $dates as $date ){
			$this->t->setDateDb( $date );
			$dateView = $this->tf->formatWeekday( $this->t->getWeekDay() ) . "<br/>" . $this->tf->formatMonthName( $this->t->getMonth() ) . ' ' . $this->t->getDay();
			$dateLabels[ $date ] = $dateView;
		} 

	// ROWS
		$rows = array();
		$cellKnows = array();
		$cellKnows['date'] = 'date';
		$cellKnows['calendar'] = 'calendar';

		$rows[0] = array();
		reset( $dates );
		foreach( $dates as $date ){
			$cell = array();
			$cell['bookings'] = array();
			$cell['slots'] = array();

			$rows[0][ $date ] = $cell;
		}

	// ARRANGE BOOKINGS
		foreach( $bookings as $booking ){
			$date = $this->t->setDateTimeDb( $booking->startDateTime )->getDateDb();
			$rowId = 0;
			if( isset($rows[$rowId]) && isset($rows[$rowId][$date]) ){
				$rows[$rowId][$date]['bookings'][] = $booking;
			}
		}

	// ARRANGE SLOTS
		foreach( $slots as $slot ){
			$date = $this->t->setDateTimeDb( $slot->startDateTime )->getDateDb();
			$rowId = 0;
			if( isset($rows[$rowId]) && isset($rows[$rowId][$date]) ){
				$rows[$rowId][$date]['slots'][] = $slot;
			}
		}

		ob_start();
?>

<table class="hc-table-bordered">

<?php if( count($dateLabels) > 1 ) : ?>
	<tr class="hc-align-center hc-fs2">
		<?php foreach( $dateLabels as $date => $label ) : ?>
			<?php
			$cellClass = ( $today == $date ) ? 'hc-white hc-bg-gray' : '';
			?>
			<td class="<?php echo $cellClass; ?>">
				<?php echo $label; ?>
			</td>
		<?php endforeach; ?>
	</tr>
<?php endif; ?>

<?php foreach( $rows as $rowId => $row ) : ?>
	<tr>
		<?php foreach( $dates as $date ) : ?>
			<td>
				<?php echo $this->viewCell->render( $row[$date], $cellKnows ); ?>
			</td>
		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>

</table>

<?php 
		$return = ob_get_clean();
		return $return;
	}
}