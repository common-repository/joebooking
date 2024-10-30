<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Time
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
		$nameHour = $name . '_hour';
		$nameMinute = $name . '_minute';

		$hour = $post[ $nameHour ];
		$minute = $post[ $nameMinute ];

		$return = 60 * 60 * $hour + 60 * $minute;
		return $return;
	}

	public function render( $name, $value = 0 )
	{
		$value = $this->helper->getValue( $name, $value );

		$nameHour = $name . '_hour';
		$nameMinute = $name . '_minute';

		$currentValueHour = floor( $value / (60*60) );
		$currentValueMinute = floor( ($value - $currentValueHour * 60 * 60) / 60 );

		$optionsHour = array();
		$this->t->setDateDb('20181218');
		foreach( range(0, 23) as $h ){
			$dt = $this->t->getDateTimeDb();
			$timeView = $this->tf->formatTime( $dt );
			$hourView = str_replace( ':00', '', $timeView );
			$this->t->modify( '+1 hour' );
			$optionsHour[ $h ] = $hourView;
		}

		$optionsMinute = array(
			0	=> '00',		5	=> '05',	10	=> '10',	15	=> '15',
			20	=> '20',	25	=> '25',	30	=> '30',	35	=> '35',
			40	=> '40',	45	=> '45',	50	=> '50',	55	=> '55',
		);

		ob_start();
?>

	<div class="hc-xs-flex-grid">
		<div class="hc-mx1 hc-xs-flex-grow">
			<label>
				<?php echo $this->inputSelect->render( $nameHour, $optionsHour, $currentValueHour ); ?>
			</label>
		</div>
		<div class="hc-mx1">:</div>
		<div class="hc-mx1 hc-xs-flex-grow">
			<label>
				<?php echo $this->inputSelect->render( $nameMinute, $optionsMinute, $currentValueMinute ); ?>
			</label>
		</div>
	</div>

<?php 
		$out = ob_get_clean();

		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}