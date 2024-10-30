<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Duration2
{
	public function __construct(
		HC4_Html_Input_Helper $helper,
		HC4_Html_Input_Text $inputText,
		HC4_Html_Input_Select $inputSelect
	)
	{}

	public function grab( $name, array $post )
	{
		$qtyName = $name . '_qty';
		$measureName = $name . '_measure';

		$qty = isset( $post[$qtyName] ) ? $post[$qtyName] : 2;
		$measure = isset( $post[$measureName] ) ? $post[$measureName] : 'hours';
		$return = $qty . ' ' . $measure;

		return $return;
	}

	public function render( $name, $value = '2 hours' )
	{
		$value = $this->helper->getValue( $name, $value );

		$measureOptions = array(
			'minutes'	=> '__Minutes__',
			'hours'		=> '__Hours__',
			'days'		=> '__Days__',
			'weeks'		=> '__Weeks__'
			);

		$value = explode( ' ', $value );

		$currentValueQty = array_shift( $value );
		$currentValueMeasure = array_shift( $value );

		$qtyName = $name . '_qty';
		$measureName = $name . '_measure';

		ob_start();
?>

	<div class="hc-xs-flex-grid">
		<div class="hc-mx1 hc-xs-flex-grow">
			<?php echo $this->inputText->render( $qtyName, $currentValueQty ); ?>
		</div>

		<div class="hc-mx1 hc-xs-flex-grow">
			<?php echo $this->inputSelect->render( $measureName, $measureOptions, $currentValueMeasure ); ?>
		</div>

	</div>

<?php 
		$out = ob_get_clean();

		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}