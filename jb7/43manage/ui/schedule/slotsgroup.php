<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Schedule_SlotsGroup
{
	public function __construct(
		JB7_43Manage_Ui_New_Params $newParams,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		HC4_Html_Href_Interface $href
	)
	{}

	public function render(
		JB7_41Schedule_Data_Model_SlotsGroup $model,
		array $iKnow = array()
		)
	{
		$slots = $model->slots;
		$calendars = array();
		foreach( $model->slots as $slot ){
			$calendars[ $slot->calendar->id ] = $slot->calendar;
		}

		$conflicts = array();

		$startTimeView = $this->tf->formatTime( $model->startDateTime );
		$endTimeView = $this->tf->formatTime( $model->endDateTime );

		$newParams = $this->newParams->make();

		$newParams
			->start( $model->startDateTime )
			->end( $model->endDateTime )
			;

		if( count($calendars) == 1 ){
			$calendar = current( $calendars );
			$newParams
				->calendarId( $calendar->id )
				;
		}

		$newParamsString = $newParams->makeString();

		$label = $startTimeView . ' - ' . $endTimeView;

		ob_start();
?>

<?php if( 1 ) : ?>
<div class="hc-pos-relative hc-nowrap hc-p1 hc-my1 hc-align-left hc-border hc-border-olive">
	<a title="<?php echo $label; ?>" href="<?php echo $this->href->hrefGet('{CURRENT}/new/' . $newParamsString); ?>"><?php echo $label; ?></a>
	<div class="hc-pos-absolute hc-p1 hc-bg-lightgreen" title="__Available Slots__" style="right: 0; top: 0;"><?php echo $model->count; ?></div>
</div>
<?php endif; ?>

<?php if( 0 ) : ?>
<a class="hc-block hc-nowrap hc-p1 hc-my1 hc-align-left hc-border hc-border-green hc-rounded" title="<?php echo $label; ?>" href="<?php echo $this->href->hrefGet('{CURRENT}/new/' . $newParamsString); ?>"><?php echo $label; ?></a>
<?php endif; ?>


<?php 
		return ob_get_clean();
	}
}