<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Manager_Calendar
{
	public function __construct(
		JB7_21Availability_Data_Repo $repo,
		JB7_21Availability_Ui_Priority $viewPriority,
		JB7_21Availability_Ui_Title $viewTitle,
		JB7_21Availability_Ui_Manager_Loader $loader,

		JB7_11Calendars_Data_Repo $repoCalendars,
		JB7_11Calendars_Ui_Title $viewCalendar,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug, $calendarId )
	{
		$calendars = $this->loader->getManagedCalendars();
		$calendar = $calendars[$calendarId];

		$syncedFromId = $this->repo->getSync( $calendar );
		$syncedFrom = $syncedFromId ? $calendars[$syncedFromId] : NULL;

		$entries = $this->repo->findAll();
		$entries = array_filter( $entries, function($e) use ($calendarId){
			return ($e->calendar->id == $calendarId);
		});

		$return = $this->render( $entries, $syncedFrom );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $entries, JB7_11Calendars_Data_Model $syncedFrom = NULL )
	{
		ob_start();
?>

<?php if( ! $entries ) : ?>
	__None__
<?php endif; ?>

<?php if( $entries ) : ?>
<div class="hc4-admin-list-primary">
	<div class="hc-grid">
		<div class="hc-col hc-col-4">__Time__</div>
		<div class="hc-col hc-col-6">__Details__</div>
		<div class="hc-col hc-col-2">__Priority__</div>
	</div>

	<?php foreach( $entries as $e ) : ?>
		<div class="hc-grid">

			<div class="hc-col hc-col-4">
				<?php if( ! $syncedFrom ) : ?>
					<a class="hc4-admin-title-link hc-xs-block" href="HREFGET:{CURRENT}/<?php echo $e->id; ?>">
				<?php endif; ?>
				<?php echo $this->viewTitle->render( $e ); ?>
				<?php if( ! $syncedFrom ) : ?>
					</a>
				<?php endif; ?>
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
	<?php endforeach; ?>
</div>
<?php endif; ?>

<?php 
		return ob_get_clean();
	}

	public function title( $calendarId )
	{
		$calendar = $this->repoCalendars->findById( $calendarId );
		$return = $this->viewCalendar->render( $calendar );
		return $return;
	}

	public function menu( $calendarId )
	{
		$return = array();

		$calendars = $this->loader->getManagedCalendars();
		$calendar = $calendars[$calendarId];

		$syncedFromId = $this->repo->getSync( $calendar );
		if( $syncedFromId ){
			$syncedFrom = $calendars[$syncedFromId];
			if( $syncedFromId ){
				$return[] = array( '{CURRENT}/unsync', NULL, '__Unsync From Another Calendar__' . ': ' . $this->viewCalendar->render($syncedFrom) );
			}
		}
		else {
			$return[] = array( '{CURRENT}/new', '__New Availability__' );

			unset( $calendars[$calendarId] );
		// don't allow to sync from if it's itself synced or something sync from it
			$ids = array_keys( $calendars );
			foreach( $ids as $id ){
				if( ! isset($calendars[$id]) ){
					continue;
				}

				$syncedFromId = $this->repo->getSync( $calendars[$id] );
				if( $syncedFromId ){
					if( $syncedFromId == $calendarId ){
						$calendars = array();
						break;
					}
					unset( $calendars[$syncedFromId] );
				}
			}

			if( $calendars ){
				$return[] = array( '{CURRENT}/sync', '__Sync From Another Calendar__' );
			}
		}

		return $return;
	}
}