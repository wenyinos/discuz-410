<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: promotions_hourly.inc.php,v $
	$Revision: 1.6 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($creditspolicy['promotion_visit']) {

	$uidarray = $userarray = array();
	$query = $db->query("SELECT * FROM {$tablepre}promotions");
	while($promotion = $db->fetch_array($query)) {
		if($promotion['uid']) {
			$uidarray[] = $promotion['uid'];
		} elseif($promotion['username']) {
			$userarray[] = addslashes($promotion['username']);
		}
	}

	if($uidarray || $userarray) {

		if($userarray) {
			$query = $db->query("SELECT uid FROM {$tablepre}members WHERE username IN ('".implode('\',\'', $userarray)."')");
			while($member = $db->fetch_array($query)) {
				$uidarray[] = $member['uid'];
			}
		}

		$countarray = array();
		foreach(array_count_values($uidarray) as $uid => $count) {
			$countarray[$count][] = $uid;
		}

		foreach($countarray as $count => $uids) {
			updatecredits(implode('\',\'', $uids), $creditspolicy['promotion_visit'], $count);
		}

		$db->query("DELETE FROM {$tablepre}promotions");

	}

}

?>