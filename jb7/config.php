<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
if( ! defined('JB7_VERSION') ){
	define( 'JB7_VERSION', 703 );
}

// MUST DEFINE $platform OUTSIDE OF THIS FILE, standalone|wordpress|joomla

return array(
'app-name'			=> 'joebooking7',
'app-short-name'	=> 'jb7',

'modules' => array(
	'hc4_app',
	'hc4_assets',
	'hc4_database',
	'hc4_crud',
	'hc4_settings',
	'hc4_csrf',
	'hc4_migration',
	'hc4_redirect',
	'hc4_session',
	'hc4_auth',
	'hc4_translate',
	'hc4_time',
	'hc4_finance',
	'hc4_email',
	'hc4_ui',
	'hc4_html_href',
	'hc4_html_input',
	'hc4_html_screen',
	'hc4_html_widget',

	'jb7_01users',
		'jb7_01users_00' . $platform,
	'jb7_02conf',
		'jb7_02conf_00' . $platform,
	'jb7_03acl',
		'jb7_03acl_00' . $platform,
	'jb7_04audit',
	'jb7_04notifications',

	'jb7_11calendars',

	'jb7_21availability',
	'jb7_31bookings',
		'jb7_31bookings_02conf',
		'jb7_31bookings_04notifications',

	'jb7_41schedule',
	'jb7_42front',
	'jb7_43manage',

	'jb7_98demo',
	'jb7_99app',
		'jb7_99app_00' . $platform,
)
);