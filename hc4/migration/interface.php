<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_Migration_Interface
{
	public function up();
	public function register( $migrationName, $migrationVersion, $migrationHandler );

	public function get( $migrationName );
	public function set( $migrationName, $newVersion );
}