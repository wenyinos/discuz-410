<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: category.inc.php,v $
	$Revision: 1.5 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$forums = $forumlist = array();
$threads = $posts = $todayposts = $fids = 0;

$sql = $accessmasks	? "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, ff.description, ff.moderators, ff.icon, ff.viewperm, a.allowview FROM {$tablepre}forums f
				LEFT JOIN {$tablepre}forumfields ff ON ff.fid=f.fid
				LEFT JOIN {$tablepre}access a ON a.uid='$discuz_uid' AND a.fid=f.fid
				WHERE f.status='1' AND (f.fid='$gid' OR (f.fup='$gid' AND f.type='forum')) ORDER BY f.type, f.displayorder"
			: "SELECT f.fid, f.fup, f.type, f.name, f.threads, f.posts, f.todayposts, f.lastpost, f.inheritedmod, ff.description, ff.moderators, ff.icon, ff.viewperm FROM {$tablepre}forums f
				LEFT JOIN {$tablepre}forumfields ff USING(fid)
				WHERE f.status='1' AND (f.fid='$gid' OR (f.fup='$gid' AND f.type='forum')) ORDER BY f.type, f.displayorder";

$query = $db->query($sql);
while($forum = $db->fetch_array($query)) {

	if($forum['type'] != 'group') {
		$threads += $forum['threads'];
		$posts += $forum['posts'];
		$todayposts += $forum['todayposts'];
		$fids .= ','.$forum['fid'];

		$forums[$forum['fid']] = $forum;
	} else {
		if(strpos($_COOKIE['discuz_collapse'], 'category_'.$forum['fid'].' ') === FALSE) {
			$forum['collapseimg'] = 'collapsed_no.gif';
			$collapse['category_'.$forum['fid']] = '';
		} else {
			$forum['collapseimg'] = 'collapsed_yes.gif';
			$collapse['category_'.$forum['fid']] = 'display: none';
		}

		if($forum['moderators']) {
			$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
		}
		$forumlist[] = $forum;

		$navigation = '&raquo; '.$forum['name'];
		$navtitle = ' - '.strip_tags($forum['name']);
	}

}	

if($db->num_rows($query) < 2) {
	showmessage('forum_nonexistence', NULL, 'HALTED');
}

$query = $db->query("SELECT fid, fup, name, threads, posts FROM {$tablepre}forums WHERE status='1' AND fup IN ($fids) AND type='sub'");
while($forum = $db->fetch_array($query)) {
	if($subforumsindex) {
		$forums[$forum['fup']]['subforums'][] = '<a href="forumdisplay.php?fid='.$forum['fid'].'"><u>'.$forum['name'].'</u></a>';
	}
	$forums[$forum['fup']]['threads'] += $forum['threads'];
	$forums[$forum['fup']]['posts'] += $forum['posts'];
	$forums[$forum['fup']]['todayposts'] += $forum['todayposts'];
}

foreach($forums as $forum) {
	if(forum($forum)) {
		$forumlist[] = $forum;
	}
}

?>