<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: login.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

require_once DISCUZ_ROOT.'./include/misc.func.php';

if(empty($logout)) {

	if(empty($username)) {

		echo "<p>$lang[username]:<input type=\"text\" name=\"username\" maxlength=\"15\" format=\"M*m\" /></p>\n".
			"<p>$lang[password]: <input type=\"password\" name=\"password\" value=\"\" format=\"M*m\" /></p>\n".
			"<p><anchor title=\"$lang[submit]\">$lang[submit]".
			"<go method=\"post\" href=\"index.php?action=login&amp;sid=$sid\">\n".
			"<postfield name=\"username\" value=\"$(username)\" />\n".
			"<postfield name=\"password\" value=\"$(password)\" />\n".
			"</go></anchor></p>\n";

	} else {

		$loginperm = logincheck();
		if(!$loginperm) {
			wapmsg('login_strike');
		}

		$password = md5($password);
		$usernameadd = preg_match("/^\d+$/", $username) ? "(uid='$username' OR username='$username')" : "username='$username'";
		$query = $db->query("SELECT uid AS discuz_uid, username AS discuz_user, password AS discuz_pw, secques AS discuz_secques, groupid, invisible
			FROM {$tablepre}members WHERE $usernameadd AND password='$password'");
		if($member = $db->fetch_array($query)) {
			@extract($member);
			/*
			$sid = random(6);
			$sessionexists = 0;
			*/
			dsetcookie('auth', authcode("$discuz_pw\t$discuz_secques\t$discuz_uid", 'ENCODE'), 2592000);
			wapmsg('login_succeed');
		} else {
			loginfailed($loginperm);
			wapmsg('login_invalid');
		}

	}
			
} else {

	/*
	$sid = random(6);
	$sessionexists = 0;
	clearcookies();
	*/

	$discuz_uid = 0;
	$discuz_user = '';
	$groupid = 7;

	wapmsg('logout_succeed');

}

?>