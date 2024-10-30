<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Database_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();

		$prefix = NULL;
		switch( $appConfig['platform'] ){
			case 'standalone':
				$dbConfig = $appConfig['database'];
				if( isset($dbConfig['dbdriver']) && ('sqlite3' == $dbConfig['dbdriver']) ){
					if( (strpos($dbConfig['database'], '/') === FALSE) && (strpos($dbConfig['database'], '\\') === FALSE) ){
						$dbConfig['database'] = $appConfig['app-dir'] . '/' . $dbConfig['database'];
					}
				}

				$db = new HC4_Database_Standalone( $dbConfig );
				break;

			case 'joomla':
				$db = new HC4_Database_Joomla();
				$prefix = '#__';
				break;

			case 'wordpress':
				$db = new HC4_Database_WordPress();
				global $wpdb;
				$prefix = $wpdb->prefix;
				break;
		}

		if( isset($appConfig['HC4_App_Profiler']) ){
			$profiler = $appConfig['HC4_App_Profiler'];
			$db = new HC4_Database_Profiled( $db, $profiler );
		}

		$db = new HC4_Database_Prefixed( $db, $prefix );
		$bind['HC4_Database_Interface'] = $db;

		return $bind;
	}
}