<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: viewthread.php,v $
	$Revision: 1.26.2.3 $
	$Date: 2006/03/23 02:45:00 $
*/

define('CURSCRIPT', 'viewthread');

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/forum.func.php';
require_once DISCUZ_ROOT.'./include/discuzcode.func.php';

$discuz_action = 3;

$query = $db->query("SELECT * FROM {$tablepre}threads t WHERE tid='$tid' AND displayorder>='0'");

$lastmod = array();
if(!$thread = $db->fetch_array($query)) {
	showmessage('thread_nonexistence');
} elseif($thread['moderated']) {
	$query = $db->query("SELECT uid AS moduid, username AS modusername, dateline AS moddateline, action AS modaction
		FROM {$tablepre}threadsmod
		WHERE tid='$tid' ORDER BY dateline DESC LIMIT 1");
	if($lastmod = $db->fetch_array($query)) {
		include language('modactions');
		$lastmod['modusername'] = $lastmod['modusername'] ? $lastmod['modusername'] : 'System';
		$lastmod['moddateline'] = gmdate("$dateformat $timeformat", $lastmod['moddateline'] + $timeoffset * 3600);
		$lastmod['modaction'] = $modactioncode[$lastmod['modaction']];
	} else {
		$db->query("UPDATE {$tablepre}threads SET moderated='0' WHERE tid='$tid'", 'UNBUFFERED');
	}
}

$codecount = 0;
$thread['subjectenc'] = rawurlencode($thread['subject']);

$oldtopics = isset($_DCOOKIE['oldtopics']) ? $_DCOOKIE['oldtopics'] : 'D';
if(strpos($oldtopics, 'D'.$tid.'D') === FALSE) {
	$oldtopics = 'D'.$tid.$oldtopics;
	if(strlen($oldtopics) > 3072) {
		$oldtopics = preg_replace("((D\d+)+D).*$", "\\1", substr($oldtopics, 0, 3072));
	}
	dsetcookie('oldtopics', $oldtopics, 3600);
}

if($lastvisit < $thread['lastpost'] && (!isset($_DCOOKIE['f'.$fid]) || $thread['lastpost'] > $_DCOOKIE['f'.$fid])) {
	dsetcookie('fid'.$fid, $thread['lastpost'], 3600);
}

$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid".($extra ? '&'.preg_replace("/^(&)*/", '', $extra) : '')."\">$forum[name]</a> &raquo; $thread[subject]";
$navtitle = ' - '.strip_tags($forum['name']).' - '.$thread['subject'];
if($forum['type'] == 'sub') {
	$query = $db->query("SELECT fid, name FROM {$tablepre}forums WHERE fid='$forum[fup]'");
	$fup = $db->fetch_array($query);
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> $navigation";
	$navtitle = ' - '.strip_tags($fup['name']).$navtitle;
}

if($thread['typeid'] && isset($forum['threadtypes']['types'][$thread['typeid']])) {
	$thread['subject'] = ($forum['threadtypes']['listable'] ? '<a href="forumdisplay.php?fid='.$fid.'&filter=type&typeid='.$thread['typeid'].'">['.$forum['threadtypes']['types'][$thread['typeid']].']</a>' : '['.$forum['threadtypes']['types'][$thread['typeid']].']').' '.$thread['subject'];
}

if(empty($forum['allowview'])) {
	if(!$forum['viewperm'] && !$readaccess) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	} elseif($forum['viewperm'] && !forumperm($forum['viewperm'])) {
		showmessage('forum_nopermission', NULL, 'NOPERM');
	}
}

if($thread['readperm'] && $thread['readperm'] > $readaccess && !$forum['ismoderator'] && $thread['authorid'] != $discuz_uid) {
	showmessage('thread_nopermission', NULL, 'NOPERM');
}

if($thread['price'] > 0) {
	if($maxchargespan && $timestamp - $thread['dateline'] >= $maxchargespan * 3600) {
		$db->query("UPDATE {$tablepre}threads SET price='0' WHERE tid='$tid'");
		$thread['price'] = 0;
	} else {
		if(!$discuz_uid) {
			showmessage('group_nopermission', NULL, 'NOPERM');
		} elseif(!$forum['ismoderator'] && $thread['authorid'] != $discuz_uid) {
			$query = $db->query("SELECT tid FROM {$tablepre}paymentlog WHERE tid='$tid' AND uid='$discuz_uid'");
			if(!$db->num_rows($query)) {
				require_once DISCUZ_ROOT.'./include/threadpay.inc.php';
				exit();
			}
		}
	}
}

