<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Duration
{
	public function __construct(
		HC4_Html_Input_Helper $helper,
		HC4_Html_Input_Select $inputSelect
	)
	{}

	public function grab( $name, array $post )
	{
		$hoursName = $name . '_hours';
		$minutesName = $name . '_minutes';

		$durationHours = isset( $post[$hoursName] ) ? $post[$hoursName] : 0;
		$durationMinutes = isset( $post[$minutesName] ) ? $post[$minutesName] : 0;
		$return = $durationHours + $durationMinutes;

		return $return;
	}

	public function render( $name, $value = 0 )
	{
		$value = $this->helper->getValue( $name, $value );

		$hoursName = $name . '_hours';
		$minutesName = $name . '_minutes';

		$currentValueHours = floor( $value / (60*60) ) * 60*60;
		$currentValueMinutes = $value - $currentValueHours;

		$hoursOptions = array(
			0 => '0',			1*60*60 => '1',	2*60*60 => '2',	3*60*60	=> '3',
			4*60*60 => '4',	5*60*60 => '5',	6*60*60 => '6',	7*60*60	=> '7',
			8*60*60 => '8',	9*60*60 => '9',	10*60*60 => '10',	11*60*60	=> '11',
			12*60*60 => '12',	13*60*60 => '13',	14*60*60 => '14',	15*60*60	=> '15',
			16*60*60 => '16',	17*60*60 => '17',	18*60*60 => '18',	19*60*60	=> '19',
			20*60*60 => '20',	21*60*60 => '21',	22*60*60 => '22',	23*60*60	=> '23',
		);

		$minutesOptions = array(
			0	=> '00',		5*60	=> '05',	10*60	=> '10',	15*60	=> '15',
			20*60	=> '20',	25*60	=> '25',	30*60	=> '30',	35*60	=> '35',
			40*60	=> '40',	45*60	=> '45',	50*60	=> '50',	55*60	=> '55',
		);

		ob_start();
?>

	<div class="hc-xs-flex-grid">
		<div class="hc-mx1 hc-xs-flex-grow">
			<label>
				<div class="hc-fs2 hc-muted2">
				__Hours__
				</div>
				<?php echo $this->inputSelect->render( $hoursName, $hoursOptions, $currentValueHours ); ?>
			</label>
		</div>

		<div class="hc-mx1 hc-xs-flex-grow">
			<label>
				<div class="hc-fs2 hc-muted2">
					__Minutes__
				</div>
				<?php echo $this->inputSelect->render( $minutesName, $minutesOptions, $currentValueMinutes ); ?>
			</label>
		</div>

	</div>

<?php 
		$out = ob_get_clean();

		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}
}