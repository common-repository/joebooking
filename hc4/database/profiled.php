<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Database_Profiled
	implements HC4_Database_Interface
{
	protected $db = NULL;
	protected $profiler = NULL;

	public function __construct( HC4_Database_Interface $db, HC4_App_Profiler $profiler )
	{
		$this->db = $db;
		$this->profiler = $profiler;
	}

	public function query( $sql )
	{
		$this->profiler->markQueryStart( $sql );
		$return = $this->db->query( $sql );
		$this->profiler->markQueryEnd();
		return $return;
	}

	public function insertId()
	{
		return $this->db->insertid();
	}
}