if($forum['password'] && $forum['password'] != $_DCOOKIE['fidpw'.$fid]) {
	header("Location: {$boardurl}forumdisplay.php?fid=$fid&sid=$sid");
	exit();
}

$raterange = $modratelimit && $adminid == 3 && !$forum['ismoderator'] ? array() : $raterange;

$extra = rawurlencode($extra);
$allowgetattach = !empty($forum['allowgetattach']) || ($allowgetattach && !$forum['getattachperm']) || forumperm($forum['getattachperm']);

if(empty($action) && $tid) {

	if($discuz_uid && $newpm) {
		require_once DISCUZ_ROOT.'./include/pmprompt.inc.php';
	}

	//get trade thread status (pos. -1)
	$allowposttrade = substr(sprintf('%02b', $forum['allowtrade']), -1, 1);

	//get pay to author status (pos. -2)
	$allowpaytoauthor = substr(sprintf('%02b', $forum['allowtrade']), -2, 1);

	//get qihoo status (pos. -3)
	$searchboxstatus = substr(sprintf('%03b', $qihoo_searchbox), -3, 1);

	$highlightstatus = isset($highlight) && str_replace('+', '', $highlight) ? 1 : 0;
	$maxsigrows = $maxsigrows ? 'style="height: '.$maxsigrows.'em"' : '';

	$page = empty($page) || !ispage($page) ? 1 : $page;
	$start_limit = $numpost = ($page - 1) * $ppp;
	if($start_limit > $thread['replies']) {
		$start_limit = $numpost = 0;
		$page = 1;
	}

	$multipage = multi($thread['replies'] + 1, $ppp, $page, "viewthread.php?tid=$tid&extra=$extra".(isset($highlight) ? "&highlight=".rawurlencode($highlight) : ''));

	$polloptions = array();
	if($thread['poll']) {
		$query = $db->query("SELECT pollopts FROM {$tablepre}polls WHERE tid='$tid'");
		if(is_array($pollopts = unserialize($db->result($query, 0)))) {
			$pollopts['voters'] = is_array($pollopts['voters']) ? array_map('stripslashes', $pollopts['voters']) : array();
			foreach($pollopts['options'] as $option) {
				$polloptions[] = array
					(
					'option'	=> dhtmlspecialchars(stripslashes($option[0])),
					'votes'		=> $option[1],
					'width'		=> @round($option[1] * 300 / $pollopts['max']) + 2,
					'percent'	=> @sprintf ("%01.2f", $option[1] * 100 / $pollopts['total'])
					);
			}

			$allowvote = $allowvote && (empty($thread['closed']) || $alloweditpoll) && !in_array(($discuz_uid ? $discuz_user : $onlineip), $pollopts['voters']);
			$optiontype = $pollopts['multiple'] ? 'checkbox' : 'radio';
		} else {
			$db->query("UPDATE {$tablepre}threads SET poll='0' WHERE tid='$tid'", 'UNBUFFERED');
		}
	}

	$extcredits_thread = array();
	foreach($extcredits as $key => $value) {
		if($value['showinthread']) {
			$extcredits_thread['extcredits'.$key] = array('title' => $value['title'], 'unit' => $value['unit']);
		}
	}

	$fieldsadd = '';
	if(is_array($_DCACHE['fields_thread'])) {
		foreach($_DCACHE['fields_thread'] as $field) {
			$fieldsadd .= ', mf.field_'.$field['fieldid'];
		}
	}

	$postlist = $attachtags = array();
	$newpostanchor = $postcount = $attachpids = 0;

	$query = $db->query("SELECT p.*, m.uid, m.username, m.groupid, m.regdate, m.lastactivity, m.posts, m.digestposts, m.oltime,
		m.pageviews, m.credits, m.extcredits1, m.extcredits2, m.extcredits3, m.extcredits4, m.extcredits5, m.extcredits6,
		m.extcredits7, m.extcredits8, m.email, m.gender, m.showemail, m.invisible, m.avatarshowid, mf.nickname, mf.site,
		mf.icq, mf.qq, mf.yahoo, mf.msn, mf.taobao, mf.alipay, mf.location, mf.medals, mf.avatar, mf.avatarwidth,
		mf.avatarheight, mf.sightml AS signature, mf.customstatus $fieldsadd
		FROM {$tablepre}posts p
		LEFT JOIN {$tablepre}members m ON m.uid=p.authorid
		LEFT JOIN {$tablepre}memberfields mf ON mf.uid=m.uid
		WHERE p.tid='$tid' AND p.invisible='0' ORDER BY dateline LIMIT $start_limit, $ppp");

	while($post = $db->fetch_array($query)) {

		if(!$newpostanchor && $post['dateline'] > $lastvisit) {
			$post['newpostanchor'] = '<a name="newpost"></a>';
			$newpostanchor = 1;
		} else {
			$post['newpostanchor'] = '';
		}
		$post['lastpostanchor'] = $numpost == $thread['replies'] ? '<a name="lastpost"></a>' : '';
		$post['number'] = ++$numpost;
		$post['count'] = $postcount++;

		$post['thisbg'] = $thisbg = isset($thisbg) && $thisbg == 'altbg1' ? 'altbg2' : 'altbg1';
		$post['dateline'] = gmdate("$dateformat $timeformat", $post['dateline'] + $timeoffset * 3600);

		if($post['username']) {

			$post['groupid'] = getgroupid($post['authorid'], $_DCACHE['usergroups'][$post['groupid']], $post);
			$post['readaccess'] = $_DCACHE['usergroups'][$post['groupid']]['readaccess'];
			if($userstatusby == 1 || $_DCACHE['usergroups'][$post['groupid']]['byrank'] === '0') {
				$post['authortitle'] = $_DCACHE['usergroups'][$post['groupid']]['grouptitle'];
				$post['stars'] = $_DCACHE['usergroups'][$post['groupid']]['stars'];
			} elseif($userstatusby == 2) {
				foreach($_DCACHE['ranks'] as $rank) {
					if($post['posts'] > $rank['postshigher']) {
						$post['authortitle'] = $rank['ranktitle'];
						$post['stars'] = $rank['stars'];
						break;
					}
				}
			}

			if(!$allowpaytoauthor) {
				$post['alipay'] = '';
			}

			$post['taobao'] = addslashes($post['taobao']);
			$post['authoras'] = !$post['anonymous'] ? ' '.addslashes($post['author']) : '';
			$post['regdate'] = gmdate($dateformat, $post['regdate'] + $timeoffset * 3600);
			$post['allowuseblog'] = $_DCACHE['usergroups'][$post['groupid']]['allowuseblog'];

			if($post['medals']) {
				require_once DISCUZ_ROOT.'./forumdata/cache/cache_medals.php';
				foreach($post['medals'] = explode("\t", $post['medals']) as $key => $medalid) {
					if(isset($_DCACHE['medals'][$medalid])) {
						$post['medals'][$key] = $_DCACHE['medals'][$medalid];
					} else {
						unset($post['medals'][$key]);
					}
				}

			}

			$post['avatarshow'] = $avatarshowstatus && ($post['avatarshowid'] || $avatarshowdefault) ? avatarshow($post['avatarshowid'], $post['gender']) : '';
			if($_DCACHE['usergroups'][$post['groupid']]['groupavatar']) {
				$post['avatar'] = '<img src="'.$_DCACHE['usergroups'][$post['groupid']]['groupavatar'].'" border="0">';
			} elseif($avatarshowstatus != 2 && $_DCACHE['usergroups'][$post['groupid']]['allowavatar'] && $post['avatar']) {
				$post['avatar'] = '<img src="'.$post['avatar'].'" width="'.$post['avatarwidth'].'" height="'.$post['avatarheight'].'" border="0">';
			} else {
				$post['avatar'] = '';
			}

		} else {

			if(!$post['authorid']) {
				$post['useip'] = substr($post['useip'], 0, strrpos($post['useip'], '.')).'.x';
			}

		}

		$post['attachments'] = array();
		if($post['attachment'] && $allowgetattach) {
			$attachpids .= ",$post[pid]";
			$post['attachment'] = 0;
			if(preg_match("/\[attach\](\d+)\[\/attach\]/i", $post['message'])) {
				$attachtags[] = $post['pid'];
			}
		}

		$forum['allowbbcode'] = $forum['allowbbcode'] ? ($_DCACHE['usergroups'][$post['groupid']]['allowcusbbcode'] ? 2 : 1) : 0;

		$post['ratings'] = karmaimg($post['rate'], $post['ratetimes']);
		$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], ($forum['jammer'] && $post['authorid'] != $discuz_uid ? 1 : 0));
		$post['signature'] = $post['usesig'] ? $post['signature'] : '';

		$postlist[$post['pid']] = $post;

	}

	if($attachpids) {

		$query = $db->query("SELECT aid, pid, dateline, readperm, filename, description, filetype, attachment, filesize, downloads
					FROM {$tablepre}attachments WHERE pid IN ($attachpids) ORDER BY aid");

		if($db->num_rows($query)) {
			require_once DISCUZ_ROOT.'./include/attachment.func.php';

			while($attach = $db->fetch_array($query)) {
				$extension = strtolower(fileext($attach['filename']));
				$attach['dateline'] = gmdate("$dateformat $timeformat", $attach['dateline'] + $timeoffset * 3600);
				$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
				$attach['attachsize'] = sizecount($attach['filesize']);
				$attach['attachimg'] = $attachimgpost && in_array($extension, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp')) && (!$attach['readperm'] || $readaccess >= $attach['readperm']) ? 1 : 0;
				$postlist[$attach['pid']]['attachments'][$attach['aid']] = $attach;
			}

			foreach($attachtags as $pid) {
				$postlist[$pid]['message'] = preg_replace("/\[attach\](\d+)\[\/attach\]/ie", "attachtag($pid, \\1)", $postlist[$pid]['message']);
			}
		} else {
			$db->query("UPDATE {$tablepre}posts SET attachment='0' WHERE pid IN ($attachpids)", 'UNBUFFERED');
		}

	}

	if(empty($postlist)) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	$relatedthreadlist = array();
	$relatedthreadupdate = FALSE;
	if($qihoo_status && $qihoo_relatedthreads) {
		$site = site();
		$relatedkeywords = '';
		$query = $db->query("SELECT expiration, keywords, relatedthreads FROM {$tablepre}relatedthreads WHERE tid=$tid");
		$related = $db->fetch_array($query);
		if($related['expiration'] > $timestamp) {
			$relatedthreadlist = unserialize($related['relatedthreads']);
			if($related['keywords']) {
				$searchkeywords = rawurlencode(str_replace("\t", ' ', $related['keywords']));
				foreach(explode("\t", $related['keywords']) as $keyword) {
					$relatedkeywords .= $keyword ? '<a href="search.php?srchtype=qihoo&srchtxt='.rawurlencode($keyword).'&searchsubmit=yes" target="_blank"><span class="bold"><font color=red>'.$keyword.'</font></span></a> ' : '';
				}
			}
		} else {
			$relatedthreadupdate = TRUE;
			$verifykey = md5($authkey.$tid.$thread[subjectenc].$charset.$site);
		}
	}

	$relatedthreads = array();
	if(!empty($relatedthreadlist)) {
		if(!isset($_COOKIE['discuz_collapse']) || strpos($_COOKIE['discuz_collapse'], 'relatedthreads') === FALSE) {
			$relatedthreads['img'] = 'collapsed_no.gif';
			$relatedthreads['style'] = '';
		} else {
			$relatedthreads['img'] = 'collapsed_yes.gif';
			$relatedthreads['style'] = 'display: none';
		}
	}

	$visitedforums = $visitedforums ? visitedforums() : '';
	$forumselect = $forumjump ? forumselect() : '';

	$usesigcheck = $discuz_uid && $sigstatus ? 'checked' : '';
	$allowpostreply = ((!$thread['closed'] && !checkautoclose()) || $forum['ismoderator']) && ((!$forum['replyperm'] && $allowreply) || ($forum['replyperm'] && forumperm($forum['replyperm'])) || $forum['allowreply']);
	$allowpost = (!$forum['postperm'] && $allowpost) || ($forum['postperm'] && forumperm($forum['postperm'])) || $forum['allowpost'];

	if($delayviewcount == 1 || $delayviewcount == 3) {
		$logfile = './forumdata/cache/cache_threadviews.log';
		if(substr($timestamp, -2) == '00') {
			require_once DISCUZ_ROOT.'./include/misc.func.php';
			updateviews('threads', 'tid', 'views', $logfile);
		}

		if(@$fp = fopen(DISCUZ_ROOT.$logfile, 'a')) {
			fwrite($fp, "$tid\n");
			fclose($fp);
		} elseif($adminid == 1) {
			showmessage('view_log_invalid');
		}
	} else {
		$db->query("UPDATE {$tablepre}threads SET views=views+1 WHERE tid='$tid'", 'UNBUFFERED');
	}

	include template('viewthread');

} elseif($action == 'printable' && $tid) {

	require_once DISCUZ_ROOT.'./include/printable.inc.php';

}

?>