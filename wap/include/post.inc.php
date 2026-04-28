<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: post.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

require_once DISCUZ_ROOT.'./forumdata/cache/cache_bbcodes.php';
require_once DISCUZ_ROOT.'./include/discuzcode.func.php';
require_once DISCUZ_ROOT.'./include/post.func.php';
require_once DISCUZ_ROOT.'./include/forum.func.php';

if(empty($forum) || $forum['type'] == 'group') {
	wapmsg('forum_nonexistence');
}

if(empty($forum['allowview']) && ((!$forum['viewperm'] && !$readaccess) || ($forum['viewperm'] && !forumperm($forum['viewperm'])))) {
	wapmsg('forum_nopermission');
}

if(empty($bbcodeoff) && !$allowhidecode && preg_match("/\[hide=?\d*\].+?\[\/hide\]/is", preg_replace("/(\[code\].*\[\/code\])/is", '', $message))) {
	wapmsg('post_hide_nopermission');
}

if(!$adminid && $newbiespan && (!$lastpost || $timestamp - $lastpost < $newbiespan * 3600)) {
	$query = $db->query("SELECT regdate FROM {$tablepre}members WHERE uid='$discuz_uid'");
	if($timestamp - ($db->result($query, 0)) < $newbiespan * 3600) {
		showmessage('post_newbie_span');
	}
}

$postcredits = $forum['postcredits'] ? $forum['postcredits'] : $creditspolicy['post'];
$replycredits = $forum['replycredits'] ? $forum['replycredits'] : $creditspolicy['reply'];

$modnewthreads = (!$allowdirectpost || $allowdirectpost == 1) && ($forum['modnewposts'] || $censormod) ? 1 : 0;
$modnewreplies = (!$allowdirectpost || $allowdirectpost == 2) && ($forum['modnewposts'] == 2 || $censormod) ? 1 : 0;

$subject = isset($subject) ? dhtmlspecialchars(censor(trim($subject))) : '';
$message = isset($message) ? censor(trim($message)) : '';

