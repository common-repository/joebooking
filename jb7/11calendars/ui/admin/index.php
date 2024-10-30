<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_11Calendars_Ui_Admin_Index
{
	public function __construct(
		JB7_11Calendars_Data_Repo $repo,

		JB7_11Calendars_Ui_Status $viewStatus,
		JB7_11Calendars_Ui_Access $viewAccess,

		HC4_Time_Format $tf,
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function titleStatus( $status )
	{
		$return = $this->viewStatus->render($status);
		return $return;
	}

	public function menu()
	{
		$return = array();

		$entries = $this->repo->findAll();

		$count = array(
			// 'active'	=> 0,
			'archived'	=> 0,
			);
		$statuses = array_keys( $count );

		reset( $entries );
		foreach( $entries as $e ){
			if( ! isset($e->status) ){
				continue;
			}
			if( ! isset($count[$e->status]) ){
				continue;
			}
			$count[$e['status']]++;
		}

		foreach( $count as $status => $statusCount ){
			if( ! $statusCount ){
				continue;
			}

			$label = array();
			$label[] = $this->viewStatus->render($status);
			$label[] = ' [' . $statusCount . ']';

			$return[] = array( '{CURRENT}/status/' . $status, $label );
		}

		return $return;
	}

	public function get( $slug, $status = 'active' )
	{
		$entries = $this->repo->findAll();

		$entries = array_filter( $entries, function($e) use ($status){
			return ( $e->status == $status );
		});

		$return = $this->render( $entries );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $calendars )
	{
		ob_start();
?>

<div class="hc4-admin-list-primary">

<?php foreach( $calendars as $e ) : ?>
	<div>
		<div class="hc-flex-auto-grid">

			<div>
				<a class="hc4-admin-title-link hc-xs-block" href="HREFGET:{CURRENT}/<?php echo $e->id; ?>"><?php echo $e->title; ?></a>
				<?php if( 'public' != $e->access ) : ?>
					&mdash; <?php echo $this->viewAccess->render( $e->access ); ?>
				<?php endif; ?>
				<?php if( 'active' != $e->status ) : ?>
					&mdash; <?php echo $this->viewStatus->render( $e->status ); ?>
				<?php endif; ?>
			</div>
			<div>
				<?php echo $this->tf->formatDuration( $e->slotSize ); ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>

</div>

<?php 
		return ob_get_clean();
	}
}