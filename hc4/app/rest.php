<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
/* currently it uses the same app, potentially it can make real http calls */
 class HC4_App_Rest
{
	public function __construct( 
		HC4_App $app
	)
	{}

	public function get( $slug )
	{
		return $this->app->handle( 'get', $slug );
	}

	public function post( $slug, $data )
	{
		return $this->app->handle( 'post', $slug, $data );
	}

	public function put( $slug, $data )
	{
		return $this->app->handle( 'put', $slug, $data );
	}

	public function delete( $slug )
	{
		return $this->app->handle( 'delete', $slug );
	}
}