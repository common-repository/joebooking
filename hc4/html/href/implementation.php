<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Href_Implementation
	implements HC4_Html_Href_Interface
{
	protected $currentSlug;
	protected $templateGet;
	protected $templatePost;
	protected $templateApi;
	protected $templateAsset;

	public function __construct(
		$currentSlug = NULL,
		$templateGet = NULL,
		$templatePost = NULL,
		$templateApi = NULL,
		$templateAsset = NULL
		)
	{
		$this->currentSlug = $currentSlug;
		$this->templateGet = $templateGet;
		$this->templatePost = $templatePost;
		$this->templateApi = $templateApi;
		$this->templateAsset = $templateAsset;
	}

	public function hrefGet( $slug )
	{
		$return = $this->_fromTemplate( $this->templateGet, $slug );
		return $return;
	}

	public function hrefPost( $slug )
	{
		$return = $this->_fromTemplate( $this->templatePost, $slug );
		return $return;
	}

	public function hrefApi( $slug )
	{
		$return = $this->_fromTemplate( $this->templateApi, $slug );
		return $return;
	}

	public function hrefAsset( $src )
	{
		if( HC4_App_Uri::isFullUrl($src) ){
			$return = $src;
		}
		else {
			$srcArray = explode( '/', $src );
			$templateAsset = isset( $this->templateAsset[$srcArray[0]] ) ? $this->templateAsset[$srcArray[0]] : $this->templateAsset['_'];
			$return = $this->_fromTemplate( $templateAsset, $src );
		}
		return $return;
	}

	protected function _fromTemplate( $template, $slug )
	{
		if( HC4_App_Uri::isFullUrl($slug) ){
			return $slug;
		}
		$slug = str_replace( '{CURRENT}', $this->currentSlug, $slug );
		$return = str_replace( '{SLUG}', $slug, $template );
		return $return;
	}

	public function processOutput( $string )
	{
		$string = "" . $string;

		preg_match_all( '/[\'"]HREFGET\:(.+)[\'"]/U', $string, $ma );
		$count = count($ma[0]);
		for( $ii = 0; $ii < $count; $ii++ ){
			$what = $ma[0][$ii];

			$slug = $ma[1][$ii];
			$to = $this->hrefGet( $slug );
			$to = '"' . $to . '"';
			$string = str_replace( $what, $to, $string );
		}

		preg_match_all( '/[\'"]HREFPOST\:(.+)[\'"]/U', $string, $ma );
		$count = count($ma[0]);
		for( $ii = 0; $ii < $count; $ii++ ){
			$what = $ma[0][$ii];

			$slug = $ma[1][$ii];
			$to = $this->hrefPost( $slug );
			$to = '"' . $to . '"';
			$string = str_replace( $what, $to, $string );
		}

		preg_match_all( '/[\'"]HREFAPI\:(.+)[\'"]/U', $string, $ma );
		$count = count($ma[0]);
		for( $ii = 0; $ii < $count; $ii++ ){
			$what = $ma[0][$ii];

			$slug = $ma[1][$ii];
			$to = $this->hrefApi( $slug );
			$to = '"' . $to . '"';
			$string = str_replace( $what, $to, $string );
		}

		return $string;
	}
}