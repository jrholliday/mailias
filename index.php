<?php
$maildir = '/var/www/Maildir/new/';

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

		if ( preg_match('/Original-To: (.*)@mail.jamesandt.com/', $email, $addresses) )
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

/*--------------------------------------------------------------------------*/

$user = isset($_GET['user']) ? $_GET['user'] : '';
$mail = isset($_GET['id'])   ? $_GET['id']   : '';
$del  = isset($_GET['del'])  ? $_GET['del']  : '';

if  ( $user != '' )
{
	if ( $mail != '' )
	{
		if ( $del == '1' )
		{
			delete_email($mail);
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
