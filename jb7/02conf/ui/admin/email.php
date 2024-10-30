<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_02Conf_Ui_Admin_Email
{
	protected $_props = array(
		'email_from',
		'email_fromname',
		'email_html'
	);

	public function __construct(
		HC4_Settings_Interface $settings,

		HC4_Html_Input_Text $inputText,
		HC4_Html_Input_RadioSet $inputRadioSet,

		HC4_Html_Screen_Interface $screen
		)
	{}

	public function post( $slug, array $post )
	{
		foreach( $this->_props as $pname ){
			$v = isset($post[$pname]) ? $post[$pname] : NULL;
			$this->settings->set( $pname, $v );
		}

		$return = array( '-referrer-', '__Settings Updated__' );
		return $return;
	}

	public function get( $slug )
	{
		$values = array();
		foreach( $this->_props as $pname ){
			$values[$pname] = $this->settings->get( $pname );
		}

		$return = $this->render( $values );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $values )
	{
		ob_start();
?>

<form method="post" action="HREFPOST:{CURRENT}">
	<div class="hc4-form-elements">

		<div class="hc4-form-element">
			<label>
				__Send Email From Address__
			</label>
			<?php echo $this->inputText->render( 'email_from', $values['email_from'] ); ?>
		</div>

		<div class="hc4-form-element">
			<label>
				__Send Email From Name__
			</label>
			<?php echo $this->inputText->render( 'email_fromname', $values['email_fromname'] ); ?>
		</div>

		<?php
		$options = array(
			'1'	=> 'HTML',
			'0'	=> '__Plain Text__',
			);
		?>
		<div class="hc4-form-element">
			<label>
				__Email Format__
			</label>
			<?php echo $this->inputRadioSet->renderInline( 'email_html', $options, $values['email_html'] ); ?>
		</div>

	</div>

	<div class="hc4-form-buttons">
		<input type="submit" class="hc4-admin-btn-primary" value="__Save__">
	</div>
</form>

<?php 
		return ob_get_clean();
	}
}