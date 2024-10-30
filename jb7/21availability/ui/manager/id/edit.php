<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Manager_Id_Edit
{
	public function __construct(
		JB7_11Calendars_Ui_Title $viewCalendar,

		JB7_21Availability_Data_Repo $repo,
		JB7_21Availability_Ui_Priority $viewPriority,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,

		HC4_Html_Input_Text $inputText,
		HC4_Html_Input_RadioSet $inputRadioSet,
		HC4_Html_Input_Select $inputSelect,
		HC4_Html_Input_Date $inputDate,
		HC4_Html_Input_MultiSet $inputMultiSet,
		HC4_Html_Input_CheckboxSet $inputCheckboxSet,
		HC4_Html_Input_Time $inputTime,
		HC4_Html_Input_Duration $inputDuration,

		HC4_Html_Screen_Interface $screen
	)
	{}

	public function title( $id )
	{
		$model = $this->repo->findById( $id );
		$return = $this->viewCalendar->render( $model->calendar );
		return $return;
	}

	public function get( $slug, $id )
	{
		$model = $this->repo->findById( $id );

		$return = $this->render( $model );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( JB7_21Availability_Data_Model $model )
	{
		$appliedOnLabels = array();
		$appliedOnOptions = array();

		$appliedOnLabels['everyday'] = '__Every Day__';

		$appliedOnLabels['daysofweek'] = '__Days Of Week__';
		$daysOfWeekOptions = $this->t->getWeekdays();
		$default = $model->appliedOnDetails ? $model->appliedOnDetails : array(0,1,2,3,4,5,6);
		$appliedOnOptions['daysofweek'] = $this->inputCheckboxSet
			->renderInline( 'applied_on_daysofweek', $daysOfWeekOptions, $default )
			;

		$defaultAppliedOn = $model->appliedOn;

		$validFromLabels = array();
		$validFromOptions = array();
		$validFromLabels['always'] = '__No Limit__';
		$validFromLabels['from'] = '__From Date__';
		$default = $model->validFromDate ? $model->validFromDate : $this->t->setNow()->getDateDb();; 
		$validFromOptions['from'] = $this->inputDate->render( 'valid_from_date', $default );

		$defaultValidForm = $model->validFromDate ? 'from' : 'always';

		$validToLabels = array();
		$validToOptions = array();
		$validToLabels['always'] = '__No Limit__';
		$validToLabels['to'] = '__To Date__';
		// $default = $model->validToDate ? $model->validToDate : $this->t->setNow()->modify('+1 year')->setEndYear()->getDateDb();
		$default = $model->validToDate ? $model->validToDate : $this->t->setNow()->getDateDb();
		$validToOptions['to'] = $this->inputDate->render( 'valid_to_date', $default );

		$defaultValidTo = $model->validToDate ? 'to' : 'always';

		$priorityOptions = array(
			3	=> $this->viewPriority->render(3),
			2	=> $this->viewPriority->render(2),
			1	=>  $this->viewPriority->render(1),
			);

		ob_start();
?>
<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-form-elements">

		<div class="hc-grid">
			<div class="hc-col hc-col-4">
				<div class="hc4-form-element">
					<label>
						__Start Time__
					</label>
					<?php echo $this->inputTime->render( 'start_time', $model->fromTime ); ?>
				</div>
			</div>

			<div class="hc-col hc-col-4">
				<div class="hc4-form-element">
					<label>
						__End Time__
					</label>
					<?php echo $this->inputTime->render( 'end_time', $model->toTime ); ?>
				</div>
			</div>

			<div class="hc-col hc-col-4">
				<div class="hc4-form-element">
					<label>
						__Interval__
					</label>
					<?php echo $this->inputDuration->render( 'interval', $model->interval ); ?>
				</div>
			</div>
		</div>

		<div class="hc4-form-element">
			<label>
				__Applied On__
			</label>
			<?php echo $this->inputMultiSet->render( 'applied_on', $appliedOnLabels, $appliedOnOptions, $defaultAppliedOn ); ?>
		</div>


		<div class="hc-grid">
			<div class="hc-col hc-col-4">
				<div class="hc4-form-element">
					<label>
						__Valid From__
					</label>
					<?php echo $this->inputMultiSet->render( 'valid_from', $validFromLabels, $validFromOptions, $defaultValidForm ); ?>
				</div>
			</div>

			<div class="hc-col hc-col-4">
				<div class="hc4-form-element">
					<label>
						__Valid To__
					</label>
					<?php echo $this->inputMultiSet->render( 'valid_to', $validToLabels, $validToOptions, $defaultValidTo ); ?>
				</div>
			</div>
		</div>

		<div class="hc4-form-element">
			<label>
				__Priority__
			</label>
			<?php echo $this->inputRadioSet->render( 'priority', $priorityOptions, $model->priority ); ?>
			<em>
			__If there are multiple availabilities for a day, the one with the highest priority is taken. If there are several options with the same highest priority, they are combined together.__
			</em>
		</div>
	</div>

	<div class="hc4-form-buttons">
		<button class="hc4-admin-btn-primary">__Save__</button>
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, array $post, $id )
	{
		$model = $this->repo->findById( $id );

		$errors = array();

		if( ! (isset($post['applied_on']) && $post['applied_on']) ){
			$errors['applied_on'] = '__Required Field__';
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		$appliedOn = $post['applied_on'];
		$appliedOnDetails = NULL;

		switch( $appliedOn ){
			case 'everyday':
				break;

			case 'daysofweek':
				$daysOfWeek = isset($post['applied_on_daysofweek']) ? $post['applied_on_daysofweek'] : array();
				if( ! $daysOfWeek ){
					$errors['applied_on_daysofweek'] = '__Required Field__';
					throw new HC4_App_Exception_FormErrors( $errors );
				}
				$appliedOnDetails = $daysOfWeek;
				break;
		}

		$validFromDate = NULL;
		if( isset($post['valid_from']) && ('from' == $post['valid_from']) ){
			$validFromDate = $this->inputDate->grab( 'valid_from_date', $post );
		}

		$validToDate = NULL;
		if( isset($post['valid_to']) && ('to' == $post['valid_to']) ){
			$validToDate = $this->inputDate->grab( 'valid_to_date', $post );
		}

		$startTime = $this->inputTime->grab( 'start_time', $post );
		$endTime = $this->inputTime->grab( 'end_time', $post );
		if( $endTime <= $startTime ){
			$endTime = 24*60*60 + $endTime;
		}

		$interval = $this->inputDuration->grab( 'interval', $post );

		$calendar = $model->calendar;

		$values = array(
			'calendar'	=> $calendar,
			'from_time'	=> $startTime,
			'to_time'	=> $endTime,
			'interval'	=> $interval,
			'priority'	=> $post['priority'],
			);

		if( NULL !== $validFromDate ){
			$values['valid_from_date'] = $validFromDate;
		}

		if( NULL !== $validToDate ){
			$values['valid_to_date'] = $validToDate;
		}

		if( $validFromDate && ($validFromDate == $validToDate) ){
			$appliedOn = 'everyday';
			$appliedOnDetails = NULL;
		}

		$values['applied_on'] = $appliedOn;
		if( NULL !== $appliedOnDetails ){
			$values['applied_on_details'] = $appliedOnDetails;
		}

	// DO
		$values['id'] = $id;

		try {
			$model = JB7_21Availability_Data_Model::fromArray( $values );
			$model = $this->repo->update( $model );
		}
		catch( HC4_App_Exception_DataError $e ){
			$to = '-referrer-';
			$return = array( $to, NULL, $e->getMessage() );
			return $return;
		}

		$slugArray = explode( '/', $slug );
		$to = implode( '/', array_slice($slugArray, 0, -1) );
		$return = array( $to, '__Availability Saved__' );

		return $return;
	}
}