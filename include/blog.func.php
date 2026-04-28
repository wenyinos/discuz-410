<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: blog.func.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function calendar($starttime = 0) {
	global $db, $tablepre, $uid, $timestamp, $timeoffset, $dateformat, $curtime;

	$starttime = $starttime ? $starttime : $timestamp;
	$curtime = gmdate($dateformat, $starttime + $timeoffset * 3600);

	$pendtime = $starttime - (gmdate('j', $starttime + $timeoffset * 3600) - 1) * 86400 - ($starttime + $timeoffset * 3600) % 86400;
	$pstarttime = $pendtime - gmdate('t', $pendtime + $timeoffset * 3600 - 1) * 86400;

	$nstarttime = $pendtime + gmdate('t', $pendtime + $timeoffset * 3600 + 1) * 86400;
	$nendtime = $nstarttime + gmdate('t', $nstarttime + $timeoffset * 3600 + 1) * 86400;

	list($skip, $dim) = explode('-', gmdate('w-t', $pendtime + $timeoffset * 3600 + 1));
	$rows = ceil(($skip + $dim) / 7);

	$blogs = array();
	$query = $db->query("SELECT dateline FROM {$tablepre}threads WHERE blog='1' AND authorid='$uid' AND dateline BETWEEN '$pendtime' AND '$nstarttime' AND displayorder>='0'");
	while($blog = $db->fetch_array($query)) {
		$day = gmdate('j', $blog['dateline'] + $timeoffset * 3600);
		!isset($blogs[$day]) ? $blogs[$day] = array('num' => 1, 'dateline' => $blog['dateline'] - $blog['dateline'] % 86400) : $blogs[$day]['num']++;
	}

	$cal = '';
	for($row = 0; $row < $rows; $row++) {
		$cal .= '<tr align="center" class="smalltxt">';
		for($col = 0; $col < 7; $col++) {
			$cur = $row * 7 + $col - $skip + 1;
			$curtd = $row * 7 + $col < $skip || $cur > $dim ? '&nbsp;' : $cur;
			if(!isset($blogs[$cur])) {
				$cal .= '<td bgcolor="'.ALTBG1.'">'.$curtd.'</td>';
			} else {
				$cal .= '<td bgcolor="'.ALTBG2.'"><a href="blog.php?uid='.$uid.'&starttime='.$blogs[$cur]['dateline'].'&endtime='.($blogs[$cur]['dateline'] + 86400).'" title=" '.$blogs[$cur]['num'].' "><b>'.$cur.'</b></a></td>';
			}
		}
		$cal .= '</tr>';
	}

	return array('pstarttime' => $pstarttime, 'pendtime' => $pendtime, 'nstarttime' => $nstarttime, 'nendtime' => $nendtime, 'html' => $cal);
}

function updateblogcache($uid, $cachename) {
	global $_DCACHE, $db, $tablepre, $timestamp;

	$_DCACHE['blog'][$cachename] = array('lastupdate' => $timestamp, 'data' => array());

	switch($cachename) {
		case 'forums'	: $sql = "SELECT f.fid, f.name FROM {$tablepre}threads t, {$tablepre}forums f
								WHERE t.blog='1' AND t.authorid='$uid' AND t.displayorder>='0' AND f.fid=t.fid
								GROUP BY t.fid ORDER BY f.displayorder"; break;
		case 'hot'		: $sql = "SELECT tid, subject, views, replies FROM {$tablepre}threads WHERE blog='1'
								AND authorid='$uid' AND displayorder>='0' ORDER BY views DESC LIMIT 5"; break;
	}

	$query = $db->query($sql);
	while($forum = $db->fetch_array($query)) {
		$_DCACHE['blog'][$cachename]['data'][] = $forum;
	}
	$db->query("REPLACE INTO {$tablepre}blogcaches (uid, variable, value)
		VALUES ('$uid', '$cachename', '".addslashes(serialize($_DCACHE['blog'][$cachename]))."')", 'UNBUFFERED');
}

?>