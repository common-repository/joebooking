<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_21Availability_Ui_Manager_Sync
{
	public function __construct(
		JB7_21Availability_Data_Repo $repo,
		JB7_21Availability_Ui_Manager_Loader $loader,

		JB7_11Calendars_Ui_Title $viewCalendar,

		HC4_Html_Input_Select $inputSelect,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug, $calendarId )
	{
		$calendars = $this->loader->getManagedCalendars();
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
				unset( $calendars[$id] );
			}
		}

		$return = $this->render( $calendars );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $calendars )
	{
		$calendarOptions = array();
		foreach( $calendars as $calendar ){
			$calendarOptions[ $calendar->id ] = $this->viewCalendar->render( $calendar );
		}
		ob_start();
?>
<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-form-elements">
		<div class="hc4-form-element">
			<label>
				__Calendar__
			</label>
			<?php echo $this->inputSelect->render( 'calendar', $calendarOptions ); ?>
		</div>

	</div>

	<div class="hc4-form-buttons">
		<button class="hc4-admin-btn-primary">__Save__</button>
	</div>

</form>

<?php 
		return ob_get_clean();
	}

	public function post( $slug, array $post, $calendarId )
	{
		$errors = array();

		if( ! (isset($post['calendar']) && $post['calendar']) ){
			$errors['calendar'] = '__Required Field__';
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		$calendars = $this->loader->getManagedCalendars();

		$model = $calendars[ $calendarId ];

		$fromCalendarId = $post['calendar'];
		$fromCalendar = $calendars[ $fromCalendarId ];

	// DO
		try {
			$this->repo->createSync( $model, $fromCalendar );
		}
		catch( HC4_App_Exception_DataError $e ){
			$to = '-referrer-';
			$return = array( $to, NULL, $e->getMessage() );
			return $return;
		}

		$slugArray = explode( '/', $slug );
		$to = implode( '/', array_slice($slugArray, 0, -2) );
		$return = array( $to, '__Availability Saved__' );

		return $return;
	}

	public function postUnsync( $slug, $post, $calendarId )
	{
		$calendars = $this->loader->getManagedCalendars();

		$model = $calendars[ $calendarId ];

	// DO
		try {
			$this->repo->deleteSync( $model );
		}
		catch( HC4_App_Exception_DataError $e ){
			$to = '-referrer-';
			$return = array( $to, NULL, $e->getMessage() );
			return $return;
		}

		$slugArray = explode( '/', $slug );
		$to = implode( '/', array_slice($slugArray, 0, -2) );
		$return = array( $to, '__Availability Saved__' );

		return $return;
	}
}