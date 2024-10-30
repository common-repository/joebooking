 <?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Bookings_Id
{
	public function __construct(
		JB7_11Calendars_Ui_Title $viewCalendar,
		JB7_31Bookings_Ui_Details $viewDetails,

		JB7_43Manage_Ui_Params $params,

		JB7_31Bookings_Data_Repo $repo,

		JB7_31Bookings_Ui_Title $viewTitle,
		JB7_31Bookings_Ui_Date $viewDate,
		JB7_31Bookings_Ui_Time $viewTime,
		JB7_31Bookings_Ui_Status $viewStatus,
		JB7_31Bookings_Ui_Refno $viewRefno,
		JB7_31Bookings_Ui_PaymentStatus $viewPaymentStatus,

		HC4_Html_Widget_Icons $widgetIcons,
		HC4_Time_Interface $t,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function title( $id )
	{
		$model = $this->repo->findById( $id );
		$return = $this->viewTitle->render( $model );
		return $return;
	}

	public function menu( $paramString, $id )
	{
		$return = array();
		$model = $this->repo->findById( $id );

	// RESCHEDULE
		// $params = $this->params->make( $paramString );
		// $today = $this->t->setDateTimeDb( $model->startDateTime )
			// ->getDateDb()
			// ;
		// $params
			// ->grouping( JB7_43Manage_Ui_Params::GROUPING_NONE )
			// ->date( $today )
			// ->calendarId( $model->calendar->id )
			// ;
		// $toParams = $params->makeString();
		// $return[] = array( '{CURRENT}/reschedule/' . $toParams, '__Reschedule__' );

		return $return;
	}

	public function get( $slug, $id )
	{
		$model = $this->repo->findById( $id );

		$return = $this->render( $model );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render(
		JB7_31Bookings_Data_Model $model
	)
	{
		$conflicts = array();
		$alternativeCalendars = array();

		ob_start();
?>

<div class="hc-grid hc-mxn1">

	<div class="hc-col hc-col-3 hc-px1">
		<div class="hc4-form-element">
			<label>
				__Date__
			</label>
			<div class="hc-p2 hc-border hc-border-gray hc-rounded">
				<?php echo $this->viewDate->render( $model ); ?>
			</div>
		</div>
	</div>

	<div class="hc-col hc-col-3 hc-px1">
		<div class="hc4-form-element">
			<label>
			<?php if( $conflicts ) : ?>
				<?php echo $this->widgetIcons->renderAlert(); ?>
			<?php endif; ?>
				__Time__
			</label>
			<div class="hc-p2 hc-border hc-border-gray hc-rounded">
				<?php echo $this->viewTime->render( $model ); ?>
			</div>
		</div>
	</div>

	<div class="hc-col hc-col-3 hc-px1">
		<div class="hc4-form-element">
			<label>
				__Calendar__
			</label>
			<div class="hc-p2 hc-border hc-border-gray hc-rounded">
				<?php
				$label = $this->viewCalendar->render( $model->calendar );
				?>
				<?php if( $alternativeCalendars ): ?>
					<a href="HREFGET:{CURRENT}/term/<?php echo $term->category->id; ?>"><?php echo $label; ?></a>
				<?php else : ?>
					<?php echo $label; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="hc-col hc-col-3 hc-px1">
		<div class="hc4-form-element">
			<label>
				__Ref No__ & __Status__
			</label>
			<div class="hc-p2 hc-border hc-border-gray hc-rounded">
				<?php echo $this->viewRefno->render($model->refno); ?> <?php echo $this->viewStatus->render( $model->status ); ?>
			</div>
		</div>
	</div>

</div>

<div class="hc4-form-element">
	<?php echo $this->viewDetails->render( $model->details ); ?>
</div>

<?php if( $conflicts ) : ?>
	<div class="hc4-form-element">
		<label>
			__Conflicts__
		</label>
		<div class="hc-p2 hc-border hc-border-gray hc-rounded">
			<div class="hc4-admin-list-secondary">
			<?php foreach( $conflicts as $e ): ?>
				<div><?php echo get_class($e); ?></div>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<?php 
		return ob_get_clean();
	}
}