<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_04Notifications_Service_Sender
{
	protected $_queue = array();
	protected $_users = array();
	protected $_senders = array();

	public function __construct(
		HC4_Session_Interface $session,
		HC4_Translate_Interface $translate,

		JB7_04Notifications_Service_Sender_Database $senderDatabase,
		JB7_04Notifications_Service_Sender_Email $senderEmail,
		JB7_04Notifications_Service_Sender_SMS $senderSms
	)
	{
		$this->_senders = array(
			'database'	=> $senderDatabase,
			'email'		=> $senderEmail,
			'sms'			=> $senderSms,
			);
	}

	public function send(
		$user,
		JB7_04Notifications_Service_Message $msg
	)
	{
		$idArray = explode( '-', $msg->id );
		$senderId = $idArray[0];

		if( ! isset($this->_senders[$senderId]) ){
			echo __CLASS__ . ' has no sender for ' . $msg->id;
			return;
		}

		$userKey = $user->{$senderId};

		$queueKey = $msg->id . ':' . $userKey;
		if( ! isset($this->_queue[$queueKey]) ){
			$this->_queue[$queueKey] = array();
		}
		$this->_queue[$queueKey][] = $msg;
		$this->_users[$userKey] = $user;
	}

	public function sendQueued()
	{
		if( ! $this->_queue ){
			return;
		}

		foreach( $this->_queue as $msgIdUserId => $messages ){
			if( ! $messages ){
				continue;
			}

			list( $msgId, $userId ) = explode( ':', $msgIdUserId );

			$count = count( $messages );

			$body = array();
			foreach( $messages as $msg ){
				$body[] = $msg->body;
			}
			$body = join( "\n\n", $body );

			$subject = $msg->subject;
			if( $count > 1 ){
				$subject = $subject . ' (' . $count . ')';
			}

			$subject = $this->translate->translate( $subject );
			$body = $this->translate->translate( $body );

			$bulkMessage = new JB7_04Notifications_Service_Message( $msg->id, $subject, $body );
			$user = $this->_users[ $userId ];

			$this->realSend( $user, $bulkMessage );
		}
	}

	public function realSend(
		$user,
		JB7_04Notifications_Service_Message $msg
	)
	{
		$idArray = explode( '-', $msg->id );
		$senderId = $idArray[0];

		if( ! isset($this->_senders[$senderId]) ){
			echo __CLASS__ . ' has no sender for ' . $msg->id;
			return;
		}

		if( defined('HC4_DEBUG_NOTIFICATIONS') && HC4_DEBUG_NOTIFICATIONS ){
			$this->sendDebug( $user, $msg );
		}
		if( defined('HC4_DEV_INSTALL') && HC4_DEV_INSTALL ){
			return;
		}

		$sender = $this->_senders[$senderId];
		$sender->send( $user, $msg );
	}

	public function sendDebug(
		$user,
		JB7_04Notifications_Service_Message $msg
	)
	{
		$alert = 'MESSAGE ' . $msg->id . ' TO ' . $user->title;
		$log = array( $msg->id, $user->title, $msg->subject, $msg->body );

		$this->session->addFlashdata( 'debug', $alert );
		$this->log( $log );
	}

	public function log( array $log )
	{
		$now = time();
		$date = date( "F j, Y, g:i a", $now );

		$out = array();
		$out[] = $date;
		$out = array_merge( $out, $log );
		// $out[] = '';
		$out = join( "\n", $out );

		if( ! defined('HC4_ROOT_PATH') ){
			return;
		}

		$outFile = HC4_ROOT_PATH . '/emaillog.txt';
		$fp = fopen( $outFile, 'a' );
		fwrite( $fp, $out . "\n\n" );
		fclose( $fp );
	}
}