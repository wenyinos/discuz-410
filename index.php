<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: index.php,v $
	$Revision: 1.9.2.1 $
	$Date: 2006/03/10 02:42:17 $
*/

define('CURSCRIPT', 'index');

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/forum.func.php';

$discuz_action = 1;

$validdays = $discuz_uid && !empty($groupexpiry) && $groupexpiry >= $timestamp ?
	ceil(($groupexpiry - $timestamp) / 86400) : 0;

if(isset($showoldetails)) {
	switch($showoldetails) {
		case 'no': dsetcookie('onlineindex', 0, 86400 * 365); break;
		case 'yes': dsetcookie('onlineindex', 1, 86400 * 365); break;
	}
} else {
	$showoldetails = false;
}

$currenttime = gmdate($timeformat, $timestamp + $timeoffset * 3600);
$lastvisittime = gmdate("$dateformat $timeformat", $lastvisit + $timeoffset * 3600);

$memberenc = rawurlencode($lastmember);
$newthreads = round(($timestamp - $lastvisit + 600) / 1000) * 1000;

$searchboxstatus = substr(sprintf('%03b', $qihoo_searchbox), -1, 1);
$keywordlist = $qihoo_links['keywords'];
$topiclist = $qihoo_links['topics'];

if($qihoo_maxtopics) {
	$customtopics = '';
	foreach(explode("\t", $_DCOOKIE['customkw']) as $topic) {
		$topic = dhtmlspecialchars(trim(stripslashes($topic)));
		$customtopics .= '<a href="topic.php?keyword='.rawurlencode($topic).'" target="_blank">'.$topic.'</a> ';
	}
}

