<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: newthread.inc.php,v $
	$Revision: 1.14.2.1 $
	$Date: 2006/03/23 06:05:28 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$discuz_action = 11;

if(empty($forum['fid']) || $forum['type'] == 'group') {
	showmessage('forum_nonexistence');
}

if(!$discuz_uid && !((!$forum['postperm'] && $allowpost) || ($forum['postperm'] && forumperm($forum['postperm'])))) {
	showmessage('group_nopermission', NULL, 'NOPERM');
} elseif(empty($forum['allowpost'])) {
	if(!$forum['postperm'] && !$allowpost) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	} elseif($forum['postperm'] && !forumperm($forum['postperm'])) {
		showmessage('post_forum_newthread_nopermission', NULL, 'HALTED');
	}
}

$isblog = empty($isblog) ? '' : 'yes';
if($isblog && (!$allowuseblog || !$forum['allowblog'])) {
	showmessage('post_newthread_blog_invalid', NULL, 'HALT');
}

checklowerlimit($postcredits);

if(!submitcheck('topicsubmit', 0, $seccodecheck)) {

	$typeselect = typeselect($typeid);

	$icons = '';
	if(is_array($_DCACHE['icons'])) {
		$key = 1;
		foreach($_DCACHE['icons'] as $id => $icon) {
			$icons .= ' <input type="radio" name="iconid" value="'.$id.'"><img src="'.SMDIR.'/'.$icon.'">';
			$icons .= !(++$key % 10) ? '<br>' : '';
		}
	}

	include template('post_newthread');

} else {

	if($subject == '' || $message == '') {
		showmessage('post_sm_isnull');
	}

	if($post_invalid = checkpost()) {
		showmessage($post_invalid);
	}

	if(checkflood()) {
		showmessage('post_flood_ctrl');
	}

	if($allowpostattach && is_array($_FILES['attach'])) {
		foreach($_FILES['attach']['name'] as $attachname) {
			if($attachname != '') {
				checklowerlimit($creditspolicy['postattach']);
				break;
			}
		}
	}

	$typeid = isset($forum['threadtypes']['types'][$typeid]) ? $typeid : 0;
	$iconid = !empty($iconid) && isset($_DCACHE['icons'][$iconid]) ? $iconid : 0;
	$displayorder = $modnewthreads ? -2 : (($forum['ismoderator'] && !empty($sticktopic)) ? 1 : 0);
	$digest = ($forum['ismoderator'] && !empty($addtodigest)) ? 1 : 0;
	$blog = $allowuseblog && $forum['allowblog'] && !empty($addtoblog) ? 1 : 0;
	$readperm = $allowsetreadperm ? $readperm : 0;
	$price = $maxprice ? ($price <= $maxprice ? $price : $maxprice) : 0;
	$isanonymous = $isanonymous && $allowanonymous ? 1 : 0;

	if(!$typeid && $forum['threadtypes']['required']) {
		showmessage('post_type_isnull');
	}

	if($price > 0 && floor($price * (1 - $creditstax)) == 0) {
		showmessage('post_net_price_iszero');
	}

	if(isset($poll) && $allowpostpoll && trim($polloptions)) {
		$poll = 1;
		$pollarray = array();
		$polloptions = explode("\n", $polloptions);
		if(count($polloptions) > $maxpolloptions) {
			showmessage('post_poll_option_toomany');
		}

		foreach($polloptions as $polloption) {
			$polloption = trim($polloption);
			if($polloption) {
				$pollarray['options'][] = array($polloption, 0);
			}
		}
		$pollarray['multiple'] = !empty($multiplepoll);
		$pollarray['voters'] = array();
		$pollopts = addslashes(serialize($pollarray));
	} elseif(isset($trade) && $allowposttrade && !empty($seller) && !empty($item_name) && !empty($item_price)) {
		include language('misc');

		$iconid = 25;

		switch($transport) {
			case 'seller':	$item_transport = 1; break;
			case 'virtual': $item_transport = 3; break;
			default:	$item_transport = 2; break;
		}

		$message = "[b]$language[post_trade_seller]:[/b] $seller\r\n\r\n".
			"[b]$language[post_trade_name]:[/b] $item_name\r\n\r\n".
			"[b]$language[post_trade_price]:[/b] $item_price $language[post_trade_yuan]\r\n\r\n".
			(!empty($item_quality) ? "[b]$language[post_trade_quality]:[/b] $item_quality\r\n\r\n" : '').
			(!empty($item_locus) ? "[b]$language[post_trade_locus]:[/b] $item_locus\r\n\r\n" : '').
			"[b]$language[post_trade_transport]:[/b] ".$language['post_trade_transport_'.$transport].($transport == 'buyer' ? (!empty($postage_mail) ? ", $language[post_trade_transport_mail] $postage_mail $language[post_trade_yuan]" : '').(!empty($postage_express) ? ", $language[post_trade_transport_express] $postage_express $language[post_trade_yuan]" : '') : '')."\r\n\r\n".
			"[b]$language[post_trade_description]:[/b] $message\r\n\r\n".
			"[payto]\r\n".
			"(seller)$seller(/seller)\r\n".
			"(subject)$item_name(/subject)\r\n".
			"(body)".cutstr($message, 400)."(/body)\r\n".
			"(price)$item_price(/price)\r\n".
			"(transport)$item_transport(/transport)\r\n".
			"(ordinary_fee)$postage_mail(/ordinary_fee)\r\n".
			"(express_fee)$postage_express(/express_fee)\r\n".
			"[/payto]";
	} else {
		$poll = 0;
		$pollopts = '';
	}

	$author = !$isanonymous ? $discuz_user : '';
	$moderated = $digest || $displayorder > 0 ? 1 : 0;
	$attachment = ($allowpostattach && $attachments = attach_upload()) ? 1 : 0;
	$subscribed = !empty($emailnotify) && $discuz_uid ? 1 : 0;

	$db->query("INSERT INTO {$tablepre}threads (fid, readperm, price, iconid, typeid, author, authorid, subject, dateline, lastpost, lastposter, displayorder, digest, blog, poll, attachment, subscribed, moderated)
		VALUES ('$fid', '$readperm', '$price', '$iconid', '$typeid', '$author', '$discuz_uid', '$subject', '$timestamp', '$timestamp', '$author', '$displayorder', '$digest', '$blog', '$poll', '$attachment', '$subscribed', '$moderated')");
	$tid = $db->insert_id();

	if($subscribed) {
		$db->query("REPLACE INTO {$tablepre}subscriptions (uid, tid, lastpost, lastnotify)
			VALUES ('$discuz_uid', '$tid', '$timestamp', '$timestamp')", 'UNBUFFERED');
	}

	if($moderated) {
		updatemodlog($tid, ($displayorder > 0 ? 'STK' : 'DIG'));
		updatemodworks(($displayorder > 0 ? 'STK' : 'DIG'), 1);
	}

	if($poll) {
		$db->query("INSERT INTO {$tablepre}polls (tid, pollopts)
			VALUES ('$tid', '$pollopts')");
	}

	$bbcodeoff = checkbbcodes($message, !empty($bbcodeoff));
	$smileyoff = checksmilies($message, !empty($smileyoff));
	$parseurloff = !empty($parseurloff);
	$htmlon = $allowhtml && !empty($htmlon) ? 1 : 0;

	$pinvisible = $modnewthreads ? -2 : 0;
	$db->query("INSERT INTO {$tablepre}posts (fid, tid, first, author, authorid, subject, dateline, message, useip, invisible, anonymous, usesig, htmlon, bbcodeoff, smileyoff, parseurloff, attachment)
		VALUES ('$fid', '$tid', '1', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$message', '$onlineip', '$pinvisible', '$isanonymous', '$usesig', '$htmlon', '$bbcodeoff', '$smileyoff', '$parseurloff', '$attachment')");
	$pid = $db->insert_id();

	if($attachment) {
		foreach($attachments as $attach) {
			$db->query("INSERT INTO {$tablepre}attachments (tid, pid, dateline, readperm, filename, description, filetype, filesize, attachment, downloads)
				VALUES ('$tid', '$pid', '$timestamp', '$attach[perm]', '$attach[name]', '$attach[description]', '$attach[type]', '$attach[size]', '$attach[attachment]', '0')");
		}
		updatecredits($discuz_uid, $creditspolicy['postattach'], count($attachments));
	}

	if($modnewthreads) {

		$allowuseblog && $isblog && $blog ? showmessage('post_newthread_mod_blog_succeed', "blog.php?uid=$discuz_uid") :
			showmessage('post_newthread_mod_succeed', "forumdisplay.php?fid=$fid");

	} else {

		if($digest) {
			foreach($creditspolicy['digest'] as $id => $addcredits) {
				$forum['postcredits'][$id] = (isset($forum['postcredits'][$id]) ? $forum['postcredits'][$id] : 0) + $addcredits;
			}
		}
		updatepostcredits('+', $discuz_uid, $postcredits);

		$lastpost = "$tid\t$subject\t$timestamp\t$author";
		$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost', threads=threads+1, posts=posts+1, todayposts=".todayposts()." WHERE fid='$fid'", 'UNBUFFERED');
		if($forum['type'] == 'sub') {
			$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost' WHERE fid='$forum[fup]'", 'UNBUFFERED');
		}

		$allowuseblog && $isblog && $blog ? showmessage('post_newthread_blog_succeed', "blog.php?tid=$tid") :
			showmessage('post_newthread_succeed', "viewthread.php?tid=$tid&extra=$extra");

	}

}

?>