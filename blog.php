<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: blog.php,v $
	$Revision: 1.5 $
	$Date: 2006/02/28 05:41:10 $
*/

define('CURSCRIPT', 'blog');

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/blog.func.php';
require_once DISCUZ_ROOT.'./include/discuzcode.func.php';

$discuz_action = 151;

$uid = isset($uid) ? intval($uid) : 0;
$starttime = isset($starttime) ? intval($starttime) : 0;
$endtime = isset($endtime) ? intval($endtime) : 0;

$page = empty($page) || !ispage($page) ? 1 : $page;
$start_limit = ($page - 1) * $ppp;

if(!empty($uid)) {

	$query = $db->query("SELECT uid, username, groupid FROM {$tablepre}members WHERE uid='$uid'");
	if(!$member = $db->fetch_array($query)) {
		showmessage('blog_nonexistence');
	}

	$uid = $member['uid'];
	$username = $member['username'];

	$fidadd = $fid ? "AND t.fid='$fid'" : '';
	$starttimeadd = $starttime ? "AND t.dateline>='$starttime'" : '';
	$endtimeadd = $endtime ? "AND t.dateline<'$endtime'" : '';

	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}threads t WHERE t.blog='1' AND t.authorid='$uid' $starttimeadd $endtimeadd $fidadd AND t.displayorder>='0'");
	$multipage = multi($db->result($query, 0), $ppp, $page, "blog.php?uid=$uid&fid=$fid&starttime=$starttime&endtime=$endtime");

	$bloglist = array();
	$query = $db->query("SELECT t.tid, t.fid, t.authorid, t.author, t.subject, t.dateline, t.views, t.replies, t.poll, t.attachment,
				f.name, f.allowsmilies, f.allowhtml, f.allowbbcode, f.allowimgcode, f.jammer,
				p.message, p.htmlon, p.smileyoff, p.bbcodeoff, p.rate, p.ratetimes
				FROM {$tablepre}threads t
				INNER JOIN {$tablepre}posts p ON p.tid=t.tid AND p.first='1'
				LEFT JOIN {$tablepre}forums f ON f.fid=t.fid
				WHERE t.blog='1' AND t.authorid='$uid' $starttimeadd $endtimeadd $fidadd AND t.displayorder>='0'
				ORDER BY t.dateline DESC LIMIT $start_limit, $ppp");

	$blognum = $db->num_rows($query);

	if(!$blognum) {

		$query = $db->query("SELECT username FROM {$tablepre}members WHERE uid='$uid'");
		if(!($username = $db->result($query, 0))) {
			showmessage('blog_nonexistence');
		}

		if($fid) {
			showmessage('blog_category_isnull');
		}

	} else {

		while($blog = $db->fetch_array($query)) {
			$blog['allowbbcode'] = $blog['allowbbcode'] ? ($_DCACHE['usergroups'][$member['groupid']]['allowcusbbcode'] ? 2 : 1) : 0;
			$blog['karma'] = karmaimg($blog['rate'], $blog['ratetimes']);
			$blog['postedon'] = gmdate($dateformat, $blog['dateline'] + $timeoffset * 3600);
			$blog['dateline'] = gmdate("$dateformat $timeformat", $blog['dateline'] + $timeoffset * 3600);
			$blog['message'] = discuzcode(cutstr($blog['message'], 800), $blog['smileyoff'], $blog['bbcodeoff'], $blog['htmlon'], $blog['allowsmilies'], $blog['allowbbcode'], $blog['allowimgcode'], $blog['allowhtml'], ($blog['jammer'] && $blog['authorid'] != $discuz_uid ? 1 : 0));

			$bloglist[] = $blog;
		}

	}

	$navigation = "&raquo; $username";
	$navtitle = " - $username";

} elseif(!empty($tid)) {

	$attachpids = '0';
	$query = $db->query("SELECT t.fid, t.authorid, t.author, t.replies, t.closed, m.groupid, p.pid, p.subject, p.message, p.htmlon, p.smileyoff, p.bbcodeoff, p.attachment, p.rate, p.ratetimes
		FROM {$tablepre}threads t
		INNER JOIN {$tablepre}posts p ON p.tid=t.tid AND p.invisible='0'
		LEFT JOIN {$tablepre}members m ON m.uid=t.authorid
		WHERE t.tid='$tid' AND t.blog='1' AND t.displayorder>='0' ORDER BY p.dateline LIMIT 1");

	if(!$blogtopic = $db->fetch_array($query)) {
		showmessage('blog_topic_nonexistence');
	}

	$uid = $blogtopic['authorid'];
	$username = $blogtopic['author'];
	$thisbg = 'altbg1';

	$navigation = "&raquo; <a href=\"blog.php?uid=$uid&starttime=$starttime&endtime=$endtime\">$username</a> &raquo; $blogtopic[subject]";
	$navtitle = " - $username - $blogtopic[subject]";

	$multipage = $multipage = multi($blogtopic['replies'], $ppp, $page, "blog.php?tid=$tid&starttime=$starttime&endtime=$endtime");

	$usesigcheck = $discuz_uid && $sigstatus ? 'checked' : '';
	$allowpostreply = (!$blogtopic['closed'] || $forum['ismoderator']) && ((!$forum['replyperm'] && $allowreply) || ($forum['replyperm'] && forumperm($forum['replyperm'])) || $forum['allowreply']);

	$forum['allowbbcode'] = $forum['allowbbcode'] ? ($_DCACHE['usergroups'][$blogtopic['groupid']]['allowcusbbcode'] ? 2 : 1) : 0;
	$blogtopic['message'] = discuzcode($blogtopic['message'], $blogtopic['smileyoff'], $blogtopic['bbcodeoff'], $blogtopic['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], ($forum['jammer'] && $blogtopic['authorid'] != $discuz_uid ? 1 : 0));
	$blogtopic['karma'] = karmaimg($blogtopic['rate'], $blogtopic['ratetimes']);

	$blogtopic['attachments'] = array();
	if($blogtopic['attachment'] && (($allowgetattach && !$forum['getattachperm']) || (!$allowgetattach && forumperm($forum['getattachperm'])))) {

		require_once DISCUZ_ROOT.'./include/attachment.func.php';

		$query = $db->query("SELECT aid, pid, readperm, filename, description, filetype, attachment, filesize, downloads
					FROM {$tablepre}attachments WHERE pid='$blogtopic[pid]'");
		while($attach = $db->fetch_array($query)) {
			$extension = strtolower(fileext($attach['filename']));
			$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
			$attach['attachsize'] = sizecount($attach['filesize']);
			$attach['attachimg'] = $attachimgpost && in_array($extension, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp')) ? 1 : 0;
			$blogtopic['attachments'][] = $attach;
		}

		$blogtopic['attachment'] = 0;

	}

	$commentlist = array();

	if($blogtopic['replies']) {

		$query = $db->query("SELECT p.*, m.username, m.groupid, m.regdate, m.posts, m.credits
					FROM {$tablepre}posts p
					LEFT JOIN {$tablepre}members m ON m.uid=p.authorid
					LEFT JOIN {$tablepre}memberfields mf ON mf.uid=m.uid
					WHERE p.tid='$tid' AND p.invisible='0' AND p.pid<>'$blogtopic[pid]'
					ORDER BY p.dateline
					LIMIT $start_limit, $ppp");

		while($comment = $db->fetch_array($query)) {

			$comment['thisbg'] = $thisbg = isset($thisbg) && $thisbg == 'altbg1' ? 'altbg2' : 'altbg1';
			$comment['dateline'] = gmdate("$dateformat $timeformat", $comment['dateline'] + $timeoffset * 3600);

			if($comment['attachment'] && $allowgetattach) {
				$attachpids .= ",$comment[pid]";
				$comment['attachment'] = 0;
			}

			$forum['allowbbcode'] = $forum['allowbbcode'] ? ($_DCACHE['usergroups'][$comment['groupid']]['allowcusbbcode'] ? 2 : 1) : 0;
			$comment['message'] = discuzcode($comment['message'], $comment['smileyoff'], $comment['bbcodeoff'], $comment['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], ($forum['jammer'] && $comment['authorid'] != $discuz_uid ? 1 : 0));

			if($comment['username']) {
				if($userstatusby == 1 || $_DCACHE['usergroups'][$comment['groupid']]['byrank'] === 0) {
					$comment['authortitle'] = strip_tags($_DCACHE['usergroups'][$comment['groupid']]['grouptitle']);
					$comment['stars'] = $_DCACHE['usergroups'][$comment['groupid']]['stars'];
				} elseif($userstatusby == 2) {
					foreach($_DCACHE['ranks'] as $rank) {
						if($comment['posts'] > $rank['postshigher']) {
							$comment['authortitle'] = $rank['ranktitle'];
							$comment['stars'] = $rank['stars'];
							break;
						}
					}
				}

				$comment['regdate'] = gmdate($dateformat, $comment['regdate'] + $timeoffset * 3600);

			} else {

				if(!$comment['authorid']) {
					$comment['useip'] = substr($comment['useip'], 0, strrpos($comment['useip'], '.')).'.x';
				}
				$comment['posts'] = $comment['credits'] = $comment['regdate'] = 'N/A';

			}

			$commentlist[$comment['pid']] = $comment;

		}

	}

} else {

	showmessage('undefined_action', NULL, 'HALTED');

}

if($discuz_uid && $uid == $discuz_uid && $allowpost && $allowuseblog) {
	require_once DISCUZ_ROOT.'./include/forum.func.php';
	$forumselect = $fid ? preg_replace("/(\<option value=\"$fid\")(\>)/", "\\1 selected=\"selected\" \\2", forumselect()) : forumselect();
} else {
	$allowuseblog = 0;
}

$whosonline = array();
$membercount = $guestcount = 0;
$query = $db->query("SELECT username FROM {$tablepre}sessions WHERE bloguid='$uid' AND invisible='0'");
while($online = $db->fetch_array($query)) {
	if($online['username']) {
		$membercount++;
		$whosonline[] = $online['username'];
	} else {
		$guestcount++;
	}
}
$onlinenum = $membercount + $guestcount;
$whosonline = dhtmlspecialchars(implode(', ', $whosonline));

$query = $db->query("SELECT variable, value FROM {$tablepre}blogcaches WHERE uid='$uid'");
while($cache = $db->fetch_array($query)) {
	$_DCACHE['blog'][$cache['variable']] = unserialize($cache['value']);
}

if($timestamp - $_DCACHE['blog']['forums']['lastupdate'] > 43200) {
	updateblogcache($uid, 'forums');
}

if($timestamp - $_DCACHE['blog']['hot']['lastupdate'] > 86400) {
	updateblogcache($uid, 'hot');
}

$bloguid = $uid;
$calendar = calendar($starttime);

include template('blog');

?>