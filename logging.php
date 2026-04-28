<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: logging.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

define('CURSCRIPT', 'logging');

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/misc.func.php';

if($action == 'logout') {

	clearcookies();
	$groupid = 7;
	$discuz_uid = 0;
	$discuz_user = $discuz_pw = '';
	$styleid = $_DCACHE['settings']['styleid'];

	showmessage('logout_succeed', dreferer());

} elseif($action == 'login') {

	$field = isset($loginfield) && $loginfield == 'uid' ? 'uid' : 'username';

	//get secure code checking status (pos. -2)
	$seccodecheck = substr(sprintf('%05b', $seccodestatus), -2, 1);

	if(!submitcheck('loginsubmit', 1, $seccodecheck)) {

		$discuz_action = 6;

		$referer = dreferer();

		$thetimenow = '(GMT '.($timeoffset > 0 ? '+' : '').$timeoffset.') '.
			gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600).

		$styleselect = '';
		$query = $db->query("SELECT styleid, name FROM {$tablepre}styles WHERE available='1'");
		while($styleinfo = $db->fetch_array($query)) {
			$styleselect .= "<option value=\"$styleinfo[styleid]\">$styleinfo[name]</option>\n";
		}

		$_DCOOKIE['cookietime'] = isset($_DCOOKIE['cookietime']) ? $_DCOOKIE['cookietime'] : 2592000;
		$cookietimecheck = array((isset($_DCOOKIE['cookietime']) ? intval($_DCOOKIE['cookietime']) : 2592000) => 'checked');

		if($seccodecheck) {
			$seccode = random(4, 1);
		}

		include template('login');

	} else {

		$discuz_uid = 0;
		$discuz_user = $discuz_pw = $discuz_secques = $md5_password = '';
		$member = array();

		$loginperm = logincheck();
		if(!$loginperm) {
			showmessage('login_strike');
		}

		$secques = quescrypt($questionid, $answer);

		if(isset($loginauth)) {
			$field = 'username';
			$password = 'VERIFIED';
			list($username, $md5_password) = explode("\t", authcode($loginauth, 'DECODE'));
		} else {
			$md5_password = md5($password);
			$password = preg_replace("/^(.{".round(strlen($password) / 4)."})(.+?)(.{".round(strlen($password) / 6)."})$/s", "\\1***\\3", $password);
		}

		$query = $db->query("SELECT m.uid AS discuz_uid, m.username AS discuz_user, m.password AS discuz_pw, m.secques AS discuz_secques,
					m.adminid, m.groupid, m.styleid AS styleidmem, m.lastvisit, m.lastpost, u.allowinvisible
					FROM {$tablepre}members m LEFT JOIN {$tablepre}usergroups u USING (groupid)
					WHERE m.$field='$username'");

		$member = $db->fetch_array($query);

		/*
		if($bbclosed && $member['adminid'] != 1) {
			showmessage($closedreason ? $closedreason : 'board_closed');
		}
		*/

		if($member['discuz_uid'] && $member['discuz_pw'] == $md5_password) {

			if($member['discuz_secques'] == $secques) {

				extract($member);

				$discuz_userss = $discuz_user;
				$discuz_user = addslashes($discuz_user);

				if(($allowinvisible && $loginmode == 'invisible') || $loginmode == 'normal') {
					$db->query("UPDATE {$tablepre}members SET invisible='".($loginmode == 'invisible' ? 1 : 0)."' WHERE uid='$member[discuz_uid]'", 'UNBUFFERED');
				}

				$styleid = intval(empty($_POST['styleid']) ? ($styleidmem ? $styleidmem :
						$_DCACHE['settings']['styleid']) : $_POST['styleid']);
			
				$cookietime = intval(isset($_POST['cookietime']) ? $_POST['cookietime'] :
						($_DCOOKIE['cookietime'] ? $_DCOOKIE['cookietime'] : 0));

				dsetcookie('cookietime', $cookietime, 31536000);
				dsetcookie('auth', authcode("$discuz_pw\t$discuz_secques\t$discuz_uid", 'ENCODE'), $cookietime);

				$sessionexists = 0;

				if($groupid == 8) {
					showmessage('login_succeed_inactive_member', 'memcp.php');
				} else {
					showmessage('login_succeed', dreferer());
				}

			} elseif(empty($secques)) {

				$username = dhtmlspecialchars($member['discuz_user']);
				$loginmode = dhtmlspecialchars($loginmode);
				$styleid = intval($styleid);
				$cookietime = intval($cookietime);

				$loginauth = authcode(addslashes($member['discuz_user'])."\t".addslashes($member['discuz_pw']), 'ENCODE');

				include template('login_secques');
				dexit();

			}

		}
				
		$errorlog = $timestamp."\t".
			dhtmlspecialchars($member['discuz_user'] ? $member['discuz_user'] : stripslashes($username))."\t".
			$password."\t".
			($secques ? "Ques #".dhtmlspecialchars($questionid) : '')."\t".
			$onlineip."\n";

		loginfailed($loginperm);

		@$fp = fopen(DISCUZ_ROOT.'./forumdata/illegallog.php', 'a');
		@flock($fp, 2);
		@fwrite($fp, $errorlog);
		@fclose($fp);

		showmessage('login_invalid', NULL, 'HALTED');

	}

}

?>