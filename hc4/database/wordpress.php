<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Database_Wordpress implements HC4_Database_Interface
{
	protected $wpdb = NULL;

	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function query( $sql )
	{
		$is_select = FALSE;
		if ( preg_match( '/^\s*(select|show)\s/i', $sql ) ) {
			$is_select = TRUE;
		}

		if( $is_select ){
			return $this->wpdb->get_results( $sql, ARRAY_A );
		}
		else {
			// echo "NOT SELECT: '$sql'";
			return $this->wpdb->query( $sql );
		}
	}

	public function insertId()
	{
		return $this->wpdb->insert_id;
	}
}