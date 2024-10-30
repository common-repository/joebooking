<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_42Front_Ui_Bookings_Index
{
	public function __construct(
		JB7_31Bookings_Data_Fields_Repo $repoFields,
		JB7_31Bookings_Data_Repo $repo,
		HC4_Html_Input_Text $inputText,

		HC4_Html_Screen_Interface $screen
	)
	{}

	public function post( $slug, $post )
	{
		$f = $this->repoFields->findFirstRequired();
		$fname = 'customer_' . $f->name;

	// VALIDATE POST
		$errors = array();
		if( ! strlen($post['refno']) ){
			$errors['refno'] = '__Required Field__';
		}
		if( ! strlen($post[$fname]) ){
			$errors[$fname] = '__Required Field__';
		}
		if( $errors ){
			throw new HC4_App_Exception_FormErrors( $errors );
		}

		$refno = $post['refno'];
		$booking = $this->repo->findByRefno( $refno );

		if( ! $booking ){
			$msg = '__Booking Not Found__';
			throw new HC4_App_Exception_DataError( $msg );
		}

	// check field
		$fSupplied = $post[$fname];
		$fSupplied = str_replace( ' ', '', $fSupplied );
		$fSupplied = strtolower( $fSupplied );

		$fValue = $booking->details->{$f->name};
		$fValue = str_replace( ' ', '', $fValue );
		$fValue = strtolower( $fValue );

		if( $fValue != $fSupplied ){
			$msg = '__Booking Not Found__';
			throw new HC4_App_Exception_DataError( $msg );
		}

		$token = $this->repo->createToken( $booking );
		$to = $slug . '/' . $token;

		$return = array( $to, NULL );
		return $return;
	}

	public function get( $slug )
	{
		$f = $this->repoFields->findFirstRequired();
		$return = $this->render( $f );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( JB7_31Bookings_Data_Fields_Type $f )
	{
		ob_start();
?>

<form method="post" action="HREFPOST:{CURRENT}" data-ajax="1">

	<div class="hc4-form-elements">

		<div class="hc4-form-element">
			<label>
				__Ref No__
				<?php echo $this->inputText->render( 'refno', '' ); ?>
			</label>
		</div>

		<div class="hc4-form-element">
			<label>
				<?php echo $f->label; ?>
			</label>
			<?php echo $this->inputText->render( 'customer_' . $f->name ); ?>
		</div>

	</div>

	<div class="hc4-form-buttons">
		<button class="hc4-admin-btn-primary">__Find Booking__</button>
	</div>

</form>

<?php 
		return ob_get_clean();
	}
}