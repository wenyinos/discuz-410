<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: admincp.php,v $
	$Revision: 1.11 $
	$Date: 2006/02/23 13:44:02 $
*/


require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./admin/global.func.php';

@include_once DISCUZ_ROOT.'./forumdata/cache/cache_bbcodes.php';

if(empty($action) || isset($frames)) {

	parse_str($_SERVER['QUERY_STRING'], $getarray);

	$extra = $and = '';
	foreach($getarray as $key => $value) {
		if(!in_array($key, array('sid', 'frames'))) {
			$extra .= $and.$key.'='.rawurlencode($value);
			$and = '&';
		}
	}
	$extra = $extra ? $extra : 'action=home';

?>
<html>
<head>
<title>Discuz! Administrator's Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
</head>

<frameset cols="160,*" frameborder="no" border="0" framespacing="0" rows="*">
<frame name="menu" noresize scrolling="yes" src="admincp.php?action=menu&sid=<?=$sid?>">
<frameset rows="20,*" frameborder="no" border="0" framespacing="0" cols="*">
<frame name="header" noresize scrolling="no" src="admincp.php?action=header&sid=<?=$sid?>">
<frame name="main" noresize scrolling="yes" src="admincp.php?<?=$extra?>&sid=<?=$sid?>">
</frameset></frameset></html>
<?

	exit();

}

require_once DISCUZ_ROOT.'./include/cache.func.php';

$discuz_action = 211;

include language('admincp');

