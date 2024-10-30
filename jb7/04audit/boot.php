<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Audit_Boot
	implements HC4_App_Module_Interface
{
	public function __construct(
		HC4_Migration_Interface $migration
	)
	{
		$migration
			->register( 'audit', 1, 'JB7_04Audit_Data_Migration@version1' )
			;
	}
}