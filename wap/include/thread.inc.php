<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: thread.inc.php,v $
	$Revision: 1.7 $
	$Date: 2006/02/28 05:38:32 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

$discuz_action = 193;

$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid='$tid' AND displayorder>='0'");
if(!$thread = $db->fetch_array($query)) {
	wapmsg('thread_nonexistence');
}

if(($thread['readperm'] && $thread['readperm'] > $readaccess && !$forum['ismoderator'] && $thread['authorid'] != $discuz_uid) || (empty($forum['allowview']) && ((!$forum['viewperm'] && !$readaccess) || ($forum['viewperm'] && !forumperm($forum['viewperm'])))) || $forum['password'] || $forum['redirect']) {
	wapmsg('thread_nopermission');
} elseif($thread['price'] > 0) {
	if($maxchargespan && $timestamp - $thread['dateline'] >= $maxchargespan * 3600) {
		$db->query("UPDATE {$tablepre}threads SET price='0' WHERE tid='$tid'");
		$thread['price'] = 0;
	} elseif(!$discuz_uid || (!$forum['ismoderator'] && $thread['authorid'] != $discuz_uid && !$db->num_rows($db->query("SELECT tid FROM {$tablepre}paymentlog WHERE tid='$tid' AND uid='$discuz_uid'")))) {
			wapmsg('thread_nopermission');
	}
}

$start = isset($start) ? intval($start) : 0;
$offset = isset($offset) ? intval($offset) : 0;

$breaked = 0;
$threadposts = '';

$query = $db->query("SELECT * FROM {$tablepre}posts
	WHERE tid='$tid' AND invisible='0'
	ORDER BY dateline LIMIT $start, $wapppp");

while($post = $db->fetch_array($query)) {
	if($offset > 0) {
		$post['message'] = '... '.substr($post['message'], $offset - 4);
	}
	if(strlen($threadposts) + strlen($post['message']) - $wapmps > 0) {
		$length = $wapmps - strlen($threadposts);
		$post['message'] = wapcutstr($post['message'], $length);
		$offset += $length;
		$breaked = 1;
	}
	$post['author'] = !$post['anonymous'] ? $post['author'] : $lang['anonymous'];
	
	$threadposts .= "<p>#".($start + 1)." $post[subject]<br /><small>".nl2br(dhtmlspecialchars(trim($post['message'])))."</small><br />\n".
		($breaked ? '' : gmdate("$wapdateformat $timeformat", $post['dateline'] + $timeoffset * 3600)."<br />\n$post[author]<br />").
		"</p>\n";

	if($breaked) {
		break;
	} else {
		$start++;
		$offset = 0;
	}
}

if($thread['displayorder'] > 0) {
	$query = $db->query("SELECT tid, subject FROM {$tablepre}threads
		WHERE fid='$fid' AND tid<>'$tid' AND
		((displayorder>0 AND displayorder<='$thread[displayorder]' AND lastpost<'$thread[lastpost]') || (displayorder=0))
		ORDER BY displayorder DESC, lastpost DESC LIMIT 1");
} else {
	$query = $db->query("SELECT tid, subject FROM {$tablepre}threads
		WHERE fid='$fid' AND tid<>'$tid' AND displayorder=0 AND lastpost<'$thread[lastpost]' 
		ORDER BY lastpost DESC LIMIT 1");
}

$next_thread = $db->fetch_array($query);

echo $threadposts."<p>#".($offset ? $start + 1 : $start).' '.
	($start - 1 == $thread['replies'] && !$offset ? $lang['end'] : "<a href=\"index.php?action=thread&amp;tid=$tid&amp;start=$start&amp;offset=$offset\">&gt;&gt;$lang[next_page]</a>")."<br />\n".
	($discuz_uid ? "<a href=\"index.php?action=post&amp;do=reply&amp;fid=$forum[fid]&amp;tid=$tid\">$lang[post_reply]</a><br /><a href=\"index.php?action=post&amp;do=newthread&amp;fid=$forum[fid]\">$lang[post_new]</a><br />\n" : '').
	"<br />$lang[forum]:<a href=\"index.php?action=forum&amp;fid=$forum[fid]\">".dhtmlspecialchars(cutstr(strip_tags($forum['name']), 20))."</a>\n".
	"<br />$lang[thread]:<a href=\"index.php?action=thread&amp;tid=$tid\">".cutstr($thread['subject'], 20)."</a>\n".
	($next_thread ? "<br />$lang[next_thread]:<a href=\"index.php?action=thread&amp;tid=$next_thread[tid]\">".cutstr($next_thread['subject'], 20)."</a>\n" : '').
	"</p>\n";

?>