<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Migration_Settings
	implements HC4_Migration_Interface
{
	protected $migrations = array();

	public function __construct(
		HC4_Settings_Interface $settings,
		HC4_App_Factory $factory
	)
	{}

	public function register( $migrationName, $migrationVersion, $migrationHandler )
	{
		$this->migrations[] = array( $migrationName, $migrationVersion, $migrationHandler );
		return $this;
	}

	public function up()
	{
		reset( $this->migrations );
		foreach( $this->migrations as $m ){
			list( $migrationName, $needVersion, $handler ) = $m;
			$installedVersion = $this->get( $migrationName );
			if( $needVersion > $installedVersion ){
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

				call_user_func( $handler );

				$this->set( $migrationName, $needVersion );
			}
		}
	}

	public function get( $migrationName )
	{
		$confName = 'migration_' . $migrationName;
		$installedVersion = $this->settings->get( $confName, 0 );
		if( ! $installedVersion ){
			$installedVersion = 0;
		}

		return $installedVersion;
	}

	public function set( $migrationName, $newVersion )
	{
		$confName = 'migration_' . $migrationName;
		$this->settings->set( $confName, $newVersion );
	}
}