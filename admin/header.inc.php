<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: header.inc.php,v $
	$Revision: 1.4 $
	$Date: 2006/02/23 13:44:02 $
*/

@header('Content-Type: text/html; charset='.$charset);

?>
<html>
<head>
<? @include template('css'); ?>
</head>

<body leftmargin="0" topmargin="0">
<table cellspacing="0" cellpadding="2" border="0" width="100%" height="100%" bgcolor="<?=ALTBG2?>">
<tr valign="middle" class="smalltxt">
<td width="33%"><a href="http://www.Discuz.com" target="_blank">Discuz! <?=$version?> <?=$lang['admincp']?></a></td>
<td width="33%" align="center"><a href="http://www.Discuz.net" target="_blank"><?=$lang['header_offical']?></a></td>
<td width="34%" align="right"><a href="index.php" target="_blank"><?=$lang['header_home']?></a></TD>
</tr>
</table>
</body></html>
