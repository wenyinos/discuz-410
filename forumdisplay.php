<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: forumdisplay.php,v $
	$Revision: 1.7 $
	$Date: 2006/02/23 13:44:02 $
*/

define('CURSCRIPT', 'forumdisplay');

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/forum.func.php';

$discuz_action = 2;

if($forum['redirect']) {
	header("Location: $forum[redirect]");
	exit();
}

if(isset($showoldetails)) {
	switch ($showoldetails) {
		case 'no': dsetcookie('onlineforum', 0, 86400 * 365); break;
		case 'yes': dsetcookie('onlineforum', 1, 86400 * 365); break;
	}
} else {
	$showoldetails = false;
}

if(!$forum['fid'] || $forum['type'] == 'group') {
	showmessage('forum_nonexistence', NULL, 'HALTED');
}

if($forum['type'] == 'forum') {
	$navigation = "&raquo; $forum[name]";
	$navtitle = ' - '.strip_tags($forum['name']);
} else {
	$forumup = $_DCACHE['forums'][$forum['fup']]['name'];
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$forum[fup]\">$forumup</a> &raquo; $forum[name]";
	$navtitle = ' - '.strip_tags($forumup).' - '.strip_tags($forum['name']);
}

if($forum['password'] && $action == 'pwverify') {
	if($pw != $forum['password']) {
		showmessage('forum_passwd_incorrect', NULL, 'HALTED');
	} else {
		dsetcookie('fidpw'.$fid, $pw);
		showmessage('forum_passwd_correct', "forumdisplay.php?fid=$fid");
	}
}

if($forum['rules']) {
	if(empty($_COOKIE['discuz_collapse']) || strpos($_COOKIE['discuz_collapse'], 'rules_'.$forum['fid'].' ') === FALSE) {
		$rulescollapseimg = 'collapsed_no.gif';
		$collapserules = '';
	} else {
		$rulescollapseimg = 'collapsed_yes.gif';
		$collapserules = 'display: none';
	}
	$forum['rules'] = nl2br($forum['rules']);
}

if($forum['viewperm'] && !forumperm($forum['viewperm']) && !$forum['allowview']) {
	showmessage('forum_nopermission', NULL, 'NOPERM');
}

if(!empty($forum['password']) && $forum['password'] != $_DCOOKIE['fidpw'.$fid]) {
	include template('forumdisplay_passwd');
	exit();
}

$moderatedby = moddisplay($forum['moderators'], 'forumdisplay');

if($forum['autoclose']) {
	$closedby = $forum['autoclose'] > 0 ? 'dateline' : 'lastpost';
	$forum['autoclose'] = abs($forum['autoclose']) * 86400;
}

$subexists = 0;
foreach($_DCACHE['forums'] as $sub) {
	if($sub['type'] == 'sub' && $sub['fup'] == $fid && (!$hideprivate || !$sub['viewperm'] || forumperm($sub['viewperm']) || strstr($sub['users'], "\t$discuz_uid\t"))) {
		$subexists = 1;
		$sublist = array();
		$sql = $accessmasks	? "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, ff.description, ff.moderators, ff.icon, ff.viewperm, a.allowview FROM {$tablepre}forums f
						LEFT JOIN {$tablepre}forumfields ff ON ff.fid=f.fid
						LEFT JOIN {$tablepre}access a ON a.uid='$discuz_uid' AND a.fid=f.fid
						WHERE fup='$fid' AND status='1' AND type='sub' ORDER BY f.displayorder"
					: "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, ff.description, ff.moderators, ff.icon, ff.viewperm FROM {$tablepre}forums f
						LEFT JOIN {$tablepre}forumfields ff USING(fid)
						WHERE f.fup='$fid' AND f.status='1' AND f.type='sub' ORDER BY f.displayorder";
		$query = $db->query($sql);
		while($sub = $db->fetch_array($query)) {
			if(forum($sub)) {
				$sublist[] = $sub;
			}
		}
		break;
	}
}

$page = empty($page) || !ispage($page) || ($threadmaxpages && $page > $threadmaxpages) ? 1 : $page;
$start_limit = ($page - 1) * $tpp;

if($page == 1) {
	if($_DCACHE['announcements_forum']) {
		$announcement = $_DCACHE['announcements_forum'];
		$announcement['starttime'] = gmdate($dateformat, $announcement['starttime'] + ($timeoffset * 3600));
	} else {
		$announcement = NULL;
	}	
}

$forumdisplayadd = $filteradd = '';
if(isset($filter)) {
	if($filter == 'digest') {
		$forumdisplayadd .= "&filter=digest";
		$filteradd = "AND digest>'0'";
	} elseif($filter == 'type' && $forum['threadtypes']['listable'] && $typeid && isset($forum['threadtypes']['types'][$typeid])) {
		$forumdisplayadd .= "&filter=type&typeid=$typeid";
		$filteradd = "AND typeid='$typeid'";
	} elseif(preg_match("/^\d+$/", $filter)) {
		$forumdisplayadd .= "&filter=$filter";
		$filteradd = $filter ? "AND lastpost>='".($timestamp - $filter)."'" : '';
	} else {
		$filter = '';
	}
} else {
	$filter = '';
}

