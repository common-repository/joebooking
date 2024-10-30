<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_31Bookings_04Notifications_Ui_Admin_Conf_Notifications
{
	public function __construct(
		JB7_04Notifications_Data_Repo $repo,
		JB7_31Bookings_04Notifications_Service_Parser $parser,

		HC4_Html_Input_Text $inputText,
		HC4_Html_Input_RichTextarea $inputTextarea,
		HC4_Html_Input_CheckboxDetails $inputCheckboxDetails,

		HC4_Html_Screen_Interface $screen
	)
	{}

	public function getMessages()
	{
		$return = array(
			'email-booking-created-customer'	=> '__Booking Created__' . ' &rarr; ' . '__Customer__',
			'email-booking-created-manager'	=> '__Booking Created__' . ' &rarr; ' . '__Manager__',
			'email-booking-status-customer'	=> '__Booking Status Changed__' . ' &rarr; ' . '__Customer__',
			'email-booking-status-manager'	=> '__Booking Status Changed__' . ' &rarr; ' . '__Manager__',
			);
		return $return;
	}

	public function post( $slug, $post )
	{
	// DO
		try {
			$labels = $this->getMessages();

			foreach( $labels as $msgId => $label ){
				$subject = $post['subject_' . $msgId];
				$body = $post['body_' . $msgId];
				$this->repo->saveById( $msgId, $subject, $body );

				$isDisabled = isset($post['on_' . $msgId]) && $post['on_' . $msgId] ? FALSE : TRUE;
				if( $isDisabled ){
					$this->repo->disable( $msgId );
				}
				else {
					$this->repo->enable( $msgId );
				}
			}
		}
		catch( HC4_App_Exception_DataError $e ){
			$to = '-referrer-';
			$return = array( $to, NULL, $e->getMessage() );
			return $return;
		}

		$slugArray = explode( '/', $slug );
		// $to = implode( '/', array_slice($slugArray, 0, -1) );
		$to = implode( '/', $slugArray );
		$return = array( $to, '__Notifications Saved__' );

		return $return;
	}

	public function get( $slug )
	{
		$labels = $this->getMessages();
		$messages = array();
		foreach( $labels as $msgId => $label ){
			$message = $this->repo->findById($msgId);
			$messages[ $msgId ] = $message;
		}

		$return = $this->render( $messages, $labels );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $messages, array $labels )
	{
		ob_start();
?>
<form method="post" action="HREFPOST:{CURRENT}">
	<?php foreach( $messages as $msgId => $message ) : ?>
		<?php
		$isDisabled = $this->repo->isDisabled( $msgId );
		?>
		<?php echo $this->renderMessage( $msgId, $message, $labels[$msgId], $isDisabled ); ?>
	<?php endforeach; ?>

	<div class="hc4-form-buttons">
		<button type="submit" class="hc4-admin-btn-primary" title="__Save__">__Save__</button>
	</div>
</form>

<?php 
		return ob_get_clean();
	}

	public function renderMessage( $msgId, JB7_04Notifications_Service_Message $message, $label, $isDisabled )
	{
		$tags = $this->parser->getTags( $message->id );
		ob_start();
?>

<div class="hc-grid hc-mxn2">
	<div class="hc-col hc-col-8 hc-px2">

		<div class="hc4-form-elements">

			<div class="hc4-form-element">
				<label>
					__Subject__
					<?php echo $this->inputText->render( 'subject_' . $msgId, $message->subject ); ?>
				</label>
			</div>

			<div class="hc4-form-element">
				<label>
					__Message__
					<?php echo $this->inputTextarea->render( 'body_' . $msgId, $message->body ); ?>
				</label>
			</div>
		</div>
	</div>

	<div class="hc-col hc-col-4 hc-px2">
		<div>__Tags__</div>
		<div class="hc4-admin-list-secondary hc-border hc-p2">
			<?php foreach( $tags as $tag => $parser ) : ?>
				<div><?php echo $tag; ?></div>
			<?php endforeach; ?>
		</div>
	</div>

</div>

<?php 
		$inputsView = ob_get_clean();

		$label = '<div class="hc-inline-block hc-p1 hc-white hc-bg-gray hc-rounded">' . $label . '</div>';
		ob_start();
?>

<div class="hc-my2">
<?php echo $this->inputCheckboxDetails->render( 'on_' . $msgId, 1, (! $isDisabled), $label, $inputsView ); ?>
</div>

<?php 
		return ob_get_clean();
	}
}