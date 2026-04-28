<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: sendmail.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once DISCUZ_ROOT.'./mail_config.inc.php';
@include language('emails');

if($sendmail_silent) {
	error_reporting(0);
}

if(isset($language[$email_subject])) {
	eval("\$email_subject = \"".$language[$email_subject]."\";");
}
if(isset($language[$email_message])) {
	eval("\$email_message = \"".$language[$email_message]."\";");
}

$email_subject = str_replace("\r", '', str_replace("\n", '', $email_subject));
$email_message = str_replace("\r\n.", " \r\n..", str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $email_message)))));

if(!$email_from) {
	$email_from = "$bbname <$adminemail>";
}

if($mailsend == 1 && function_exists('mail')) {

	$email_message = str_replace("\r", '', $email_message);
	strpos($email_to, ',') ? @mail('Discuz! User <me@localhost>', $email_subject, $email_message, "From: $email_from\r\nBcc: $email_to") :
		@mail($email_to, $email_subject, $email_message, "From: $email_from");

} elseif($mailsend == 2) {

	if(!$fp = fsockopen($mailcfg['server'], $mailcfg['port'], $errno, $errstr, 30)) {
		errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) CONNECT - Unable to connect to the SMTP server, please check your \"mail_config.php\".", 0);
	}
 	stream_set_blocking($fp, true);

	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != '220') {
		errorlog('SMTP', "$mailcfg[server]:$mailcfg[port] CONNECT - $lastmessage", 0);
	}

	fputs($fp, ($mailcfg['auth'] ? 'EHLO' : 'HELO')." discuz\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
		errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) HELO/EHLO - $lastmessage", 0);
	}

	while(1) {
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
 			break;
 		}
	} 

	if($mailcfg['auth']) {
		fputs($fp, "AUTH LOGIN\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 334) {
			errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) AUTH LOGIN - $lastmessage", 0);
		}

		fputs($fp, base64_encode($mailcfg['auth_username'])."\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 334) {
			errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) USERNAME - $lastmessage", 0);
		}

		fputs($fp, base64_encode($mailcfg['auth_password'])."\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 235) {
			errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) PASSWORD - $lastmessage", 0);
		}

		$email_from = $mailcfg['from'];
	}

	fputs($fp, "MAIL FROM: ".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from)."\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) MAIL FROM - $lastmessage", 0);
		}
	}

	foreach(explode(',', $email_to) as $touser) {
		$touser = trim($touser);
		if($touser) {
			fputs($fp, "RCPT TO: $touser\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 250) {
				fputs($fp, "RCPT TO: <$touser>\r\n");
				$lastmessage = fgets($fp, 512);
				errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) RCPT TO - $lastmessage", 0);
			}
		}
	}

	fputs($fp, "DATA\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 354) {
		errorlog('SMTP', "($mailcfg[server]:$mailcfg[port]) DATA - $lastmessage", 0);
	}

 	fputs($fp, "To: $email_to\r\nFrom: $email_from\r\nSubject: ".str_replace("\n", ' ', $email_subject)."\r\n\r\n$email_message\r\n.\r\n"); 
	fputs($fp, "QUIT\r\n");

} elseif($mailsend == 3) {

	ini_set('SMTP', $mailcfg['server']);
	ini_set('smtp_port', $mailcfg['port']);
	ini_set('sendmail_from', $email_from);

	foreach(explode(',', $email_to) as $touser) {
		$touser = trim($touser);
		if($touser) {
			@mail($touser, $email_subject, $email_message, "From: $email_from");
		}
	}

}

?>