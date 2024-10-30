<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Html_Input_Boot
	implements HC4_App_Module_Interface
{
	public static function bind( array $appConfig )
	{
		$bind = array();

		switch( $appConfig['platform'] ){
			case 'standalone':
				$bind['HC4_Html_Input_RichTextarea'] = 'HC4_Html_Input_Textarea';
				break;

			case 'joomla':
				$bind['HC4_Html_Input_RichTextarea'] = 'HC4_Html_Input_Textarea';
				break;

			case 'wordpress':
				$bind['HC4_Html_Input_RichTextarea'] = 'HC4_Html_Input_WordPress_RichTextarea';
				break;
		}

		return $bind;
	}
}