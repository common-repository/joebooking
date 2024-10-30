<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_02Conf_00WordPress_Boot_CrudTable
	implements HC4_Settings_Database_Crud_Table
{
	protected $table = 'jb7_conf';
	public function __toString()
	{
		return $this->table;
	}
}

class JB7_02Conf_00WordPress_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();
		$bind['HC4_Settings_Database_Crud_Table'] = 'JB7_02Conf_00WordPress_Boot_CrudTable';
		$bind['HC4_Settings_Interface'] = 'HC4_Settings_Database';
		return $bind;
	}

	public function __construct(
		HC4_Settings_Interface $settings
	)
	{
		$defaultEmail = 'info@' . $_SERVER['SERVER_NAME'];
		$settings
			->init( 'email_from', $defaultEmail )
			->init( 'email_fromname', 'JoeBooking' )
			->init( 'email_html', 1 )
			;
	}
}