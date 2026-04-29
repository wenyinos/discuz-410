<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: newreply.inc.php,v $
	$Revision: 1.12 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$discuz_action = 12;

if(!$discuz_uid && !((!$forum['replyperm'] && $allowreply) || ($forum['replyperm'] && forumperm($forum['replyperm'])))) {
	showmessage('group_nopermission', NULL, 'NOPERM');
} elseif(empty($forum['allowreply'])) {
	if(!$forum['replyperm'] && !$allowreply) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	} elseif($forum['replyperm'] && !forumperm($forum['replyperm'])) {
		showmessage('post_forum_newreply_nopermission', NULL, 'HALTED');
	}
}

if(empty($thread)) {
	showmessage('thread_nonexistence');
} elseif($thread['price'] > 0) {
	if(!$discuz_uid) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	} elseif(!$forum['ismoderator'] && $thread['authorid'] != $discuz_uid) {
		$query = $db->query("SELECT tid FROM {$tablepre}paymentlog WHERE tid='$tid' AND uid='$discuz_uid'");
		if(!$db->num_rows($query)) {
			showmessage('undefined_action', NULL, 'HALTED');
		}
	}
}

checklowerlimit($replycredits);

if(!submitcheck('replysubmit', 0, $seccodecheck)) {

	if(isset($repquote)) {

		include_once language('misc');

		$query = $db->query("SELECT tid, fid, author, authorid, message, useip, dateline, anonymous FROM {$tablepre}posts WHERE pid='$repquote' AND invisible='0'");
		$thaquote = $db->fetch_array($query);
		if($thaquote['tid'] != $tid) {
			showmessage('undefined_action', NULL, 'HALTED');
		}

		$quotefid = $thaquote['fid'];
		$message = $thaquote['message'];

		if($bannedmessages && $thaquote['authorid']) {
			$query = $db->query("SELECT groupid FROM {$tablepre}members WHERE uid='{$thaquote['authorid']}'");
			$author = $db->fetch_array($query);
			if(!$author['groupid'] || $author['groupid'] == 4 || $author['groupid'] == 5) {
				$message = $language['post_banned'];
			}
		}

		$time = gmdate("$dateformat $timeformat", $thaquote['dateline'] + ($timeoffset * 3600));
		$message = preg_replace("/\[hide=?\d*\](.+?)\[\/hide\]/is", "[b]{$language['post_hidden']}[/b]", $message);
		$message = preg_replace("/(\[quote])(.*)(\[\/quote])/siU", "", $message);
		$message = preg_replace($language['post_edit_regexp'], '', $message);
		$message = cutstr(dhtmlspecialchars(preg_replace("/\[.+?\]/", '', $message)), 200);

		$thaquote['useip'] = substr($thaquote['useip'], 0, strrpos($thaquote['useip'], '.')).'.x';
		if($thaquote['author'] && $thaquote['anonymous']) {
		    $thaquote['author'] = "[i]Anonymous[/i]";
		} elseif(!$thaquote['author']) {
		    $thaquote['author'] = "[i]Guest[/i] from {$thaquote['useip']}";
		} else {
		    $thaquote['author'] = "[i]{$thaquote['author']}[/i]";
		}

		$language['post_reply_quote'] = dinterpolate($language[post_reply_quote]);
		$message = "[quote]{$language['post_reply_quote']}\n$message [/quote]\n";

	}

	if($thread['replies'] <= $ppp) {
		$postlist = array();
		$query = $db->query("SELECT p.* ".($bannedmessages ? ', m.groupid ' : '').
			"FROM {$tablepre}posts p ".($bannedmessages ? "LEFT JOIN {$tablepre}members m ON p.authorid=m.uid " : '').
			"WHERE p.tid='$tid' AND p.invisible='0' ORDER BY p.dateline DESC");
		while($post = $db->fetch_array($query)) {
			
			$post['thisbg'] = $thisbg = isset($thisbg) && $thisbg == 'altbg1' ? 'altbg2' : 'altbg1';
			$post['dateline'] = gmdate("$dateformat $timeformat", $post['dateline'] + $timeoffset * 3600);

			if($bannedmessages && ($post['authorid'] && (!$post['groupid'] || $post['groupid'] == 4 || $post['groupid'] == 5))) {
				include_once language('misc');
				$post['message'] = $language['post_banned'];
			} else {
				$post['message'] = preg_replace("/\[hide=?\d*\](.+?)\[\/hide\]/is", "[b]{$language['post_hidden']}[/b]", $post['message']);
				$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], $forum['jammer']);
			}

			$postlist[] = $post;
		}
	}

	include template('post_newreply');

} else {

	require_once DISCUZ_ROOT.'./include/forum.func.php';

	if($subject == '' && $message == '') {
		showmessage('post_sm_isnull');
	}

	if($thread['closed'] && !$forum['ismoderator']) {
		showmessage('post_thread_closed');
	}

	if($post_autoclose = checkautoclose()) {
		showmessage($post_autoclose);
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

	$attachment = ($allowpostattach && $attachments = attach_upload()) ? 1 : 0;

	$subscribed = $thread['subscribed'] && $timestamp - $thread['lastpost'] < 7776000;
	$newsubscribed = !empty($emailnotify) && $discuz_uid;

	if($subscribed && !$modnewreplies) {
		$db->query("UPDATE {$tablepre}subscriptions SET lastpost='$timestamp' WHERE tid='$tid' AND uid<>'$discuz_uid'", 'UNBUFFERED');
	}
	if($newsubscribed) {
		$db->query("REPLACE INTO {$tablepre}subscriptions (uid, tid, lastpost, lastnotify)
			VALUES ('$discuz_uid', '$tid', '".($modnewreplies ? $thread['lastpost'] : $timestamp)."', '$timestamp')", 'UNBUFFERED');
	}

	$author = !$isanonymous ? $discuz_user : '';
	$bbcodeoff = checkbbcodes($message, !empty($bbcodeoff));
	$smileyoff = checksmilies($message, !empty($smileyoff));
	$parseurloff = !empty($parseurloff);
	$htmlon = $allowhtml && !empty($htmlon) ? 1 : 0;
	$isanonymous = $isanonymous && $allowanonymous ? 1 : 0;

	$pinvisible = $modnewreplies ? 2 : 0;
	$db->query("INSERT INTO {$tablepre}posts (fid, tid, first, author, authorid, subject, dateline, message, useip, invisible, anonymous, usesig, htmlon, bbcodeoff, smileyoff, parseurloff, attachment)
			VALUES ('$fid', '$tid', '0', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$message', '$onlineip', '$pinvisible', '$isanonymous', '$usesig', '$htmlon', '$bbcodeoff', '$smileyoff', '$parseurloff', '$attachment')");
	$pid = $db->insert_id();

	if($attachment) {
		foreach($attachments as $attach) {
			$db->query("INSERT INTO {$tablepre}attachments (tid, pid, dateline, readperm, filename, description, filetype, filesize, attachment, downloads)
				VALUES ('$tid', '$pid', '$timestamp', '{$attach['perm']}', '{$attach['name']}', '{$attach['description']}', '{$attach['type']}', '{$attach['size']}', '{$attach['attachment']}', '0')");
		}
		updatecredits($discuz_uid, $creditspolicy['postattach'], count($attachments));
	}

	if($modnewreplies) {
		if($newsubscribed) {
			$db->query("UPDATE {$tablepre}threads SET subscribed='1' WHERE tid='$tid'", 'UNBUFFERED');
		}
		!$allowuseblog || empty($isblog) ? showmessage('post_reply_mod_succeed', "forumdisplay.php?fid=$fid") :
			showmessage('post_reply_mod_blog_succeed', "blog.php?tid=$tid&starttime=$starttime&endtime=$endtime&page=$page");
	} else {
		$db->query("UPDATE {$tablepre}threads SET lastposter='$author', lastpost='$timestamp', replies=replies+1 ".($attachment ? ', attachment=\'1\'' : '').", subscribed='".($subscribed || $newsubscribed ? 1 : 0)."' WHERE tid='$tid'", 'UNBUFFERED');

		updatepostcredits('+', $discuz_uid, $replycredits);

		$lastpost = "{$thread['tid']}\t".addslashes($thread['subject'])."\t$timestamp\t$author";
		$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost', posts=posts+1, todayposts=".todayposts()." WHERE fid='$fid'", 'UNBUFFERED');
		if($forum['type'] == 'sub') {
			$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost' WHERE fid='{$forum['fup']}'", 'UNBUFFERED');
		}

		!$allowuseblog || empty($isblog) ? showmessage('post_reply_succeed', "viewthread.php?tid=$tid&pid=$pid&page=".(@ceil(($thread['replies'] + 2) / $ppp))."&extra=$extra#pid$pid") :
			showmessage('post_reply_blog_succeed', "blog.php?tid=$tid&starttime=$starttime&endtime=$endtime&page=".(@ceil(($thread['replies'] + 1) / $ppp))."#bottom");
	}

}

?>