if($action == 'menu') {
	require_once DISCUZ_ROOT.'./admin/menu.inc.php';
} elseif($action == 'header') {
	require_once DISCUZ_ROOT.'./admin/header.inc.php';
} else {

	if($adminid <= 0) {

		$cpaccess = 0;

	} else {

		if(!$discuz_secques && $forcesecques) {
			cpheader();
			cpmsg('secques_invalid');
		}

		if($adminipaccess && $adminid == 1 && !ipaccess($onlineip, $adminipaccess)) {
			$cpaccess = 2;
		} else {
			$query = $db->query("SELECT errorcount FROM {$tablepre}adminsessions WHERE uid='$discuz_uid' AND ip='$onlineip' AND dateline+1800>'$timestamp'", 'SILENT');
			if($db->error()) {
				$db->query("DROP TABLE IF EXISTS {$tablepre}adminsessions");
				$db->query("CREATE TABLE {$tablepre}adminsessions (uid mediumint(8) UNSIGNED NOT NULL default '0', ip char(15) NOT NULL default '', dateline int(10) unsigned NOT NULL default '0', errorcount tinyint(1) NOT NULL default '0')");
				$cpaccess = 1;
			} else {
				if($session = $db->fetch_array($query)) {
					if($session['errorcount'] == -1) {
						$db->query("UPDATE {$tablepre}adminsessions SET dateline='$timestamp' WHERE uid='$discuz_uid'", 'UNBUFFERED');
						$cpaccess = 3;
					} elseif($session['errorcount'] <= 3) {
						$cpaccess = 1;
					} else {
						$cpaccess = 0;
					}
				} else {
					$db->query("DELETE FROM {$tablepre}adminsessions WHERE uid='$discuz_uid' OR dateline+1800<'$timestamp'");
					$db->query("INSERT INTO {$tablepre}adminsessions (uid, ip, dateline, errorcount)
						VALUES ('$discuz_uid', '$onlineip', '$timestamp', '0')");
					$cpaccess = 1;
				}
			}
		}

	}

	if($action && !in_array($action, array('main', 'header', 'menu', 'illegallog', 'ratelog', 'modslog', 'banlog', 'cplog', 'errorlog'))) {
		switch($cpaccess) {
			case 0:
				$extra = 'PERMISSION DENIED';
				break;
			case 1:
				$extra = 'AUTHENTIFICATION(ERROR #'.intval($session['errorcount']).')';
				break;
			case 2:
				$extra = 'IP ACCESS DENIED';
				break;
			case 3:
				$extra = $semicolon = '';
				if(is_array($_GET)) {
					foreach(array_merge($_GET, $_POST) as $key => $val) {
						if(!in_array($key, array('action', 'sid', 'formhash', 'admin_password')) && $val) {
							$extra .= $semicolon.$key.'=';
							if(is_array($val)) {
								$extra .= 'Array(';
								foreach($val as $arraykey => $arrayval) {
									$extra .= $arraykey.'='.cutstr($arrayval, 15).'; ';
								}
								$extra .= ')';
							} else {
								$extra .= cutstr($val, 15);
							}
							$semicolon = '; ';
						}
					}
					$extra = nl2br(htmlspecialchars($extra));
				}
				break;
		}

		@$fp = fopen(DISCUZ_ROOT.'./forumdata/cplog.php', 'a');
		@flock($fp, 2);
		@fwrite($fp, "$timestamp\t".dhtmlspecialchars($discuz_userss)."\t$adminid\t$onlineip\t".dhtmlspecialchars($action)."\t$extra\n");
		@fclose($fp);
	}

	if($cpaccess == 0) {

		clearcookies();
		cpheader();
		cpmsg('noaccess');

	} elseif($cpaccess == 1) {

		if(!$admin_password || md5($admin_password) != $discuz_pw) {
			if($admin_password) {
				$db->query("UPDATE {$tablepre}adminsessions SET errorcount=errorcount+1 WHERE uid='$discuz_uid'");
			}
			$action = empty($action) ? 'home' : $action;
			cpheader();

?>
<br><br><br><br><br><br>
<form method="post" name="login" action="admincp.php?<?=$_SERVER['QUERY_STRING']?>">
<input type="hidden" name="sid" value="<?=$sid?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="60%" align="center" class="tableborder">
<tr class="header"><td colspan="2"><?=$lang['password_required']?></td></tr>
<tr><td class="altbg1" width="25%"><?=$lang['username']?>:</td><td class="altbg2"><?=$discuz_user?> <a href="logging.php?action=logout&referer=index.php" target="_blank">[<?=$lang['menu_logout']?>]</a></td></tr>
<tr><td class="altbg1" width="25%"><?=$lang['password']?>:</td><td class="altbg2"><input type="password" name="admin_password" size="25"></td></tr>
</td></tr></table>
<br><center><input type="submit" value="<?=$lang['submit']?>"></center></form>
<br><br>
<script language="JavaScript">
document.login.admin_password.focus();
</script>
<?

			cpfooter();
			dexit();
		} else {
			$db->query("UPDATE {$tablepre}adminsessions SET errorcount='-1' WHERE uid='$discuz_uid'");
		}

	} elseif($cpaccess == 2) {

		cpheader();
		cpmsg('noaccess_ip');

	}

	$cpscript = '';

	if($adminid == 1) {

		if($action == 'home') {
			$cpscript = 'home';
		} elseif($action == 'settings') {
			$cpscript = 'settings';
		} elseif($action == 'passport' || $action == 'siteengine' || $action == 'shopex') {
			$cpscript = 'passport';
		} elseif($action == 'avatarshow_config' || $action == 'avatarshow_register') {
			$cpscript = 'avatarshow';
		} elseif($action == 'qihoo_config' || $action == 'qihoo_topics') {
			$cpscript = 'qihoo';
		} elseif($action == 'forumadd' || $action == 'forumsedit' || $action == 'forumsmerge' || $action == 'forumdetail' || $action == 'forumdelete' || $action == 'moderators' || $action == 'threadtypes' || $action=='forumcopy') {
			$cpscript = 'forums';
		} elseif($action == 'memberadd' || $action == 'members' || $action == 'membersmerge' || $action == 'editgroups' || $action == 'access' || $action == 'editcredits' || $action == 'editmedals' || $action == 'memberprofile' || $action == 'profilefields' || $action == 'ipban') {
			$cpscript = 'members';
		} elseif($action == 'usergroups' || $action == 'admingroups' || $action == 'ranks') {
			$cpscript = 'groups';
		} elseif($action == 'announcements') {
			$cpscript = 'announcements';
		} elseif($action == 'styles') {
			$cpscript = 'styles';
		} elseif($action == 'templates' || $action == 'tpladd' || $action == 'tpledit') {
			$cpscript = 'templates';
		} elseif($action == 'modmembers' || $action == 'modthreads' || $action == 'modreplies') {
			$cpscript = 'moderate';
		} elseif($action == 'recyclebin') {
			$cpscript = 'recyclebin';
		} elseif($action == 'alipay' || $action == 'orders') {
			$cpscript = 'ecommerce';
		} elseif($action == 'forumlinks' || $action == 'onlinelist' || $action == 'medals' || $action == 'censor' || $action == 'discuzcodes' || $action == 'smilies' || $action == 'attachtypes' || $action == 'crons' || $action == 'creditslog' || $action == 'logout') {
			$cpscript = 'misc';
		} elseif($action == 'adv' || $action == 'advadd' || $action == 'advedit') {
			$cpscript = 'advertisements';
		} elseif($action == 'export' || $action == 'import' || $action == 'runquery' || $action == 'optimize') {
			$cpscript = 'database';
		} elseif($action == 'attachments') {
			$cpscript = 'attachments';
		} elseif($action == 'counter') {
			$cpscript = 'counter';
		} elseif($action == 'threads') {
			$cpscript = 'threads';
		} elseif($action == 'prune' || $action == 'pmprune') {
			$cpscript = 'prune';
		} elseif($action == 'updatecache' || $action == 'jswizard' || $action == 'fileperms') {
			$cpscript = 'tools';
		} elseif($action == 'plugins' || $action == 'pluginsconfig' || $action == 'pluginsedit' || $action == 'pluginhooks' || $action == 'pluginvars') {
			$cpscript = 'plugins';
		} elseif($action == 'illegallog' || $action == 'ratelog' || $action == 'modslog' || $action == 'medalslog' || $action == 'banlog' || $action == 'cplog' || $action == 'errorlog') {
			$cpscript = 'logs';
		}

		if($radminid != $groupid) {
			$query = $db->query("SELECT disabledactions FROM {$tablepre}adminactions WHERE admingid='$groupid'");
			$dactionarray = ($dactionarray = unserialize($db->result($query, 0))) ? $dactionarray : array();
			if(in_array($action, $dactionarray)) {
				cpheader();
				cpmsg('action_noaccess');
			}
		}

	} elseif($adminid == 2 || $adminid == 3) {

		if($action == 'home') {
			$cpscript = 'home';
		} elseif((($allowedituser || $allowbanuser) && $action == 'editmember') || ($allowbanip && $action == 'ipban')) {
			$cpscript = 'members';
		} elseif($action == 'forumrules') {
			$cpscript = 'forums';
		} elseif($allowpostannounce && $action == 'announcements') {
			$cpscript = 'announcements';
		} elseif(($allowmoduser && $action == 'modmembers') || ($allowmodpost && ($action == 'modthreads' || $action == 'modreplies'))) {
			$cpscript = 'moderate';
		} elseif(($allowcensorword && $action == 'censor') || $action == 'logout') {
			$cpscript = 'misc';
		} elseif($allowmassprune && $action == 'prune') {
			$cpscript = 'prune';
		} elseif($action == 'plugins') {
			$cpscript = 'plugins';
		} elseif($allowviewlog && ($action == 'ratelog' || $action == 'modslog' || $action == 'banlog')) {
			$cpscript = 'logs';
		}

	}

	if($cpscript) {
		require_once DISCUZ_ROOT.'./admin/'.$cpscript.'.inc.php';
	} else {
		cpheader();
		cpmsg('noaccess');
	}

	if($action != 'menu' && $action != 'header') {
		cpfooter();
	}

}
output();

?>