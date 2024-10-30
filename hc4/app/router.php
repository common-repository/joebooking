<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_App_Router_
{
	public function add( $methodSlug, $handler );
	public function find( $method, $slug );
}

class HC4_App_Router implements HC4_App_Router_
{
	protected $routes = array();

	public function __construct(
	)
	{}

	protected function _prepare( $methodSlug, $handler )
	{
		$methodSlugArray = explode( '/', $methodSlug, 2 );
		$method = array_shift( $methodSlugArray );
		$slug = array_shift( $methodSlugArray );
		$method = strtolower( $method );
		$return = array( $method, $slug, $handler );
		return $return;
	}

	public function add( $methodSlug, $handler )
	{
		$add = $this->_prepare( $methodSlug, $handler );
		$this->routes[] = $add;
		return $this;
	}

	public function prepend( $methodSlug, $handler )
	{
		$add = $this->_prepare( $methodSlug, $handler );
		array_unshift( $this->routes, $add );
		return $this;
	}

	public function find( $method, $slug )
	{
		$return = array();
		$method = strtolower( $method );

		reset( $this->routes );
		foreach( $this->routes as $r ){
			list( $thisMethod, $thisRoute, $thisHandler ) = $r;

			if( $thisMethod != $method ){
				continue;
			}

		// convert to re
			$re = $thisRoute;

		// find #href like things
			$hash = NULL;
			$hashPos = strpos( $re, '#' );
			if( FALSE !== $hashPos ){
				$hash = substr( $re, $hashPos + 1 );
				$re = substr( $re, 0, $hashPos ); 
			}

			$re = str_replace( '[', '(', $re );
			$re = str_replace( ']', ')', $re );
			$re = str_replace( '*', '.+', $re );

			// $re = str_replace( ':id', '[\w\-]+', $re );
			$re = str_replace( ':id', '[\d\_\-]+', $re );
		// find :param like things
			$re = preg_replace( '/\:(\w+)/', '[^\/]+', $re );

			$re = '%^' . $re . '$%';

			if( ! preg_match($re, $slug, $matches) ){
				continue;
			}

// echo "BINGO!<br>";
// _print_r( $matches );

			array_shift( $matches );

			$i = $hash ? $hash : count($return);
			$return[ $i ] = array( $thisHandler, $matches );
		}

		return $return;
	}
}