<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Date
{
	public function __construct(
		HC4_Html_Input_Helper $helper,
		HC4_Html_Input_Select $inputSelect,

		HC4_Time_Interface $t,
		HC4_Time_Format $tf
	)
	{}

	public function grab( $name, array $post )
	{
		$nameYear = $name . '_year';
		$nameMonth = $name . '_month';
		$nameDay = $name . '_day';

		$year = $post[ $nameYear ];

		$month = $post[ $nameMonth ];
		$month = sprintf( '%02d', $month );

		$day = $post[ $nameDay ];
		$day = sprintf( '%02d', $day );

		$return = $year . $month . $day;
		return $return;
	}

	public function render( $name, $value = NULL )
	{
		if( NULL === $value ){
			$value = $this->t->setNow()->getDateDb();
		}

		$value = $this->helper->getValue( $name, $value );

		$nameYear = $name . '_year';
		$nameMonth = $name . '_month';
		$nameDay = $name . '_day';

		$currentValueYear = substr( $value, 0, 4 );
		$currentValueMonth = substr( $value, 4, 2 );
		$currentValueDay = substr( $value, 6, 2 );

		$optionsYear = array();
		$years = range( 2000, 2040 );
		foreach( $years as $y ){
			$optionsYear[ $y ] = $y;
		}

		$optionsMonth = array();
		$months = range( 1, 12 );
		foreach( $months as $m ){
			$monthView = $this->tf->formatMonthName( $m );
			$optionsMonth[ $m ] = $monthView;
		}

		$optionsDay = array();
		$days = range( 1, 31 );
		foreach( $days as $d ){
			$optionsDay[ $d ] = $d;
		}

		ob_start();
?>

	<div class="hc-xs-flex-grid">
		<div class="hc-xs-flex-grow">
			<label>
				<?php echo $this->inputSelect->render( $nameYear, $optionsYear, $currentValueYear ); ?>
			</label>
		</div>
		<div class="hc-mx1">-</div>
		<div class="hc-xs-flex-grow">
			<label>
				<?php echo $this->inputSelect->render( $nameMonth, $optionsMonth, $currentValueMonth ); ?>
			</label>
		</div>
		<div class="hc-mx1">-</div>
		<div class="hc-xs-flex-grow">
			<label>
				<?php echo $this->inputSelect->render( $nameDay, $optionsDay, $currentValueDay ); ?>
			</label>
		</div>
	</div>

<?php 
		$out = ob_get_clean();

		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}