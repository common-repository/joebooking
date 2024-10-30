<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Screen_WordPress
	extends HC4_Html_Screen_Abstract
	implements HC4_Html_Screen_Interface
{
	public function __construct( 
		HC4_App_Factory $factory,
		HC4_App_Profiler $profiler,
		HC4_Session_Interface $session,
		HC4_Translate_Interface $translate,
		HC4_CSRF_Interface $csrf,
		HC4_Html_Href_Interface $href
	)
	{}

	public function renderWidget( $slug, $return )
	{
		$return = parent::renderWidget( $slug, $return );
		$cssReplace = array(
			'hc4-admin-btn-primary' => 'button button-primary button-large',
			'hc4-admin-link-secondary'	=> 'hc-block page-title-action hc-top-auto',
			);
		foreach( $cssReplace as $from => $to ){
			$return = str_replace( $from, $to, $return );
		}
		return $return;
	}

	public function render( $slug, $result )
	{
		$isAjax = FALSE;
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ){
			$isAjax = TRUE;
		}
		elseif( substr( $slug, -strlen(':ajax') ) == ':ajax' ){
			$isAjax = TRUE;
		}

		$result = $this->renderWidget( $slug, $result );

		if( $isAjax ){
			return $result;
		}

	// ASSETS
		$css = $this->getCss($slug);
		for( $ii = 0; $ii < count($css); $ii++ ){
			$css[$ii] = $this->href->hrefAsset( $css[$ii] );
		}
		$js = $this->getJs( $slug );
		for( $ii = 0; $ii < count($js); $ii++ ){
			$js[$ii] = $this->href->hrefAsset( $js[$ii] );
		}

		$enqueuer = new HC4_Ui_Enqueuer_WordPress();
		$assetsView = $enqueuer->call( $css, $js );

		ob_start();
?>

<div class="wrap">
<div class="hc4-main">
<?php echo $result; ?>
</div>
</div>

<?php 
		$return = ob_get_clean();

		if( $this->profiler ){
			if( defined('HC4_DEBUG') && HC4_DEBUG ){
				$return = $this->profiler->render( $return );
			}
		}

		return $return;
	}
}