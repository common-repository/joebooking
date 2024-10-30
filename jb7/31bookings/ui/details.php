<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_Ui_Details
{
	public function __construct(
		JB7_31Bookings_Data_Fields_Repo $repoFields
	)
	{}

	public function renderText( JB7_31Bookings_Data_Model_Details $details )
	{
		$fields = $this->repoFields->findAll();

		$return = array();
		foreach( $fields as $f ){
			if( NULL !== $details->{$f->name} ){
				$return[ $f->name ] = $f->label . ': ' . $details->{$f->name};
			}
		}

		$return = join( "\n", $return );
		return $return;
	}

	public function render( JB7_31Bookings_Data_Model_Details $details )
	{
		$fields = $this->repoFields->findAll();

		ob_start();
?>
	<?php foreach( $fields as $f ) : ?>
		<label>
			<?php echo $f->label; ?>
		</label>
		<div class="hc-p2 hc-border hc-border-gray hc-rounded">
			<?php echo $f->render( $details->{$f->name} ); ?>
		</div>

	<?php endforeach; ?>

<?php 
		return ob_get_clean();
	}


	public function renderOne( JB7_31Bookings_Data_Model_Details $details )
	{
		$f = $this->repoFields->findFirstRequired();
		$return = $f->render( $details->{$f->name} );
		return $return;
	}
}