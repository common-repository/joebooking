<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_MultiSet
{
	public function __construct(
		HC4_Html_Input_Helper $helper,
		HC4_Html_Input_RadioSet $inputRadioSet
	)
	{}

	public function render( $name, array $labels, array $options, $value = NULL )
	{
		$myId = 'hc4-input-multiset-' . HC4_App_Functions::generateRand(2);

		$value = $this->helper->getValue( $name, $value );

		$out = array();
		$out[] = $this->inputRadioSet->renderInline( $name, $labels, $value );

		foreach( $options as $k => $v ){
			$out[] = '<div class="hc4-input-multiset-detail hc4-input-multiset-detail-' . $k . '">';
			$out[] = $v;
			$out[] = '</div>';
		}

		$out = join( '', $out );
		$out = $this->helper->afterRender( $name, $out );

		ob_start();
?>

<div id="<?php echo $myId; ?>">
<?php echo $out; ?>
</div>

<script>
( function(){

function MultiSetInput( el ){
	var ii = 0, jj = 0;
	var togglers = el.getElementsByClassName( 'hc4-input-radio' );
	var details = el.getElementsByClassName( 'hc4-input-multiset-detail' );

	this.render = function(){
		for( ii = 0; ii < details.length; ii++ ){
			details[ii].style.display = 'none';
		}

		for( ii = 0; ii < togglers.length; ii++ ){
			if( togglers[ii].checked ){
				var showClass = 'hc4-input-multiset-detail-' + togglers[ii].value;
				var showDetails = el.getElementsByClassName( showClass );
				for( jj = 0; jj < showDetails.length; jj++ ){
					showDetails[jj].style.display = 'block';
				}
			}
		}
	};

	for( ii = 0; ii < togglers.length; ii++ ){
		// togglers[ii].addEventListener( 'RadioStateChange', this.render );
		togglers[ii].addEventListener( 'change', this.render );
	}
};

var el = new MultiSetInput( document.getElementById('<?php echo $myId; ?>') );
el.render();

})();
</script>


<?php 
		return ob_get_clean();
	}
}