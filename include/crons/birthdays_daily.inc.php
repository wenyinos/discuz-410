<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: birthdays_daily.inc.php,v $
	$Revision: 1.3.2.1 $
	$Date: 2006/03/10 02:42:17 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($bdaystatus) {

	$bdaymembers = array();
	$today = gmdate('m-d', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);

	$query = $db->query("SELECT uid, username, email, bday FROM {$tablepre}members WHERE bday>'1900-01-01' AND bday LIKE '%-$today' ORDER BY bday");
	while($member = $db->fetch_array($query)) {
		$birthyear = intval($member['bday']);
		$bdaymembers[] = '<a href="viewpro.php?uid='.$member['uid'].'" target="_blank" '.($birthyear ? 'alt="'.$member['bday'].'"' : '').'>'.$member['username'].'</a>';
		if($bdaystatus == 2 || $bdaystatus == 3) {
			sendmail($member['emails'], 'birthday_subject', 'birthday_message');
		}
	}

	if($bdaystatus == 1 || $bdaystatus == 3) {
		$GLOBALS['_DCACHE']['settings']['todaysbdays'] = implode(', ', $bdaymembers);
	}

}

?>