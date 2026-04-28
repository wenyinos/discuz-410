<?

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: promotion.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!empty($fromuid)) {
	$fromuid = intval($fromuid);
	$fromuser = '';
}

if(!$discuz_uid || !($fromuid == $discuz_uid || $fromuser == $discuz_user)) {

	if($creditspolicy['promotion_visit']) {
		$db->query("REPLACE INTO {$tablepre}promotions (ip, uid, username)
			VALUES ('$onlineip', '$fromuid', '$fromuser')");
	}

	if($creditspolicy['promotion_register']) {
		if($fromuser && empty($fromuid)) {
			if(empty($_DCOOKIE['promotion'])) {
				$query = $db->query("SELECT uid FROM {$tablepre}members WHERE username='$fromuser'");
				$fromuid = $db->result($query, 0);
			} else {
				$fromuid = intval($_DCOOKIE['promotion']);
			}
		}
		if($fromuid) {
			dsetcookie('promotion', ($_DCOOKIE['promotion'] = $fromuid), 1800);
		}
	}

}

?>