<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: index.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once './include/header.inc.php';

?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td bgcolor="<?=ALTBG1?>" class="bold"><a href="archiver/"><?=$_DCACHE['settings']['bbname']?></a></td></tr></table><br><br>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td bgcolor="<?=ALTBG2?>"><br>
<?

$forums = $subforums = array();
$categories = array(0 => array('fid' => 0, 'name' => $_DCACHE['settings']['bbname']));

foreach($_DCACHE['forums'] as $forum) {
	if(forumperm($forum['viewperm'])) {
		if($forum['type'] == 'group') {
			$categories[] = $forum;
		} else {
			$forum['type'] == 'sub' ? $subforums[$forum['fup']][] = $forum : $forums[$forum['fup']][] = $forum;
		}
	 }
}

echo "<ul>\n";

foreach($categories as $category) {
	if(isset($forums[$category['fid']])) {
		echo "<li><b>$category[name]</b></li><ul>\n";
		foreach($forums[$category[fid]] as $forum) {
			echo "<li><a href=\"archiver/{$qm}fid-{$forum[fid]}.html\">$forum[name]</a></li>\n";
			if(isset($subforums[$forum['fid']])) {
				echo "<ul>\n";
				foreach($subforums[$forum['fid']] as $subforum) {
					echo "<li><a href=\"archiver/{$qm}fid-$subforum[fid].html\">$subforum[name]</a></li>\n";
				}
				echo "</ul>\n";
			}
		}
		echo "</ul><br>\n";
	}
}

echo "</td></tr></table>\n";

?>