if(empty($gid)) {

	$navigation = $navtitle = '';

	$announcements = $space = '';
	if($_DCACHE['announcements']) {
		foreach($_DCACHE['announcements'] as $announcement) {
			$announcements .= $space.'<a href="announcement.php?id='.$announcement['id'].'#'.$announcement['id'].'"><span class="bold">'.$announcement['subject'].'</span> '.
				'('.gmdate($dateformat, $announcement['starttime'] + $timeoffset * 3600).')</a>';
			$space = '&nbsp; &nbsp; &nbsp; &nbsp;';
		}
	}
	unset($_DCACHE['announcements']);

	$threads = $posts = $todayposts = 0;
	$forumlist = $catforumlist = $forums = $catforums = $categories = $collapse = array();

	$sql = !empty($accessmasks)	?
				"SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, ff.description, ff.moderators, ff.icon, ff.viewperm, a.allowview FROM {$tablepre}forums f
					LEFT JOIN {$tablepre}forumfields ff ON ff.fid=f.fid
					LEFT JOIN {$tablepre}access a ON a.uid='$discuz_uid' AND a.fid=f.fid
					WHERE f.status='1' ORDER BY f.type, f.displayorder"
				: "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, ff.description, ff.moderators, ff.icon, ff.viewperm FROM {$tablepre}forums f
					LEFT JOIN {$tablepre}forumfields ff USING(fid)
					WHERE f.status='1' ORDER BY f.type, f.displayorder";

	$query = $db->query($sql);

	while($forum = $db->fetch_array($query)) {
		$forumname[$forum['fid']] = strip_tags($forum['name']);
		if($forum['type'] != 'group') {
			$threads += $forum['threads'];
			$posts += $forum['posts'];
			$todayposts += $forum['todayposts'];

			if($forum['type'] != 'sub') {
				$forums[$forum['fid']] = $forum;
			} else {
				if($subforumsindex) {
					$forums[$forum['fup']]['subforums'][] = '<a href="forumdisplay.php?fid='.$forum['fid'].'"><u>'.$forum['name'].'</u></a>';
				}
				$forums[$forum['fup']]['threads'] += $forum['threads'];
				$forums[$forum['fup']]['posts'] += $forum['posts'];
				$forums[$forum['fup']]['todayposts'] += $forum['todayposts'];
			}
		} else {
			$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
			if(!isset($_COOKIE['discuz_collapse']) || strpos($_COOKIE['discuz_collapse'], 'category_'.$forum['fid'].' ') === FALSE) {
				$forum['collapseimg'] = 'collapsed_no.gif';
				$collapse['category_'.$forum['fid']] = '';
			} else {
				$forum['collapseimg'] = 'collapsed_yes.gif';
				$collapse['category_'.$forum['fid']] = 'display: none';
			}
			$categories[] = $forum;
		}
	}

	if($categories) {
		foreach($categories as $group) {
			$group_forum = array();
			foreach($forums as $fid => $forum) {
				if($forum['fup'] == $group['fid']) {
					if(forum($forum)) {
						$group_forum[] = $forum;
						unset($forums[$fid]);
					}
				} elseif(!$forum['fup'] && $forum['type'] == 'forum') {
					$catforums[] = $forum;
					unset($forums[$fid]);
				}
			}
			if($group_forum) {
				$forumlist = array_merge($forumlist, array($group), $group_forum);
			}
		}
	} else {
		$catforums = $forums;
	}

 	foreach($catforums as $forum) {
		if(forum($forum)) {
			$catforumlist[] = $forum;
		}
	}
	if($catforumlist) {
		$forum = array('fid' => 0, 'type' => 'group', 'name' => $bbname);
		if(strpos($_COOKIE['discuz_collapse'], 'category_0 ') === FALSE) {
			$forum['collapseimg'] = 'collapsed_no.gif';
			$collapse['category_0'] = '';
		} else {
			$forum['collapseimg'] = 'collapsed_yes.gif';
			$collapse['category_'.$forum['fid']] = 'display: none';
		}
		$forumlist = array_merge($forumlist, array($forum), $catforumlist);
	}

	unset($fid, $forums, $catforums, $catforumlist, $categories, $group, $forum, $group_forum);

	foreach(array('forumlinks', 'birthdays') as $key) {
		if(!isset($_COOKIE['discuz_collapse']) || strpos($_COOKIE['discuz_collapse'], $key.' ') === FALSE) {
			$linkcollapseimg = 'collapsed_no.gif';
			$collapse[$key] = '';
		} else {
			$linkcollapseimg = 'collapsed_yes.gif';
			$collapse[$key] = 'display: none';
		}
	}

	if($whosonlinestatus == 1 || $whosonlinestatus == 3) {
		$whosonlinestatus = 1;

		$onlineinfo = explode("\t", $onlinerecord);
		$detailstatus = ((empty($_DCOOKIE['onlineindex']) && $onlineinfo[0] < 500) || (!empty($_DCOOKIE['onlineindex']) || $showoldetails == 'yes')) && $showoldetails != 'no';

		if($detailstatus) {
			@include language('actions');

			updatesession();
			$membercount = $invisiblecount = 0;
			$whosonline = array();
			$query = $db->query("SELECT uid, username, groupid, invisible, action, lastactivity, fid FROM {$tablepre}sessions ORDER BY uid DESC");
			while($online = $db->fetch_array($query)) {
				if($online['uid']) {
					$membercount++;
					if(!$online['invisible']) {
						$online['icon'] = isset($_DCACHE['onlinelist'][$online['groupid']]) ? $_DCACHE['onlinelist'][$online['groupid']] : $_DCACHE['onlinelist'][0];
					} else {
						$invisiblecount++;
						continue;
					}

					$online['fid'] = $online['fid'] ? $forumname[$online['fid']] : 0;
					$online['action'] = $actioncode[$online['action']];
					$online['lastactivity'] = gmdate($timeformat, $online['lastactivity'] + ($timeoffset * 3600));
					$whosonline[] = $online;
				} else {
					break;
				}
			}
			$onlinenum = $db->num_rows($query);
			$guestcount = $onlinenum - $membercount;
			unset($online);
		} else {
			$query = $db->query("SELECT COUNT(*) FROM {$tablepre}sessions");
			$onlinenum = $db->result($query, 0);
		}

		if($onlinenum > $onlineinfo[0]) {
			$db->query("UPDATE {$tablepre}settings SET value='$onlinenum\t$timestamp' WHERE variable='onlinerecord'");
			require_once DISCUZ_ROOT.'./include/cache.func.php';
			updatecache('settings');
			$onlineinfo = array($onlinenum, $timestamp);
		}

		$onlineinfo[1] = gmdate($dateformat, $onlineinfo[1] + ($timeoffset * 3600));
	} else {
		$whosonlinestatus = 0;
	}

	if($discuz_uid && $newpm) {
		require_once DISCUZ_ROOT.'./include/pmprompt.inc.php';
	}

} else {

	require_once DISCUZ_ROOT.'./include/category.inc.php';

}

include template('index');

?>