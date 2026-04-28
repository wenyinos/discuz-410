<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: goto.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

$discuz_action = 194;

echo "<p>$lang[goto]:</p>\n".
	"<p><input title=\"url\" name=\"url\" type=\"text\" value=\"http://\" /></p>\n".
	"<p><anchor title=\"$lang[submit]\">$lang[submit]<go href=\"index.php?action=goto&amp;url=$(url:escape)\" /></anchor></p>\n";

?>