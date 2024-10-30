<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_App_Events
{
	protected $observers = array();

	public function __construct(
		HC4_App_Factory $factory
	)
	{}

	public function register( $event, $handler )
	{
		$event = strtolower( $event );
		$this->observers[] = array( $event, $handler );
		return $this;
	}

	public function publish( $event )
	{
		$args = func_get_args();
		$event = array_shift( $args );

		$event = strtolower( $event );

		reset( $this->observers );
		foreach( $this->observers as $observer ){
			list( $thisEvent, $handler ) = $observer;
			if( $event != $thisEvent ){
				continue;
			}

			$handler = trim( $handler );
			if( FALSE === strpos($handler, '@') ){
				$method = 'call';
			}
			else {
				list( $handler, $method ) = explode( '@', $handler );
			}
			$handler = array( $handler, $method );

			if( ! is_object($handler[0]) ){
				$handler[0] = $this->factory->make( $handler[0] );
			}

			call_user_func_array( $handler, $args );
		}

		return $event;
	}
}