<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Translate_Wordpress
	implements HC4_Translate_Interface
{
	protected $domain = 'hitcode';
	protected $locale = '';

	public function __construct( $domain, $pluginDir )
	{
		$this->domain = $domain;
		// $this->locale = $config->getConfigLocale();

		$langDir = plugin_basename( $pluginDir ) . '/languages';
		$langFullDir = $pluginDir . '/languages';

		add_filter( 'locale', array($this, 'setWpLocale') );

		// load_plugin_textdomain( $this->domain, '', $langDir );
		$locale = get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, $domain );

		$mofile = $domain . '-' . $locale . '.mo';
		$fullMofile = $langFullDir . '/' . $mofile;
		$load_result = load_textdomain( $domain, $fullMofile );
		if( ! $load_result ){
			$load_result = load_plugin_textdomain( $domain, '', $langDir );
		}

		remove_filter( 'locale', array($this, 'setWpLocale') );
	}

	public function setWpLocale( $locale )
	{
		if( $this->locale ){
			$locale = $this->locale;
		}
		return $locale;
	}

	public function translate( $string )
	{
		$string = "" . $string;
		preg_match_all( '/__(.+)__/U', $string, $ma );

		$replace = array();
		$count = count($ma[0]);
		for( $ii = 0; $ii < $count; $ii++ ){
			$what = $ma[0][$ii];
			$replace[$what] = $what;
		}

		foreach( $replace as $what => $from ){
			$from = substr( $what, 2, -2 );
			$to = __( $from, $this->domain );
			if( $to == $from ){
				$to = __( $from );
			}
			$string = str_replace( $what, $to, $string );
		}

		return $string;
	}
}