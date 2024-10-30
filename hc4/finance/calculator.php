<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Finance_Calculator
{
	protected $result = 0;

	function __construct()
	{
		$this->reset();
	}

	public function reset()
	{
		$this->result = 0;
		return $this;
	}

	public function add( $amount )
	{
		$this->result += $amount;
	}

	public function get()
	{
		$return = $this->result;
		$return = $return * 100;

		$test1 = (int) $return;
		$diff = abs($return - $test1);
		if( $diff < 0.01 ){
		}
		else {
			$return = ($return > 0) ? ceil( $return ) : floor( $return );
		}

		$return = (int) $return;
		$return = $return/100;
		return $return;
	}
}