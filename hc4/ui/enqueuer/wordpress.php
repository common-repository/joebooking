<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Ui_Enqueuer_WordPress implements HC4_Ui_Enqueuer
{
	public function call( array $assetsCss, array $assetsJs )
	{
		$handleId = 1;

		foreach( $assetsCss as $src ){
			$handle = 'hc4-' . $handleId;
			wp_enqueue_style( $handle, $src );
			$handleId++;
		}

		foreach( $assetsJs as $src ){
			$handle = 'hc4-' . $handleId;
			wp_enqueue_script( $handle, $src );
			$handleId++;
		}
	}
}