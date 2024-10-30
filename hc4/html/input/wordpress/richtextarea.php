<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_WordPress_RichTextarea
	implements HC4_Html_Input_RichTextarea
{
	public function __construct(
		HC4_Html_Input_Helper $helper
	)
	{}

	public function render( $name, $value = NULL, $rows = 6 )
	{
		$value = $this->helper->getValue( $name, $value );

		// $out = '<input type="text" name="' . $name . '" value="' . $value . '" class="hc4-form-input">';

		$wpEditorSettings = array();
		$wpEditorSettings['textarea_name'] = $name;

		if( $rows ){
			$wpEditorSettings['textarea_rows'] = $rows;
		}

	// stupid wp, it outputs it right away
		ob_start();

		$editorId = $name;
		wp_editor(
			$value,
			$editorId,
			$wpEditorSettings
			);

		if( 0 ){
			$more_js = <<<EOT
<script type="text/javascript">
var str = nts_tinyMCEPreInit.replace(/nts_wp_editor/gi, '$editor_id');
var ajax_tinymce_init = JSON.parse(str);

tinymce.init( ajax_tinymce_init.mceInit['$editor_id'] );
</script>
EOT;

//				_WP_Editors::enqueue_scripts();
//				print_footer_scripts();
//				_WP_Editors::editor_js();
			echo $more_js;
		}

		$out = ob_get_clean();

		$out = $this->helper->afterRender( $name, $out );

		return $out;
	}

	public function render2()
	{
		$wpEditorSettings = array();
		$wpEditorSettings['textarea_name'] = $this->htmlName();

		$rows = $this->getAttr('rows');
		if( $rows ){
			$wpEditorSettings['textarea_rows'] = $rows;
		}

		// stupid wp, it outputs it right away
		ob_start();

		$editorId = $this->htmlId();
		wp_editor(
			$this->value,
			$editorId,
			$wpEditorSettings
			);

		if( 0 )
		{
			$more_js = <<<EOT
<script type="text/javascript">
var str = nts_tinyMCEPreInit.replace(/nts_wp_editor/gi, '$editor_id');
var ajax_tinymce_init = JSON.parse(str);

tinymce.init( ajax_tinymce_init.mceInit['$editor_id'] );
</script>
EOT;

//				_WP_Editors::enqueue_scripts();
//				print_footer_scripts();
//				_WP_Editors::editor_js();
			echo $more_js;
		}

		$out = ob_get_clean();
		return $out;




		$out = $this->htmlFactory->makeElement('input')
			->addAttr('type', 'text' )
			->addAttr('name', $this->htmlName() )
			->addAttr('class', 'hc-field')
			// ->addAttr('class', 'hc-block')
			->addAttr('class', 'hc-full-width')
			;

		if( strlen($this->value) ){
			$out->addAttr('value', $this->value);
		}

		$attr = $this->getAttr();
		foreach( $attr as $k => $v ){
			$out->addAttr( $k, $v );
		}

		$out->addAttr('id', $this->htmlId());

		if( strlen($this->label) ){
			$out
				->addAttr('placeholder', $this->label)
				;
		}

		if( $this->bold ){
			$out
				->addAttr('class', 'hc-fs5')
				;
		}

		if( strlen($this->label) && (! $this->bold) ){
			$label = $this->htmlFactory->makeElement('label', $this->label)
				->addAttr('for', $this->htmlId())
				->addAttr('class', 'hc-fs2')
				;
			$out = $this->htmlFactory->makeList( array($label, $out) );
			// $out = $this->htmlFactory->makeCollection( array($label, $out) );
		}

		return $out;
	}
}