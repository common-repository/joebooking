<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
abstract class HC4_Html_Screen_Abstract
{
	protected $css = array();
	protected $js = array();
	protected $title = array();
	protected $breadcrumbTitle = array();
	protected $layout = array();
	protected $menu = array();
	protected $partials = array();

	public function css( $slugPreg, $path )
	{
		$this->css[] = array( $slugPreg, $path );
		return $this;
	}

	public function js( $slugPreg, $path )
	{
		$this->js[] = array( $slugPreg, $path );
		return $this;
	}

	public function title( $slugPreg, $value )
	{
		$this->title[] = array( $slugPreg, $value );
		return $this;
	}

	public function breadcrumbTitle( $slugPreg, $value )
	{
		$this->breadcrumbTitle[] = array( $slugPreg, $value );
		return $this;
	}

	public function layout( $slugPreg, $layout )
	{
		$this->layout[] = array( $slugPreg, $layout );
		return $this;
	}

	public function menu( $slugPreg, $menuLink, $menuLabel = NULL )
	{
		if( NULL !== $menuLabel ){
			$this->menu[] = array( $slugPreg, array($menuLink, $menuLabel) );
		}
		else {
			$this->menu[] = array( $slugPreg, $menuLink );
		}

		return $this;
	}

	public function partial( $slugPreg, $partialSlug )
	{
		$this->partials[] = array( $slugPreg, $partialSlug );
		return $this;
	}

	public function getCss( $slug )
	{
		return $this->_find( $this->css, $slug, TRUE );
	}

	public function getJs( $slug )
	{
		return $this->_find( $this->js, $slug, TRUE );
	}

	public function getTitle( $slug )
	{
		return $this->_find( $this->title, $slug, FALSE );
	}

	public function getBreadcrumbTitle( $slug )
	{
		return $this->_find( $this->breadcrumbTitle, $slug, FALSE );
	}

	public function getLayout( $slug )
	{
		return $this->_find( $this->layout, $slug, FALSE );
	}

	public function getMenu( $slug )
	{
		$rawReturn = $this->_find( $this->menu, $slug, TRUE );

		$return = array();
	// stright out
		foreach( $rawReturn as $rm ){
			if( isset($rm[0]) && is_array($rm[0]) ){
				foreach( $rm as $rm2 ){
					$return[] = $rm2;
				}
			}
			else {
				$return[] = $rm;
			}
		}

		return $return;
	}

	public function getPartials( $slug )
	{
		return $this->_find( $this->partials, $slug, TRUE );
	}

	public function getBreadcrumb( $slug )
	{
		$return = array();

		if( $slug ){
			$slug = '/' . $slug;
		}
		$slugParts = explode( '/', $slug );
		array_pop( $slugParts );

		$thisSlug = '';
		while( $slugParts ){
			$addPart = array_shift( $slugParts );
			if( $thisSlug ){
				$thisSlug .= '/';
			}
			$thisSlug .= $addPart;

			$thisTitle = $this->getBreadcrumbTitle( $thisSlug );
			if( ! $thisTitle ){
				$thisTitle = $this->getTitle( $thisSlug );
			}
			if( $thisTitle ){
				$return[] = array( $thisSlug, $thisTitle );
			}
		}

		return $return;
	}

	protected function _find( array $routes, $slug, $many = FALSE )
	{
		$return = $many ? array() : NULL;

		reset( $routes );
		foreach( $routes as $r ){
			$thisRoute = $r[0];

		// convert to re
			$re = $thisRoute;

			$re = str_replace( '[', '(', $re );
			$re = str_replace( ']', ')', $re );
			$re = str_replace( '*', '.*', $re );

			// $re = str_replace( ':id', '[\w\-]+', $re );
			$re = str_replace( ':id', '[\d\_\-]+', $re );
		// find :param like things
			$re = preg_replace( '/\:(\w+)/', '[^\/]+', $re );
			$re = '%^' . $re . '$%';

			// $re = '#' . $thisRoute . '#';
			if( ! preg_match($re, $slug, $matches) ){
				continue;
			}

			if( count($r) > 2 ){
				$thisOne = array_slice( $r, 1 );
			}
			else {
				$thisOne = $r[1];
			}

			if( is_array($thisOne) ){
				$keys = array_keys( $thisOne );
				foreach( $keys as $k ){
					$thisOne[$k] = $this->_processOne( $thisOne[$k], $matches );
				}
			}
			else {
				$thisOne = $this->_processOne( $thisOne, $matches );
			}

			// array_shift( $matches );
			// $return[] = array( $thisHandler, $matches );
			
			if( $many ){
				if( $thisOne ){
					$return[] = $thisOne;
				}
			}
			else {
				$return = $thisOne;
				break;
			}
		}

		return $return;
	}

	protected function _processOne( $thisOne, array $matches )
	{
		if( strpos($thisOne, '@') !== FALSE ){
			list( $className, $method ) = explode( '@', $thisOne );
			$object = $this->factory->make( $className );

			$args = array_slice( $matches, 1 );
			$thisOne = call_user_func_array( array($object, $method), $args );
		}
		else {
			if( count($matches) > 1 ){
				for( $ii = 1; $ii < count($matches); $ii++ ){
					$search = '{$' . $ii . '}';
					$replace = $matches[$ii];
					$thisOne = str_replace( $search, $replace, $thisOne );
				}
			}
		}

		return $thisOne;
	}

	public function renderWidget( $slug, $return )
	{
	// ANNOUNCE IF ANY
		$announceView = NULL;

		$message = $this->session->getFlashdata('message');
		$error = $this->session->getFlashdata('error');
		$debug = $this->session->getFlashdata('debug');

		if( $message OR $debug OR $error ){
			$announce = new HC4_Ui_Announce;
			$announceView = $announce->render( $message, $error, $debug );
			$return = $announceView . $return;
		}

	// LAYOUT
		$title = $this->getTitle( $slug );
		$menu = $this->getMenu( $slug );
		$breadcrumb = $this->getBreadcrumb( $slug );

		$layout = $this->getLayout( $slug );
		$layout = $this->factory->make( $layout );

		$return = $layout->render( $slug, $return, $title, $menu, $breadcrumb );
		$return = $this->csrf->render( $return );

	// TRANSLATE
		$return = $this->translate->translate( $return );

	// HREFS
		$return = $this->href->processOutput( $return );

		return $return;
	}
}