<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Href_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();

		$myAppName = $appConfig['app-name'];
		$myAppShortName = $appConfig['app-short-name'];

		$uri = new HC4_App_Uri;
		$currentSlug = $uri->getSlug();

		switch( $appConfig['platform'] ){
			case 'standalone':
				$template = $uri->makeUrl( '{SLUG}' );

				$templateGet = $template;
				$templatePost = $template;
				$templateApi = $template;

			// ASSETS
				$assetsWebDir = $uri->baseUrl();
				if( substr($assetsWebDir, -1) != '/' ){
					$test = explode('/', $assetsWebDir);
					$lastPart = array_pop( $test );
					if( strpos($lastPart, '.') !== FALSE ){
						$assetsWebDir = dirname( $assetsWebDir );
					}
				}
				if( substr($assetsWebDir, -1) != '/' ){
					$assetsWebDir = $assetsWebDir . '/';
				}
				$templateAsset = $assetsWebDir . '{SLUG}';

				break;

			case 'joomla':
				$template = $uri->makeUrl( '{SLUG}' );

				$templateGet = $template;
				$templatePost = $template;
				$templateApi = $template;

			// ASSETS
				$templateAsset = JUri::base() . 'components/com_' . $myAppName . '/' . '{SLUG}';
				break;

			case 'wordpress':
				$templateGet = $uri->makeUrl( '{SLUG}' );

			// API
				$url = parse_url( site_url('/') );

				$baseUrl = $url['scheme'] . '://'. $url['host'];
				if( isset($url['port']) && (80 != $url['port']) ){
					$baseUrl .= ':' . $url['port'];
				}
				$baseUrl .= $url['path'];

				$templateApi = $baseUrl;
				$templateApi .= (isset($url['query']) && $url['query']) ? '?' . $url['query'] . '&' : '?';
				$templateApi .= 'hcs=' . $myAppShortName . '&hca={SLUG}';

			// POST
				// $templatePost = $templateApi;
				$templatePost = $templateGet;
				$templatePost .= ( FALSE === strpos($templatePost, '?') ) ? '?' : '&';
				$templatePost .= 'hcs=' . $myAppShortName;

			// ASSETS
				$pluginFile = $appConfig['app-dir'] . '/' . $appConfig['app-name'];
				// $templateAsset = plugins_url( '{SLUG}', $appConfig['app-dir'] );
				$templateAsset = plugins_url( '{SLUG}', $pluginFile );
				break;
		}

	// OVERRIDE BY CONFIG FILE
		$templateAsset = array( '_' => $templateAsset );
		if( isset($appConfig['app-href-asset']) ){
			$templateAsset = array_merge( $templateAsset, $appConfig['app-href-asset'] );
		}

		$href = new HC4_Html_Href_Implementation( $currentSlug, $templateGet, $templatePost, $templateApi, $templateAsset );
		$bind['HC4_Html_Href_Interface'] = $href;

		return $bind;
	}
}