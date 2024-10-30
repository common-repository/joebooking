<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface HC4_App_Uri_
{
	public function getSlug();
	public function makeUrl( $slug = NULL, $proto = NULL );
	public function baseUrl();
	public function fromUrl( $url );

	public static function isFullUrl( $url );
	public static function currentUrl();
}

class HC4_App_Uri implements HC4_App_Uri_
{
	protected $_hca = 'hca';
	protected $_hcs = 'hcs';

	protected $baseUrl = NULL;
	protected $baseParams = array();
	protected $slug = NULL;

	protected $siteRoot = NULL;
	protected $rewrite = FALSE;

	public function __construct( $fromUrl = NULL )
	{
		if( NULL === $fromUrl ){
			$fromUrl = self::currentUrl();
		}

		// echo "CONSTRUCTING<br>";
		$this->fromUrl( $fromUrl );
	}

	public function getSlug()
	{
// _print_r( $this );
// exit;
		return $this->slug;
	}

	public function makeUrl( $slug = NULL, $proto = NULL )
	{
		if( self::isFullUrl($slug) ){
			return $slug;
		}

		if( $slug == '-referrer-' ){
			if( isset($_SERVER['HTTP_REFERER']) && strlen($_SERVER['HTTP_REFERER']) ){
				$this->fromUrl( $_SERVER['HTTP_REFERER'] );
				$slug = $this->getSlug();
				// $return = $_SERVER['HTTP_REFERER'];
				// return $return;
			}
		}

		$href_params = $this->baseParams;

		$hca_param = NULL;
		if( $slug ){
			$hca_param = $slug;
		}

		if( $hca_param ){
		// add random to avoid unnecessary caching
			$href_params[$this->_hca] = $hca_param;
		}

		$return = $this->baseUrl;

		if( $this->rewrite ){
			if( $hca_param ){
				$return .= $hca_param . '/';
			}
			return $return;
		}

		if( $href_params ){
			$href_params = http_build_query( $href_params );
			$href_params = urldecode( $href_params );

			$glue = (strpos($return, '?') === FALSE) ? '?' : '&';
			$return .= $glue . $href_params;
		}

		if( NULL !== $proto ){
// echo "PROTO = '$proto'<br>";
			$starts = array( 'https://', 'http://', '//' );
			foreach( $starts as $prefix ){
				if( substr($return, 0, strlen($prefix)) == $prefix ){
					$return = $proto . substr($return, strlen($prefix));
					break;
				}
			}
		}

		return $return;
	}

	public function baseUrl()
	{
		$return = $this->baseUrl;
		return $return;
	}

	public function fromUrl( $url )
	{
// echo "FROM URL: '$url'<br>";
		$purl = parse_url( $url );

		if( NULL == $this->siteRoot ){
			$this->baseUrl = $purl['scheme'] . '://'. $purl['host'] . $purl['path'];
		}
		else {
			$this->baseUrl = $this->siteRoot;
		}

		$this->baseParams = array();
		// $this->slug = NULL;

		if( $this->rewrite ){
			$hca = array_key_exists($this->_hca, $_GET) ? $_GET[$this->_hca] : NULL;
			$this->slug = $hca;
		}
		elseif( isset($purl['query']) && $purl['query']){
			parse_str( $purl['query'], $base_params );

		/* grab our hca */
			if( isset($base_params[$this->_hca]) ){
				// $this->slug = NULL;
				$hca = $base_params[$this->_hca];

			// trim slashes
				$hca = trim($hca, '/');

				$slug = $hca;
				$this->slug = $slug;
			}

		/* base params */
			unset( $base_params[$this->_hca] );
			unset( $base_params[$this->_hcs] );
			$this->baseParams = $base_params;
		}
		else {
			$this->slug = NULL;
		}

		return $this;
	}

	public static function isFullUrl( $url )
	{
		$return = FALSE;

		if( is_array($url) ){
			$url = array_shift($url);
		}

		$prfx = array( 'http://', 'https://', '//', 'webcal://' );
		reset( $prfx );
		foreach( $prfx as $prf ){
			if( substr($url, 0, strlen($prf)) == $prf ){
				$return = TRUE;
				return $return;
			}
		}

		if( strpos($url, '.php') !== FALSE ){
			$return = TRUE;
			return $return;
		}

		return $return;
	}

	public static function currentUrl()
	{
		$return = 'http';
		if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ){
			$return .= 's';
		}

		$return .= "://";

		if( isset($_SERVER['HTTP_HOST']) && $_SERVER['SERVER_PORT'] != '80'){
			$return .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'];
		}
		else {
			$return .= $_SERVER['HTTP_HOST'];
		}

		if ( ! empty($_SERVER['REQUEST_URI']) ){
			$return .= $_SERVER['REQUEST_URI'];
		}
		else {
			$return .= $_SERVER['SCRIPT_NAME'];
		}

		$return = urldecode( $return );
		return $return;
	}
}