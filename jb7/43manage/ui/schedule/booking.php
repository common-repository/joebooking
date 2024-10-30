<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Schedule_Booking
{
	public function __construct(
		JB7_11Calendars_Ui_Title $viewCalendar,

		JB7_31Bookings_Ui_Status $viewBookingStatus,
		JB7_31Bookings_Ui_PaymentStatus $viewBookingPaymentStatus,
		JB7_31Bookings_Ui_Details $viewDetails,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		HC4_Html_Href_Interface $href
	)
	{}

	public function render(
		JB7_31Bookings_Data_Model $model,
		array $iKnow = array()
		)
	{
		$id = $model->id;

		$conflicts = array();

		$startDateTime = $model->startDateTime;
		$endDateTime = $model->endDateTime;

		$startTimeView = $this->tf->formatTime( $startDateTime );
		$endTimeView = $this->tf->formatTime( $endDateTime );

		$color = $this->viewBookingStatus->getColor( $model->status );
		$borderColor = '';

		$bgClass = 'hc-bg-' . $color . ' hc-muted4';

		$borderClass = '';
		if( $borderColor ){
			$borderClass = 'hc-border-thick-left hc-border-' . $borderColor;
		}

		$bgClass2 = '';
		if( ! $model->lock ){
			$bgClass2 .= 'hc-bg-striped';
			$borderClass .= ' hc-border-dashed';
		}

		ob_start();
?>

<div class="hc-pos-relative hc-nowrap hc-border hc-p1 hc-my1 hc-align-left <?php echo $borderClass; ?>">
	<div class="hc-pos-absolute <?php echo $bgClass; ?>" style="top: 0; bottom: 0; left: 0; right: 0; z-index: -1;"></div>
	<?php if( $bgClass2 ) : ?>
		<div class="hc-pos-absolute <?php echo $bgClass2; ?>" style="top: 0; bottom: 0; left: 0; right: 0; z-index: -1;"></div>
	<?php endif; ?>

	<?php if( ! isset($iKnow['date']) ) : ?>
		<div>
		<?php $dateView = $this->tf->formatDateWithWeekday( $startDateTime );
		echo $dateView;
		?>
		</div>
	<?php endif; ?>

	<div>
		<?php if( $conflicts ) : ?>
			<?php echo $this->widgetIcons->renderAlert(); ?>
		<?php endif; ?>
		<a href="<?php echo $this->href->hrefGet('{CURRENT}/' . $id); ?>"><?php echo $startTimeView; ?> - <?php echo $endTimeView; ?></a>
	</div>

	<?php if( ! isset($iKnow['calendar']) ) : ?>
		<?php $label = $this->viewCalendar->render( $model->calendar ); ?>
		<div class="hc-fs2">
			<?php echo $label; ?>
		</div>
	<?php endif; ?>

	<?php $label = $this->viewDetails->renderOne( $model->details ); ?>
	<div class="hc-fs2">
		<?php echo $label; ?>
	</div>

</div>

<?php 
		return ob_get_clean();
	}
}