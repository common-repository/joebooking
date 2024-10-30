<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Email_Wordpress
	implements HC4_Email_Interface
{
	protected $emailHtml = 1;
	public $from;
	public $fromName;

	public function __construct(
		HC4_Settings_Interface $settings
	)
	{
		$this->emailHtml = $settings->get( 'email_html', TRUE );
	}

	public function send( $to, $subj, $msg )
	{
		add_filter( 'wp_mail_content_type', array($this, '_setHtmlMailContentType') );
		if( $this->emailHtml ){
			$msg = nl2br( $msg );
		}
		@wp_mail( $to, $subj, $msg );
		remove_filter( 'wp_mail_content_type', array($this, '_setHtmlMailContentType') );
		return $this;
	}

	public function _setHtmlMailContentType()
	{
		$return = $this->emailHtml ? 'text/html' : 'text/plain';
		return $return;
	}
}