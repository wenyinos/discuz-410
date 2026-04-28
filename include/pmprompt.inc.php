<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: pmprompt.inc.php,v $
	$Revision: 1.10 $
	$Date: 2006/02/24 06:15:28 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

if($ignorepm == 'yes') {

	$db->query("UPDATE {$tablepre}pms SET new='2' WHERE msgtoid='$discuz_uid' AND folder='inbox' AND new='1'");
	$db->query("UPDATE {$tablepre}members SET newpm='0' WHERE uid='$discuz_uid'");

} else {

	if($maxpmnum == 0) {

		$query = $db->query("DELETE FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox'", 'UNBUFFERED');
		$db->query("UPDATE {$tablepre}members SET newpm='0' WHERE uid='$discuz_uid'");

	} else {

		$newpmexists = 0;
		$pmlist = array();

		$query = $db->query("SELECT pmid, msgfrom, msgfromid, subject, message FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox' AND new='1'");
		if($newpmnum = $db->num_rows($query)) {
			$newpmexists = 1;
			if($newpmnum <= 10) {
				$pmdetail = '';
				while($pm = $db->fetch_array($query)) {
					$pm['subject'] = cutstr($pm['subject'], 20);
					$pm['message'] = dhtmlspecialchars(cutstr($pm['message'], 70));
					$pmlist[] = $pm;
				}
			}

			if($rewritestatus == 2 || $rewritestatus == 3) {
				$ignorelink = (defined('CURSCRIPT') && in_array(CURSCRIPT, array('forumdisplay', 'viewthread')) ? CURSCRIPT : 'index').'.php?ignorepm=yes';
			} else {
				$ignorelink = $PHP_SELF.'?ignorepm=yes';
			}

			foreach($_GET as $key => $val) {
				$ignorelink .= '&'.dhtmlspecialchars($key).'='.@rawurlencode($val);
			}
		} else {
			$db->query("UPDATE {$tablepre}members SET newpm='0' WHERE uid='$discuz_uid'");
		}

	}

}

?>