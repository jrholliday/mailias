<?php

// Set Maildir
$maildir = '/var/www/Maildir/new/';

// Hide mailbox list?
$hide_mailbox_list = false;

/*--------------------------------------------------------------------------*/

require_once('MimeMailParser.class.php');

function get_all_mailboxes()
{
	global $maildir;

	$users = array();
	foreach ( scandir($maildir) as $filename )
	{
		$email = htmlentities(file_get_contents($maildir . $filename));

		if ( preg_match('/Original-To: (.*)@(.*)/', $email, $addresses) )
		{
			if ( in_array($addresses[1], $users) == false )
			{
				$users[$addresses[1]]  = 1;
			}
			else
			{
				$users[$addresses[1]] += 1;
			}
		}
	}
	ksort($users);

	$xml = new SimpleXMLElement(
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<?xml-stylesheet type="text/xsl" href="web/mailias.xsl"?>' .
			'<Postoffice/>');

	foreach ( $users as $user => $count )
	{
		$node = $xml->addChild('Mailbox');
		$node->addChild('User', $user);
		$node->addChild('Count', $count);
	}
	Header('Content-type: text/xml; charset=UTF-8');
	print($xml->asXML());
}


function get_mailbox($user)
{
	global $maildir;

	$xml = new SimpleXMLElement(
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<?xml-stylesheet type="text/xsl" href="web/mailias.xsl"?>' .
			'<Mailbox/>');
	$xml->addChild('User', $user);

	foreach ( scandir($maildir) as $filename )
	{
		$email = htmlentities(file_get_contents($maildir . $filename));

		if ( preg_match("/Original-To: $user@mail.jamesandt.com/", $email) )
		{
			preg_match('/Date: (.*)/', $email, $date);
			preg_match('/From: (.*)/', $email, $from);
			preg_match('/Subject: (.*)/', $email, $subject);

			$node = $xml->addChild('Email');
			$node->addChild('From', $from[1]);
			$node->addChild('Subject', $subject[1]);
			$node->addChild('Date', $date[1]);
			$node->addChild('Link', $filename);
		}
	}
	Header('Content-type: text/xml; charset=UTF-8');
	print($xml->asXML());
}

function get_email($filename)
{
	global $maildir;

	class SimpleXMLExtended extends SimpleXMLElement {
		public function addCData($cdata_text) {
			$node = dom_import_simplexml($this);
			$no   = $node->ownerDocument;
			$node->appendChild($no->createCDATASection($cdata_text));
		}
	}

	$email = file_get_contents($maildir . $filename);

	$Parser = new MimeMailParser();
	$Parser->setText($email);

	$to = $Parser->getHeader('x-original-to');
	$from = $Parser->getHeader('from');
	$subject = $Parser->getHeader('subject');
	$date = $Parser->getHeader('date');
        $body = $Parser->getMessageBody('html') ?: $Parser->getMessageBody('text');

	$xml = new SimpleXMLExtended(
			'<?xml version="1.0"?>' .
			'<?xml-stylesheet type="text/xsl" href="../web/mailias.xsl"?>' .
			'<Message/>');

	$xml->addChild('To', $to);
	$xml->addChild('From', $from);
	$xml->addChild('Subject', $subject);
	$xml->addChild('Date', $date);
	$xml->addChild('Text')->addCData($body);
	$xml->addChild('Raw')->addCData($email);

	Header('Content-type: text/xml; charset=UTF-8');
	print($xml->asXML());
}

function delete_email($filename)
{
	global $maildir;

	$handle = realpath($maildir . $filename);

	if ( $handle !== ($maildir . $filename) )
	{
		// Can't have relative paths in the file name!
		return;
	}
	else
	{
	if ( file_exists($handle) === false )
		{
			// File not found!
			return;
		}
		else
		{
			unlink($handle);
		}
	}
}

function forward_email($filename, $to_address)
{
	global $maildir;

	$handle = realpath($maildir . $filename);

	if ( $handle !== ($maildir . $filename) )
	{
		// Can't have relative paths in the file name!
		return;
	}
	else
	{
	if ( file_exists($handle) === false )
		{
			// File not found!
			return;
		}
		else
		{
			$email = file_get_contents($handle);

			preg_match('/From:.*<(.*)>/', $email, $from);

			$lSmtpTalk = array(
				array('220', 'HELO '.$_SERVER['SERVER_NAME'].chr(10)),
				array('250', 'MAIL FROM: <' . $from[1] . '>'.chr(10)),
				array('250', 'RCPT TO: <' . $to_address . '>'.chr(10)),
				array('250', 'DATA'.chr(10)),
				array('354', $email.chr(10).'.'.chr(10)),
				array('250', 'QUIT'.chr(10)),
				array('221', ''));
			$lConnection = fsockopen('localhost', 25, $errno, $errstr, 1);
			if (!$lConnection) die('Cant relay, no connnection');
			for ($i=0;$i<count($lSmtpTalk);$i++) {
				$lRes = fgets($lConnection, 256);
				if (substr($lRes, 0, 3) !== $lSmtpTalk[$i][0])
					die('Got '.$lRes.' - expected: '.$lSmtpTalk[$i][0]);
				if ($lSmtpTalk[$i][1] !== '')
					fputs($lConnection, $lSmtpTalk[$i][1]);
			}
			fclose($lConnection);
		}
	}
}

/*--------------------------------------------------------------------------*/

$user = isset($_GET['user']) ? $_GET['user'] : '';
$mail = isset($_GET['id'])   ? $_GET['id']   : '';
$del  = isset($_GET['del'])  ? $_GET['del']  : '';
$push = isset($_GET['push']) ? $_GET['push'] : '';

if  ( $user != '' )
{
	if ( $mail != '' )
	{
		if ( $del == '1' )
		{
			delete_email($mail);
		}
		else if ( $push != '' )
		{
			forward_email($mail, $push);
		}
		else
		{
			get_email($mail);
		}
	}
	else
	{
		get_mailbox($user);
	}
}
else
{
	if ( $hide_mailbox_list == true )
	{
		$xml = new SimpleXMLElement(
				'<?xml version="1.0" encoding="UTF-8"?>' .
				'<?xml-stylesheet type="text/xsl" href="web/mailias.xsl"?>' .
				'<Mailias/>');

		Header('Content-type: text/xml; charset=UTF-8');
		print($xml->asXML());
	}
	else
	{
		get_all_mailboxes();
	}
}
?>
