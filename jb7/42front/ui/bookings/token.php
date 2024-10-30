 <?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_42Front_Ui_Bookings_Token
{
	public function __construct(
		JB7_11Calendars_Ui_Title $viewCalendar,

		JB7_31Bookings_Data_Repo $repo,

		JB7_31Bookings_Ui_Title $viewTitle,
		JB7_31Bookings_Ui_Date $viewDate,
		JB7_31Bookings_Ui_Time $viewTime,
		JB7_31Bookings_Ui_Status $viewStatus,
		JB7_31Bookings_Ui_Refno $viewRefno,
		JB7_31Bookings_Ui_Details $viewDetails,

		HC4_Html_Widget_Icons $widgetIcons,
		HC4_Time_Interface $t,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function title( $token )
	{
		$model = $this->repo->findByToken( $token );
		$return = $this->viewTitle->render( $model );
		return $return;
	}

	public function menu( $token )
	{
		$return = array();
		$model = $this->repo->findByToken( $token );

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

	public function get( $slug, $token )
	{
		$model = $this->repo->findByToken( $token );

		$return = $this->render( $model );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( JB7_31Bookings_Data_Model $model )
	{
		$conflicts = array();

		ob_start();
?>

<div class="hc-grid hc-mxn1">

	<div class="hc-col hc-col-6 hc-px1">
		<div class="hc4-form-element">
			<label>
				__Date__
			</label>
			<div class="hc-p2 hc-border hc-border-gray hc-rounded">
				<?php echo $this->viewDate->render( $model ); ?>
			</div>
		</div>
	</div>

	<div class="hc-col hc-col-6 hc-px1">
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
</div>

<div class="hc-grid hc-mxn1">
	<div class="hc-col hc-col-6 hc-px1">
		<div class="hc4-form-element">
			<label>
				__Calendar__
			</label>
			<div class="hc-p2 hc-border hc-border-gray hc-rounded">
				<?php echo $this->viewCalendar->render( $model->calendar ); ?>
			</div>
		</div>
	</div>

	<div class="hc-col hc-col-6 hc-px1">
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