isset($orderby) && in_array($orderby, array('dateline', 'replies', 'views')) ? $forumdisplayadd .= "&orderby=$orderby" : $orderby = 'lastpost';
isset($ascdesc) && $ascdesc == 'ASC' ? $forumdisplayadd .= '&ascdesc=ASC' : $ascdesc = 'DESC';

$dotadd1 = $dotadd2 = '';
if($dotfolders && $discuz_uid) {
	$dotadd1 = "DISTINCT p.authorid AS dotauthor, ";
	$dotadd2 = "LEFT JOIN {$tablepre}posts p ON (t.tid=p.tid AND p.authorid='$discuz_uid')";
}

if($whosonlinestatus == 2 || $whosonlinestatus == 3) {
	$whosonlinestatus = 1;
	$onlineinfo = explode("\t", $onlinerecord);
	$detailstatus = ((!isset($_DCOOKIE['onlineforum']) && $onlineinfo[0] < 500) || (!empty($_DCOOKIE['onlineforum']) || $showoldetails == 'yes')) && $showoldetails != 'no';

	if($detailstatus) {
		updatesession();
		@include language('actions');

		$whosonline = array();
		$forumname = strip_tags($forum['name']);
		$query = $db->query("SELECT uid, groupid, username, invisible, lastactivity, action FROM {$tablepre}sessions WHERE uid>'0' AND fid='$fid' AND invisible='0'");
		if($db->num_rows($query)) {
			$whosonlinestatus = 1;
			while($online = $db->fetch_array($query)) {
				$online['icon'] = isset($_DCACHE['onlinelist'][$online['groupid']]) ? $_DCACHE['onlinelist'][$online['groupid']] : $_DCACHE['onlinelist'][0];
				$online['action'] = $actioncode[$online['action']];
				$online['lastactivity'] = gmdate($timeformat, $online['lastactivity'] + ($timeoffset * 3600));
				$whosonline[] = $online;     
			}
		}
		unset($online);
	}
} else {
	$whosonlinestatus = 0;
}

if($discuz_uid && $newpm) {
	require_once DISCUZ_ROOT.'./include/pmprompt.inc.php';
}

if(empty($filter)) {
	$threadcount = $forum['threads'];
} else {
	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}threads WHERE fid='$fid' $filteradd AND displayorder>='0'");
	$threadcount = $db->result($query, 0);
}

if($globalstick) {
	$thisgid = $forum['type'] == 'forum' ? $forum['fup'] : $_DCACHE['forums'][$forum['fup']]['fup'];
	$stickytids = $_DCACHE['globalstick']['global']['tids'].(empty($_DCACHE['globalstick']['categories'][$thisgid]['count']) ? '' : ','.$_DCACHE['globalstick']['categories'][$thisgid]['tids']);
	$stickycount = $_DCACHE['globalstick']['global']['count'] + $_DCACHE['globalstick']['categories'][$thisgid]['count'];
} else {
	$thisgid = $stickycount = $stickytids = 0;
}

$threadcount = $threadcount + $stickycount;
$multipage = multi($threadcount, $tpp, $page, "forumdisplay.php?fid=$fid$forumdisplayadd", $threadmaxpages);
$extra = rawurlencode("page=$page$forumdisplayadd");