if($do == 'newthread') {

	$discuz_action = 195;

	if(empty($forum['allowpost']) && ((!$forum['postperm'] && !$allowpost) || ($forum['postperm'] && !forumperm($forum['postperm'])))) {
		wapmsg('post_newthread_nopermission');
	}

	if(empty($subject) || empty($message)) {

		$typeselect = $forum['threadtypes']['required'] ? typeselect() : '';
		echo ($typeselect ? "<p>$lang[type]:$typeselect</p>\n" : '').
			"<p>$lang[subject]:<input type=\"text\" name=\"subject\" value=\"\" maxlength=\"80\" format=\"M*m\" /></p>\n".
			"<p>$lang[message]:<input type=\"text\" name=\"message\" value=\"\" format=\"M*m\" /></p>\n".
			"<p><anchor title=\"$lang[submit]\">$lang[submit]".
			"<go method=\"post\" href=\"index.php?action=post&amp;do=newthread&amp;fid=$fid&amp;sid=$sid\">\n".
			"<postfield name=\"subject\" value=\"$(subject)\" />\n".
			"<postfield name=\"message\" value=\"$(message)\" />\n".
			($typeselect ? "<postfield name=\"typeid\" value=\"$(typeid)\" />\n" : '').
			"</go></anchor></p>\n";

	} else {

		if($post_invalid = checkpost()) {
			wapmsg($post_invalid);
		}
		if(checkflood()) {
			wapmsg('post_flood_ctrl');
		}

		$typeid = isset($forum['threadtypes']['types'][$typeid]) ? $typeid : 0;
		if(!$typeid && $forum['threadtypes']['required']) {
			wapmsg('post_type_isnull');
		}

		$displayorder = $pinvisible = $modnewthreads ? -2 : 0;
		$db->query("INSERT INTO {$tablepre}threads (fid, readperm, iconid, typeid, author, authorid, subject, dateline, lastpost, lastposter, displayorder, digest, blog, poll, attachment, moderated)
			VALUES ('$fid', '0', '0', '$typeid', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$timestamp', '$discuz_user', '$displayorder', '0', '0', '0', '0', '0')");
		$tid = $db->insert_id();

		$db->query("INSERT INTO {$tablepre}posts (fid, tid, first, author, authorid, subject, dateline, message, useip, invisible, usesig, htmlon, bbcodeoff, smileyoff, parseurloff, attachment)
			VALUES ('$fid', '$tid', '1', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$message', '$onlineip', '$pinvisible', '0', '0', '0', '0', '0', '0')");
		$pid = $db->insert_id();

		if($modnewthreads) {
			wapmsg('post_mod_succeed', array('title' => 'post_mod_forward', 'link' => "index.php?action=forum&amp;tid=$fid"));
		} else {
			updatepostcredits('+', $discuz_uid, $postcredits);

			$lastpost = "$tid\t$subject\t$timestamp\t$discuz_user";
			$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost', threads=threads+1, posts=posts+1, todayposts=".todayposts()." WHERE fid='$fid'", 'UNBUFFERED');
			if($forum['type'] == 'sub') {
				$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost' WHERE fid='$forum[fup]'", 'UNBUFFERED');
			}

			wapmsg('post_newthread_succeed', array('title' => 'post_newthread_forward', 'link' => "index.php?action=thread&amp;tid=$tid"));

		}
	}

} elseif($do == 'reply') {

	$discuz_action = 196;

	$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid='$tid'");
	if(!$thread = $db->fetch_array($query)) {
		wapmsg('thread_nonexistence');
	}

	if(empty($forum['allowreply']) && ((!$forum['replyperm'] && !$allowreply) || ($forum['replyperm'] && !forumperm($forum['replyperm'])))) {
		wapmsg('post_newreply_nopermission');
	}

	if($thread['closed'] && !$forum['ismoderator']) {
		wapmsg('post_thread_closed');
	}
	if($post_autoclose = checkautoclose()) {
		wapmsg($post_autoclose);
	}

	if(empty($subject) && empty($message)) {

		echo "<p>$lang[subject]:<input type=\"text\" name=\"subject\" value=\"\" maxlength=\"80\" /></p>\n".
			"<p>$lang[message]:<input type=\"text\" name=\"message\" value=\"\" format=\"M*m\" /></p>\n".
			"<p><anchor title=\"$lang[submit]\">$lang[submit]".
			"<go method=\"post\" href=\"index.php?action=post&amp;do=reply&amp;fid=$fid&amp;tid=$tid&amp;sid=$sid\">\n".
			"<postfield name=\"subject\" value=\"$(subject)\" />\n".
			"<postfield name=\"message\" value=\"$(message)\" />\n".
			"</go></anchor></p>\n";

	} else {

		if($subject == '' && $message == '') {
			wapmsg('post_sm_isnull');
		}
		if($post_invalid = checkpost()) {
			wapmsg($post_invalid);
		}
		if(checkflood()) {
			wapmsg('post_flood_ctrl');
		}
	
		$pinvisible = $modnewreplies ? 2 : 0;
		$db->query("INSERT INTO {$tablepre}posts (fid, tid, first, author, authorid, subject, dateline, message, useip, invisible, usesig, htmlon, bbcodeoff, smileyoff, parseurloff, attachment)
				VALUES ('$fid', '$tid', '0', '$discuz_user', '$discuz_uid', '$subject', '$timestamp', '$message', '$onlineip', '$pinvisible', '1', '0', '0', '0', '0', '0')");
		$pid = $db->insert_id();
	
		if($modnewreplies) {
			wapmsg('post_mod_succeed', array('title' => 'post_mod_forward', 'link' => "index.php?action=forum&amp;fid=$fid"));
		} else {
			$db->query("UPDATE {$tablepre}threads SET lastposter='$discuz_user', lastpost='$timestamp', replies=replies+1 WHERE tid='$tid' AND fid='$fid'", 'UNBUFFERED');
	
			updatepostcredits('+', $discuz_uid, $replycredits);
	
			$lastpost = "$thread[tid]\t".addslashes($thread['subject'])."\t$timestamp\t$discuz_user";
			$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost', posts=posts+1, todayposts=".todayposts()." WHERE fid='$fid'", 'UNBUFFERED');
			if($forum['type'] == 'sub') {
				$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost' WHERE fid='$forum[fup]'", 'UNBUFFERED');
			}
	
			wapmsg('post_newreply_succeed', array('title' => 'post_newreply_forward', 'link' => "index.php?action=thread&amp;tid=$tid&amp;page=".(@ceil(($thread['replies'] + 2) / $wapppp))));
		}

	}

}



?>