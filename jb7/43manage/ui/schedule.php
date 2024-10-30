<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Schedule
{
	public function __construct(
		JB7_31Bookings_Data_Repo $repoBookings,

		JB7_43Manage_Data_Repo $repo,
		JB7_43Manage_Ui_Params $params,

	// subviews
		JB7_43Manage_Ui_Schedule_Calendar $viewCalendar,
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

	public function menu( $paramString )
	{
		$return = array();

	// PREV
		$params = $this->params->make( $paramString );
		$prevStartDate = $params->getPrevDate();
		if( $prevStartDate ){
			$params->date( $prevStartDate );

			$toPrev = $params->makeString();
			$toPrev = 'manage/' . $toPrev;
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
			$toNext = 'manage/' . $toNext;
			$labelNext = $params->getTitleDates();
			$labelNext = $labelNext . ' &rarr;';
			$return[] = array( $toNext, $labelNext );
		}

		return $return;
	}

	public function get( $slug, $paramString )
	{
		$params = $this->params->make( $paramString );

		$dates = $params->getDates();
		if( ! $dates ){
			$return = '__No bookings or availability__';
			$return = $this->screen->render( $slug, $return );
			return $return;
		}

		$startDate = $dates[0];
		$endDate = $dates[ count($dates) - 1 ];

		$calendars = array();
		if( $params->isGroupingCalendar() ){
			$calendars = $this->repo->getCalendars();
		}

		$startDateTime = $this->t->setDateDb( $startDate )->getDateTimeDb();
		$endDateTime = $this->t->setDateDb( $endDate )->modify('+1 day')->getDateTimeDb();

		$bookings = $this->repoBookings->find( $startDateTime, $endDateTime );
		$slots = $this->repo->findSlots( $startDateTime, $endDateTime );

		$return = $this->render( $dates, $calendars, $bookings, $slots );

		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $dates, array $calendars, array $bookings, array $slots )
	{
		$today = $this->t->setNow()->getDateDb();

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

	// GROUP BY CALENDAR
		if( $calendars ){
			$cellKnows['calendar'] = 'calendar';
			foreach( $calendars as $calendar ){
				$rowId = $calendar->id;

				$rows[ $rowId ] = array();
				$rows[ $rowId ]['label'] = $calendar;

				reset( $dates );
				foreach( $dates as $date ){
					$cell = array();
					$cell['bookings'] = array();
					$cell['slots'] = array();

					$rows[ $rowId ][ $date ] = $cell;
				}
			}
		}
		else {
			$rows[0] = array();
			reset( $dates );
			foreach( $dates as $date ){
				$cell = array();
				$cell['bookings'] = array();
				$cell['slots'] = array();

				$rows[0][ $date ] = $cell;
			}
		}

	// ARRANGE BOOKINGS
		foreach( $bookings as $booking ){
			$date = $this->t->setDateTimeDb( $booking->startDateTime )->getDateDb();
			$rowId = 0;
			if( $calendars ){
				$rowId = $booking->calendar->id;
			}

			if( isset($rows[$rowId]) && isset($rows[$rowId][$date]) ){
				$rows[$rowId][$date]['bookings'][] = $booking;
			}
		}

	// ARRANGE SLOTS
		foreach( $slots as $slot ){
			$date = $this->t->setDateTimeDb( $slot->startDateTime )->getDateDb();
			$rowId = 0;
			if( $calendars ){
				$rowId = $slot->calendar->id;
			}

			if( isset($rows[$rowId]) && isset($rows[$rowId][$date]) ){
				$rows[$rowId][$date]['slots'][] = $slot;
			}
		}

		ob_start();
?>

<table class="hc-table-bordered">

<?php if( count($dateLabels) > 1 ) : ?>
	<tr class="hc-align-center hc-fs2">
		<?php if( count($calendars) ) : ?>
			<td>&nbsp;</td>
		<?php endif; ?>

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
		<?php if( count($calendars) ) : ?>
			<td class="hc-nowrap">
				<?php echo $this->viewCalendar->render( $row['label'] ); ?>
			</td>
		<?php endif; ?>

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