$separatepos = 0;
$threadlist = array();
$colorarray = array('', 'red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'purple', 'gray');

$displayorderadd = $filter != 'digest' && $filter != 'type' && $stickycount ? 't.displayorder IN (0, 1)' : 't.displayorder>=0';

if(($start_limit && $start_limit > $stickycount) || !$stickycount || $filter == 'digest' || $filter == 'type' ) {

	$querysticky = '';
	$query = $db->query("SELECT $dotadd1 t.* FROM {$tablepre}threads t $dotadd2
		WHERE t.fid='$fid' $filteradd AND $displayorderadd
		ORDER BY t.displayorder DESC, t.$orderby $ascdesc
		LIMIT ".($filter == 'digest' || $filter == 'type' ? $start_limit : $start_limit - $stickycount).", $tpp");

} else {

	$querysticky = $db->query("SELECT $dotadd1 t.* FROM {$tablepre}threads t $dotadd2
		WHERE t.tid IN ($stickytids) AND t.displayorder IN (2, 3)
		ORDER BY displayorder DESC, $orderby $ascdesc
		LIMIT $start_limit, ".($stickycount - $start_limit < $tpp ? $stickycount - $start_limit : $tpp));

	if($tpp - $stickycount + $start_limit > 0) {
		$query = $db->query("SELECT $dotadd1 t.* FROM {$tablepre}threads t $dotadd2
			WHERE t.fid='$fid' $filteradd AND $displayorderadd
			ORDER BY displayorder DESC, $orderby $ascdesc
			LIMIT ".($tpp - $stickycount + $start_limit));
	} else {
		$query = '';
	}

}

while(($querysticky && $thread = $db->fetch_array($querysticky)) || ($query && $thread = $db->fetch_array($query))) {
	$thread['icon'] = isset($_DCACHE['icons'][$thread['iconid']]) ? '<img src="'.SMDIR.'/'.$_DCACHE['icons'][$thread['iconid']].'" align="absmiddle">' : '&nbsp;';
	$thread['lastposterenc'] = rawurlencode($thread['lastposter']);

	$thread['typeid'] = $thread['typeid'] && !empty($forum['threadtypes']['prefix']) && isset($forum['threadtypes']['types'][$thread['typeid']]) ?
		'['.$forum['threadtypes']['types'][$thread['typeid']].'] ' : '';		

	$topicposts = $thread['replies'] + 1;
	if($topicposts > $ppp) {
		$pagelinks = '';
		$topicpages = ceil($topicposts / $ppp);
		for ($i = 1; $i <= $topicpages; $i++) {
			$pagelinks .= "<a href=\"viewthread.php?tid=$thread[tid]&extra=$extra&page=$i\">$i</a> ";
			if($i == 6) {
				$i = $topicpages + 1;
			}
		}
		if($topicpages > 6) {
			$pagelinks .= " .. <a href=\"viewthread.php?tid=$thread[tid]&page=$topicpages&extra=$extra\">$topicpages</a> ";
		}
		$thread['multipage'] = '&nbsp; &nbsp;( <img src="'.IMGDIR.'/multipage.gif" align="absmiddle" border="0"> '.$pagelinks.')';
	} else {
		$thread['multipage'] = '';
	}

	if($thread['highlight']) {
		$string = sprintf('%02d', $thread['highlight']);
		$stylestr = sprintf('%03b', $string[0]);

		$thread['highlight'] = ' style="';
		$thread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
		$thread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
		$thread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
		$thread['highlight'] .= $string[1] ? 'color: '.$colorarray[$string[1]] : '';
		$thread['highlight'] .= '"';
	} else {
		$thread['highlight'] = '';
	}

	$thread['moved'] = 0;
	if($thread['closed'] || ($forum['autoclose'] && $timestamp - $thread[$closedby] > $forum['autoclose'])) {
		$thread['new'] = 0;
		if($thread['closed'] > 1) {
			$thread['moved'] = $thread['tid'];
			$thread['tid'] = $thread['closed'];
			$thread['replies'] = '-';
			$thread['views'] = '-';
		}
		$thread['folder'] = 'lock_folder.gif';
	} else {
		$thread['folder'] = 'folder.gif';
		if($lastvisit < $thread['lastpost'] && (empty($_DCOOKIE['oldtopics']) || strpos($_DCOOKIE['oldtopics'], 'D'.$thread['tid'].'D') === FALSE)) {
			$thread['new'] = 1;
			$thread['folder'] = 'red_'.$thread['folder'];
		} else {
			$thread['new'] = 0;
		}
		if($thread['replies'] > $thread['views']) {
			$thread['views'] = $thread['replies'];
		}
		if($thread['replies'] >= $hottopic) {
			$thread['folder'] = 'hot_'.$thread['folder'];
		}
		if($dotfolders && $thread['dotauthor'] == $discuz_uid && $discuz_uid) {
			$thread['folder'] = 'dot_'.$thread['folder'];
		}
	}

	$thread['dateline'] = gmdate($dateformat, $thread['dateline'] + $timeoffset * 3600);
	$thread['lastpost'] = gmdate("$dateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600);

	if($thread['displayorder'] > 0) {
		$separatepos++;
	}

	$threadlist[] = $thread;

}

$separatepos = $separatepos ? $separatepos + 1 : ($announcement ? 1 : 0);

$check = array();
$check[$filter] = $check[$orderby] = $check[$ascdesc] = 'selected="selected"';

$visitedforums = $visitedforums ? visitedforums() : '';
$forumselect = $forumjump ? forumselect() : '';
$typeselect = typeselect($typeid);

$usesigcheck = $discuz_uid && $sigstatus ? 'checked' : '';
$allowpost = (!$forum['postperm'] && $allowpost) || ($forum['postperm'] && forumperm($forum['postperm'])) || !empty($forum['allowpost']);
$allowpostpoll = $allowpost && $allowpostpoll;

//get trade thread status (pos. -1)
$allowposttrade = substr(sprintf('%02b', $forum['allowtrade']), -1, 1);

$searchboxstatus = substr(sprintf('%03b', $qihoo_searchbox), -2, 1);

include template('forumdisplay');

?>
