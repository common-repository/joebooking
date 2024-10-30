<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Schedule_Cell
{
	public function __construct(
		JB7_41Schedule_Data_Repo $repoSchedule,
		JB7_43Manage_Ui_Schedule_Booking $viewBooking,
		JB7_43Manage_Ui_Schedule_SlotsGroup $viewSlotsGroup,
		HC4_Time_Interface $t
	)
	{}

	public function render( array $cell, array $cellKnows )
	{
		$bookings = isset( $cell['bookings'] ) ? $cell['bookings'] : array();
		$slots = isset( $cell['slots'] ) ? $cell['slots'] : array();
		$slotGroups = $this->repoSchedule->groupSlots( $slots );

		$display = array();
		foreach( $bookings as $e ){
			$display[] = array( $e->startDateTime, 0, $e );
		}
		foreach( $slotGroups as $e ){
			$display[] = array( $e->startDateTime, 1, $e );
		}

		usort( $display, function($a, $b){
			if( $a[0] == $b[0] ){
				return $a[1] < $b[1];
			}
			else {
				return $a[0] > $b[0];
			}
		});

		ob_start();
?>

<?php foreach( $display as $ea ) : ?>
	<?php list( $start, $priority, $e ) = $ea; ?>
	<?php if( $e instanceof JB7_31Bookings_Data_Model ) : ?>
		<?php echo $this->viewBooking->render( $e, $cellKnows ); ?>
	<?php elseif( $e instanceof JB7_41Schedule_Data_Model_SlotsGroup ) : ?>
		<?php echo $this->viewSlotsGroup->render( $e ); ?>
	<?php endif; ?>
<?php endforeach; ?>

<?php 
		$return = ob_get_clean();
		return $return;
	}
}