<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Manager_Index
{
	public function __construct(
		HC4_Auth_Interface $auth,

		JB7_21Availability_Data_Repo $repo,
		JB7_21Availability_Ui_Priority $viewPriority,
		JB7_21Availability_Ui_Title $viewTitle,
		JB7_21Availability_Ui_Manager_Loader $loader,

		JB7_11Calendars_Ui_Title $viewCalendar,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug )
	{
		$entries = $this->repo->findAll();
		$calendars = $this->loader->getManagedCalendars();

		$syncedFrom = array();

	// entries by calendar
		$entriesByCalendar = array();
		foreach( $calendars as $calendar ){
			$entriesByCalendar[ $calendar->id ] = array();

			$syncedFromId = $this->repo->getSync( $calendar );
			if( $syncedFromId ){
				$syncedFrom[ $calendar->id ] = $calendars[$syncedFromId];
			}
		}

		foreach( $entries as $e ){
			if( ! isset($entriesByCalendar[$e->calendar->id]) ){
				continue;
			}
			$entriesByCalendar[$e->calendar->id][] = $e;
		}

		$return = $this->render( $calendars, $entriesByCalendar, $syncedFrom );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $calendars, array $entriesByCalendar, array $syncedFrom )
	{
		ob_start();
?>

<div class="hc4-admin-list-primary">

<?php if( $entriesByCalendar ) : ?>
	<div class="hc-grid">
		<div class="hc-col hc-col-3">__Calendar__</div>
		<div class="hc-col hc-col-3">__Time__</div>
		<div class="hc-col hc-col-6">__Details__</div>
	</div>

<?php endif; ?>

<?php foreach( $calendars as $calendar ) : ?>
	<div class="hc-grid">
		<div class="hc-col hc-col-3">
			<div class="hc4-admin-list-secondary">
				<div>
					<a class="hc4-admin-title-link hc-xs-block" href="HREFGET:{CURRENT}/<?php echo $calendar->id; ?>">
						<?php echo $this->viewCalendar->render( $calendar ); ?> (<?php echo $this->tf->formatDuration($calendar->slotSize); ?>)
					</a>
				</div>
				<?php if( isset($syncedFrom[$calendar->id]) ) : ?>
					<div class="hc-fs2 hc-muted2">
					__Synced From__: <?php echo $this->viewCalendar->render( $syncedFrom[$calendar->id] ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="hc-col hc-col-9">
			<?php if( ! $entriesByCalendar[$calendar->id] ) : ?>
				__None__
			<?php endif; ?>
			<div class="hc4-admin-list-secondary">
				<?php foreach( $entriesByCalendar[$calendar->id] as $e ) : ?>
					<div>
						<div class="hc-grid">

							<div class="hc-col hc-col-4">
								<?php echo $this->viewTitle->render( $e ); ?>
							</div>

							<div class="hc-col hc-col-6">
								<?php
								$appliedOnView = NULL;

								switch( $e->appliedOn ){
									case 'everyday':
										$appliedOnView = '__Every Day__';
										break;

									case 'daysofweek':
										$daysOfWeek = $e->appliedOnDetails;
										$appliedOnView = array();
										foreach( $daysOfWeek as $d ){
											$appliedOnView[] = $this->tf->formatWeekday( $d );
										}
										$appliedOnView = join( ', ', $appliedOnView );
										break;
								}

								$validView = NULL;
								if( $e->validFromDate && $e->validToDate ){
									if( $e->validToDate == $e->validFromDate ){
										$appliedOnView = $this->tf->formatDateDate( $e->validFromDate );
									}
									else {
										$validView = $this->tf->formatDateDate( $e->validFromDate ) . ' &rarr; ' . $this->tf->formatDateDate( $e->validToDate );
									}
								}
								elseif( $e->validFromDate ){
									$validView = $this->tf->formatDateDate( $e->validFromDate ) . ' &rarr;';
								}
								elseif( $e->validToDate ){
									$validView = '&rarr; ' . $this->tf->formatDateDate( $e->validToDate );
								}
								?>
								<div>
									<div>
										<?php echo $appliedOnView; ?>
									</div>
									<?php if( $validView ) : ?>
										<div class="hc-fs2 hc-muted2">
											<?php echo $validView; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>

							<div class="hc-col hc-col-2">
								<?php echo $this->viewPriority->render($e->priority); ?>
							</div>

						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>

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

		$return[] = array( 'manager/availability', '__Availability__' );
		return $return;


		// $calendars = $this->rest->get( 'api/users/' . $currentUserId . '/calendars/manager' );
		// $calendars = json_decode( $calendars, TRUE );
		// if( $calendars ){
			// $return[] = array( 'manager/schedule', '__Schedule__' );
		// }

		return $return;
	}
}