<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_99App_Ui_Layout
	implements HC4_Html_Screen_Layout_Interface
{
	public function __construct(
		HC4_App $app,
		HC4_Redirect_Interface $redirect,
		HC4_Html_Href_Interface $href,
		JB7_99App_Ui_Promo $promo = NULL
	)
	{}

	public function render( $slug, $content, $title = NULL, array $menu = array(), array $breadcrumb = array() )
	{
		$contentAsMenu = array();

	/* process menu */
		$menuView = array();
		$iis = array_keys( $menu );
		foreach( $iis as $ii ){
			$menuItem = $menu[ $ii ];
			$menuItemView = $this->renderMenuItem( $slug, $menuItem );
			if( NULL == $menuItemView ){
				unset( $menu[$ii] );
			}
			else {
				$menuView[] = $menuItemView;
			}
		}

		if( ! strlen($content) && $menu ){
			if( count($menu) == 1 ){
				$to = $menu[0][0];
				$to = $this->href->hrefGet( $to );
				$this->redirect->call( $to );
				exit;
			}

			$contentAsMenu = $menuView;
			$menuView = array();
		}

		ob_start();
?>

<?php
if( $this->promo ){
	echo $this->promo->render( $slug );
}
?>

<div id="jb7-<?php echo $slug; ?>">
<div class="hc4-admin-page">

	<?php if( $breadcrumb ) : ?>
		<div class="hc4-breadcrumb-desktop hc-xs-hide hc-fs2">
			<div class="hc-flex-grid">

				<?php $ii = 0; ?>
				<?php foreach( $breadcrumb as $menuItem ) : ?>
					<?php list( $to, $label ) = $menuItem; ?>

					<?php if( $ii ) : ?>
					<div class="hc-mx1">
						&raquo;
					</div>
					<?php endif; ?>

					<div>
						<?php if( strpos($label, '<') === FALSE ) : ?>
							<?php echo $this->renderLink2( $to, $label, $label ); ?>
						<?php else : ?>
							<?php echo $label; ?>
						<?php endif; ?>
					</div>

					<?php $ii++; ?>

				<?php endforeach; ?>

				<?php if( 0 && strlen($title) ) : ?>
					<div class="hc-mx1">
						&raquo;
					</div>
					<div>
						<div class="hc4-admin-label"><?php echo $title; ?></div>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<div class="hc4-breadcrumb-mobile hc-lg-hide hc-fs2">
			<?php
			$lastItem = $breadcrumb[ count($breadcrumb) - 1 ];
			list( $to, $label ) = $lastItem;
			?>
			<div class="hc-xs-flex-grid">
				<div class="hc-mx1">
					&laquo;
				</div>

				<div>
					<?php if( strpos($label, '<') === FALSE ) : ?>
						<?php echo $this->renderLink( $to, $label, $label ); ?>
					<?php else : ?>
						<?php echo $label; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if( $title OR $menuView ) : ?>

		<!-- MOBILE -->
		<div class="hc4-admin-page-header hc-lg-hide">
			<div class="hc4-submenu-mobile hc-nowrap">

			<?php if( $menuView && (count($menuView) > 2) ) : ?>
				<div class="hc-collapse-container hc-nowrap">
					<input type="checkbox" id="hc4-submenu-mobile" class="hc-collapse-toggler hc-hide">
					<label for="hc4-submenu-mobile" class="hc-collapse-burger hc-block">
						<div class="hc-xs-flex-grid">
							<div class="hc-xs-col hc-xs-col-10"><h1><?php echo $title; ?></h1></div>
							<div class="hc-xs-col hc-xs-col-2 hc-align-center">
								<h1 class="hc-inline-block hc-px2">&vellip;</h1>
							</div>
						</div>
					</label>

					<div class="hc-collapse-content">
						<div class="hc4-admin-list-primary">
						<?php foreach( $menuView as $menuItem ) : ?>
							<div>
								<?php echo $menuItem; ?>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
				</div>

			<?php else : ?>

				<?php if( strlen($title) ) : ?>
					<h1><?php echo $title; ?></h1>
				<?php endif; ?>

				<?php foreach( $menuView as $menuItem ) : ?>
					<div class="hc-my2"><?php echo $menuItem; ?></div>
				<?php endforeach; ?>

			<?php endif; ?>
			</div>
		</div>
		<!-- END OF MOBILE -->

		<!-- DESKTOP -->
		<?php if( $menuView OR strlen($title) ) : ?>
			<div class="hc4-admin-page-header hc-xs-hide">
				<?php if( (count($menuView) == 1) && strlen($title) && (strlen($title) < 30) ) : ?>
					<?php if( defined('WPINC') && is_admin() ) : ?>
						<h1 class="wp-heading-inline hc-inline-block" style="margin: 0 0 0 0; padding: 0 0 0 0;"><?php echo $title; ?></h1>
						<div class="hc-inline-block">
							<?php foreach( $menuView as $menuItem ) : ?>
								<?php echo $menuItem; ?>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div class="hc-xs-flex-grid">
							<div>
								<h1><?php echo $title; ?></h1>
							</div>
							<?php foreach( $menuView as $menuItem ) : ?>
								<div class="hc-mx1"><?php echo $menuItem; ?></div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				<?php else : ?>
					<?php if( strlen($title) ) : ?>
						<h1 style="margin: 0 0 0 0; padding: 0 0 0 0;"><?php echo $title; ?></h1>
					<?php endif; ?>

					<?php if( $menuView ) : ?>
						<div class="hc4-submenu-desktop">
							<div class="hc-xs-flex-grid">
								<?php $ii = 0; ?>
								<?php foreach( $menuView as $menuItem ) : ?>
									<?php if( 0 && $ii ) : ?>
										<div class="hc-mx1">&middot;</div>
									<?php endif; ?>
									<?php if( $ii ) : ?>
										<div class="hc-mx1">&nbsp;</div>
									<?php endif; ?>
									<div>
										<?php echo $menuItem; ?>
									</div>
									<?php $ii++; ?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<!-- END OF DESKTOP -->
	<?php endif; ?>

	<div class="hc4-admin-page-main">
		<?php if( $contentAsMenu ) : ?>
			<div class="hc4-admin-list-secondary">
			<?php foreach( $contentAsMenu as $menuItem ) : ?>
				<div>
					<?php echo $menuItem; ?>
				</div>
			<?php endforeach; ?>
			</div>
		<?php else : ?>
			<?php echo $content; ?>
		<?php endif; ?>
	</div>

</div>
</div>

<?php 
		$return = ob_get_clean();
		return $return;
	}

	public function renderLink( $to, $label, $htmlLabel = NULL )
	{
		$isFullUrl = HC4_App_Uri::isFullUrl( $to );
		ob_start();
?>
<?php if( $isFullUrl ) : ?>
<a class="hc4-admin-link-secondary" href="HREFGET:<?php echo $to; ?>" title="<?php echo $htmlLabel; ?>"><?php echo $label; ?></a>
<?php else : ?>
<a class="hc4-admin-link-secondary" href="HREFGET:<?php echo $to; ?>" data-ajax="1" title="<?php echo $htmlLabel; ?>"><?php echo $label; ?></a>
<?php endif; ?>
<?php 
		return ob_get_clean();
	}

	public function renderLink2( $to, $label, $htmlLabel = NULL )
	{
		$isFullUrl = HC4_App_Uri::isFullUrl( $to );
		ob_start();
?>
<?php if( $isFullUrl ) : ?>
<a class="hc4-admin-link-ternary" href="HREFGET:<?php echo $to; ?>" title="<?php echo $htmlLabel; ?>"><?php echo $label; ?></a>
<?php else : ?>
<a class="hc4-admin-link-ternary" href="HREFGET:<?php echo $to; ?>" data-ajax="1" title="<?php echo $htmlLabel; ?>"><?php echo $label; ?></a>
<?php endif; ?>
<?php 
		return ob_get_clean();
	}

	public function renderMenuItem( $slug, array $menuItem )
	{
		if( count($menuItem) == 1 ){
			$to = NULL;
			list( $label ) = $menuItem;
		}
		elseif( count($menuItem) == 3 ){
			list( $to, $null, $label ) = $menuItem;
			$method = 'POST';
		}
		else {
			list( $to, $label ) = $menuItem;
			$method = 'GET';
		}

		if( $to ){
			$to = str_replace( '{CURRENT}', $slug, $to );
			if( ! $this->app->check( $method, $to ) ){
				return;
			}
		}

		ob_start();
?>
	<?php if( count($menuItem) == 1 ) : ?>

		<?php echo $label; ?>

	<?php elseif( count($menuItem) == 3 ) : ?>

		<form method="post" action="HREFPOST:<?php echo $to; ?>">
		<?php if( FALSE === strpos($label, '<') ) : ?>
			<button class="hc4-admin-link-secondary hc-block" title="<?php echo $label; ?>"><?php echo $label; ?></button>
		<?php else : ?>
			<?php echo $label; ?>
		<?php endif; ?>
		</form>

	<?php else : ?>

		<?php if( is_array($label) ) : ?>
			<?php $linkLabel = array_shift($label); ?>
			<div class="hc-inline-block"><?php echo $this->renderLink( $to, $linkLabel, $label ); ?></div>
			<?php while( $more = array_shift($label) ) : ?>
				<div class="hc-inline-block"><?php echo $more; ?></div>
			<?php endwhile; ?>
		<?php elseif( FALSE === strpos($label, '<') ) : ?>
			<?php echo $this->renderLink( $to, $label, $label ); ?>
		<?php else : ?>
			<?php echo $label; ?>
		<?php endif; ?>

	<?php endif; ?>
<?php 
		return ob_get_clean();
	}
}