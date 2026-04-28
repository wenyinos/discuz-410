<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: forum.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$query = $db->query("SELECT * FROM {$tablepre}forums f
	LEFT JOIN {$tablepre}forumfields ff USING (fid)
	WHERE f.fid='$fid' AND f.status='1' AND f.type<>'group' AND ff.password=''");

$forum = $db->fetch_array($query);

if($forum['redirect']) {
	header("Location: $forum[redirect]");
	exit();
}

$page = empty($page) ? 1 : $page;

$navtitle = ($forum['type'] == 'sub' ? ' - '.strip_tags($_DCACHE['forums'][$forum['fup']]['name']) : '').
	' - '.strip_tags($forum['name']).'('.$lang['page'].' '.$page.')';

require_once './include/header.inc.php';

?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<?

if(!$forum || !forumperm($forum['viewperm'])) {

?>
<tr><td bgcolor="<?=ALTBG1?>" class="bold"><a href="archiver/"><?=$_DCACHE['settings']['bbname']?></a></td></tr></table><br><br>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td bgcolor="<?=ALTBG2?>"><br><?=$lang['forum_nonexistence']?><br><br></td></tr></table>
<?

} else {

	$navsub = $forum['type'] == 'sub' ? "<a href=\"archiver/{$qm}fid-$forum[fup].html\">{$_DCACHE[forums][$forum[fup]][name]}</a> <b>&raquo;</b>": ' ';
	$fullversion = array('title' => $forum['name'], 'link' => "forumdisplay.php?fid=$fid");

	$tpp = $_DCACHE['settings']['topicperpage'] * 2;
	$start = ($page - 1) * $tpp;

?>
<tr><td bgcolor="<?=ALTBG1?>" class="bold"><a href="archiver/"><?=$_DCACHE['settings']['bbname']?></a> <b>&raquo;</b><?=$navsub?><a href="archiver/<?=$qm?>fid-<?=$fid?>.html"><?=$forum['name']?></a></td></tr></table>
<table cellspacing="0" cellpadding="0" width="<?=TABLEWIDTH?>" align="center"><tr><td align="center"><br><?=multi($forum['threads'], $page, $tpp, "{$qm}fid-$fid")?><br><br></td></tr></table>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td bgcolor="<?=ALTBG2?>"><br>
<?

	echo "<ul type=\"1\" start=\"".($start + 1)."\">\n";

	$query = $db->query("SELECT * FROM {$tablepre}threads WHERE fid='$fid' AND displayorder>='0' ORDER BY displayorder DESC, lastpost DESC LIMIT $start, $tpp");
	while($thread = $db->fetch_array($query)) {
		echo "<li><a href=\"archiver/{$qm}tid-$thread[tid].html\">$thread[subject]</a> <i>($thread[replies] $lang[replies])</i></li>\n";
	}

	echo "</ul></td></tr></table>\n";

}

?>