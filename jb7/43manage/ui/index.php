<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Index
{
	public function __construct(
		HC4_Auth_Interface $auth,

		JB7_43Manage_Ui_Params $params,
		JB7_43Manage_Data_Repo $repo,

		JB7_11Calendars_Ui_Title $viewCalendar,

		HC4_Time_Interface $t,
		HC4_Html_Input_RadioSet $inputRadioSet,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug )
	{
		$params = $this->params->make();
		$calendars = $this->repo->getCalendars();

		$return = $this->render( $calendars );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $calendars )
	{
		$groupingOptions = $this->params->getGroupingOptions();
		$presentationOptions = $this->params->getPresentationOptions();
		$rangeOptions = $this->params->getRangeOptions();

		ob_start();
?>

<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-form-elements">

		<?php if( count($rangeOptions) > 1 ) : ?>
			<div class="hc4-form-element">
				<label>__Range__</label>
				<?php echo $this->inputRadioSet->renderInline( 'range', $rangeOptions, current(array_keys($rangeOptions)) ); ?>
			</div>
		<?php endif; ?>

		<?php if( count($presentationOptions) > 1 ) : ?>
			<div class="hc4-form-element">
				<label>__Presentation__</label>
				<?php echo $this->inputRadioSet->renderInline( 'presentation', $presentationOptions, current(array_keys($presentationOptions)) ); ?>
			</div>
		<?php endif; ?>

		<?php if( count($calendars) > 1 ) : ?>
			<div class="hc4-form-element">
				<label>__Group By__</label>
				<?php echo $this->inputRadioSet->renderInline( 'grouping', $groupingOptions, current(array_keys($groupingOptions)) ); ?>
			</div>
		<?php endif; ?>

	</div>

	<div class="hc4-form-buttons">
		<input type="submit" class="hc4-admin-btn-primary" value="__Continue__">
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function menu()
	{
		$return = array();

	// FIND CALENDARS THAT I CAN MANAGE
		$currentUserId = $this->auth->getCurrentUserId();
		if( ! $currentUserId ){
			return $return;
		}

$return[] = array( 'manage', '__Bookings__' );
return $return;

		return $return;
	}

	public function post( $slug, $post )
	{
		$range = isset($post['range']) ? $post['range'] : NULL;
		$presentation = isset($post['presentation']) ? $post['presentation'] : NULL;
		$grouping = isset( $post['grouping'] ) ? $post['grouping'] : NULL;
		$date = $this->t->setNow()->getDateDb();

		$params = $this->params->make();
		$params
			->date( $date )
			;

		if( $grouping ){
			$params->grouping( $grouping );
		}

		if( $range ){
			$params->range( $range );
		}
		if( $presentation ){
			$presentation->range( $range );
		}

		$termIds = array();
		foreach( array_keys($post) as $k ){
			if( ('term_' == substr($k, 0, strlen('term_'))) && $post[$k] ){
				$termIds[] = $post[$k];
			}
		}

		if( $termIds ){
			$params->termIds( $termIds );
		}

		$paramString = $params->makeString();
		$to = $slug . '/' . $paramString;

		$return = array( $to, NULL );
		return $return;
	}
}