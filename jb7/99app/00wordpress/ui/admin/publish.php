<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_99App_00WordPress_Ui_Admin_Publish
{
	public function __construct(
		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug )
	{
		$shortcode = 'jb7';

		$pagesWithShortcode = array();
		$pageIds = HC4_App_Functions::wpGetIdByShortcode($shortcode);
		if( $pageIds ){
			foreach( $pageIds as $pid ){
				$link = get_permalink( $pid );
				$label = get_the_title( $pid );
				$pagesWithShortcode[] = array( $link, $label );
			}
		}
		else {
			$pagesWithShortcode[] = '__None__';
		}

		$return = $this->render( $pagesWithShortcode );

		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $pagesWithShortcode )
	{
		$htmlFile = dirname(__FILE__) . '/publish.html.php';
		ob_start();
		require( $htmlFile );
		$text = ob_get_contents();
		ob_end_clean();

		ob_start();
?>
	<div class="hc-grid hc-mxn2">
		<div class="hc-col hc-col-8 hc-px2">
			<?php echo $text; ?>
		</div>
		<div class="hc-col hc-col-4 hc-px2">
			<div class="hc4-admin-list-secondary">
				<div>
					<strong>__Pages With Shortcode__</strong>
				</div>
				<?php foreach( $pagesWithShortcode as $p ) : ?>
					<div>
						<?php if( is_array($p) ) : ?>
							<a target="_blank" class="hc4-admin-link" href="<?php echo $p[0]; ?>"><?php echo $p[1]; ?></a>
						<?php else : ?>
							<?php echo $p; ?>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

				<div>
					<a target="_blank" class="hc4-admin-link-secondary hc-block" href="<?php echo admin_url('post-new.php'); ?>">__Add New__</a>
				</div>

			</div>
		</div>
	</div>

<?php 
		return ob_get_clean();
	}
}