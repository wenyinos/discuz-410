<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: home.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

echo "<p align=\"center\">$bbname</p>\n";

$onlinemem = $onlineguest = 0;
$query = $db->query("SELECT uid, COUNT(*) AS count FROM {$tablepre}sessions GROUP BY uid='0'");
while($online = $db->fetch_array($query)) {
	$online['uid'] ? $onlinemem = $online['count'] : $onlineguest = $online['count'];
}

echo "<p align=\"center\">$lang[home_online]".($onlinemem + $onlineguest)."({$onlinemem} $lang[home_members])<br /></p>\n";

if($discuz_uid && $newpm) {
	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox' AND new='1'");
	if($newpm = $db->result($query, 0)) {
		echo "<p align=\"center\"><a href=\"index.php?action=pm&amp;do=list&amp;unread=yes\">$newpm $lang[home_newpm]</a><br /></p>\n";
	} else {
		$db->query("UPDATE {$tablepre}members SET newpm='0' WHERE uid='$discuz_uid'");
	}
}

$catforumexists = 0;
echo "<p>$lang[home_forums]<br />";

foreach($_DCACHE['forums'] as $fid => $forum) {
	if($forum['type'] == 'group') {
		echo "<a href=\"index.php?action=forum&amp;fup=$forum[fid]\">$forum[name]</a><br/>";
	} elseif($forum['type'] == 'forum' && !$forum['fup']) {
		$catforumexists = 1;
	}
}

if($catforumexists) {
	echo "<a href=\"index.php?action=forum&amp;fup=0\">{$_DCACHE['settings']['bbname']}</a><br/>";
}

echo "</p>\n".
	"<p>$lang[home_tools]<br />".
	"<a href=\"index.php?action=".($discuz_uid ? 'pm' : 'login')."\">$lang[pm]</a><br />".
	"<a href=\"index.php?action=stats\">$lang[stats]</a><br />".
	"<a href=\"index.php?action=myphone\">$lang[myphone]</a><br />".
	"<a href=\"index.php?action=goto\">$lang[goto]</a></p>";

?>