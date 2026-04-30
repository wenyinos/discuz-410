<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: common.inc.php,v $
	$Revision: 1.20.2.1 $
	$Date: 2006/03/03 04:40:21 $
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if(PHP_VERSION_ID >= 80000) {
	set_error_handler(function($errno, $errstr) {
		if(!($errno & (E_WARNING | E_NOTICE)) || !is_string($errstr)) {
			return FALSE;
		}
		static $compatWarnings = array(
			'Undefined variable $',
			'Undefined array key',
			'Undefined index',
			'Trying to access array offset on null',
			'Trying to access array offset on value of type null'
		);
		foreach($compatWarnings as $prefix) {
			if(strpos($errstr, $prefix) === 0) {
				return TRUE;
			}
		}
		return FALSE;
	});
}

$mtime = explode(' ', microtime());
$discuz_starttime = $mtime[1] + $mtime[0];

define('IN_DISCUZ', TRUE);
define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -7));
define('DISCUZ_AVATARSHOW', '3560626219401401200');

require_once DISCUZ_ROOT.'./include/global.func.php';

@extract(daddslashes($_COOKIE), EXTR_SKIP);
@extract(daddslashes($_POST), EXTR_SKIP);
@extract(daddslashes($_GET), EXTR_SKIP);
$_FILES = daddslashes($_FILES);

$charset = $dbcharset = '';
$plugins = $hooks = array();

require_once DISCUZ_ROOT.'./config.inc.php';
require_once DISCUZ_ROOT.'./include/db_'.$database.'.class.php';

if($attackevasive) {
	require_once DISCUZ_ROOT.'./include/security.inc.php';
}

$timestamp = time();
$PHP_SELF = $_SERVER['PHP_SELF'] ?? $_SERVER['SCRIPT_NAME'] ?? '';
$SCRIPT_FILENAME = str_replace('\\\\', '/', ($_SERVER['PATH_TRANSLATED'] ?? $_SERVER['SCRIPT_FILENAME'] ?? ''));
$boardurl = 'http://'.($_SERVER['HTTP_HOST'] ?? '').preg_replace("/\/+(api|archiver|wap)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/';

if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
	$onlineip = getenv('HTTP_CLIENT_IP');
} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
	$onlineip = getenv('HTTP_X_FORWARDED_FOR');
} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
	$onlineip = getenv('REMOTE_ADDR');
} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
	$onlineip = $_SERVER['REMOTE_ADDR'];
}

preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
$onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
unset($onlineipmatches);

$extrahead = '';
$_DCOOKIE = $_DSESSION = $_DCACHE = $_DPLUGIN = array();

$prelength = strlen($tablepre);
foreach($_COOKIE as $key => $val) {
	if(substr($key, 0, $prelength) == $tablepre) {
		$_DCOOKIE[(substr($key, $prelength))] = daddslashes($val);
	}
}
unset($prelength);


$cachelost = (@include DISCUZ_ROOT.'./forumdata/cache/cache_settings.php') ? '' : 'settings';
@extract($_DCACHE['settings'], EXTR_SKIP);

if($gzipcompress && function_exists('ob_gzhandler') && CURSCRIPT != 'wap') {
	ob_start('ob_gzhandler');
} else {
	$gzipcompress = 0;
	ob_start();
}

/* Avatar Show Data */
$avatarshow_license = strval(empty($avatarshow_license) ? DISCUZ_AVATARSHOW : $avatarshow_license);

if($loadctrl && (!defined('CURSCRIPT') || CURSCRIPT != 'wap') && substr(PHP_OS, 0, 3) != 'WIN') {
	if($fp = @fopen('/proc/loadavg', 'r')) {
		list($loadaverage) = explode(' ', fread($fp, 6));
		fclose($fp);
		if($loadaverage > $loadctrl) {
			header("HTTP/1.0 503 Service Unavailable");
			include DISCUZ_ROOT.'./include/serverbusy.htm';
			exit();
		}
	}
}

if(defined('CURSCRIPT') && in_array(CURSCRIPT, array('index', 'forumdisplay', 'viewthread', 'post', 'blog'))) {
	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/cache_'.CURSCRIPT.'.php') ? '' : ' '.CURSCRIPT;
}

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

$sid = daddslashes(($transsidstatus || (defined('CURSCRIPT') && CURSCRIPT == 'wap'))&& (isset($_GET['sid']) || isset($_POST['sid'])) ?
	(isset($_GET['sid']) ? $_GET['sid'] : $_POST['sid']) :
	(isset($_DCOOKIE['sid']) ? $_DCOOKIE['sid'] : ''));

$discuz_auth_key = md5($_DCACHE['settings']['authkey'].($_SERVER['HTTP_USER_AGENT'] ?? ''));
list($discuz_pw, $discuz_secques, $discuz_uid) = isset($_DCOOKIE['auth']) ? explode("\t", authcode($_DCOOKIE['auth'], 'DECODE')) : array('', '', 0);

$discuz_pw = addslashes($discuz_pw);
$discuz_secques = addslashes($discuz_secques);
$discuz_uid = intval($discuz_uid);

if(isset($_DCOOKIE['auth']) && !$discuz_uid) {
	clearcookies();
}

$newpm = $newpmexists = $sessionexists = $seccode = $bloguid = 0;
if($sid) {
	if($discuz_uid) {
		$query = $db->query("SELECT s.sid, s.styleid, s.groupid='6' AS ipbanned, s.pageviews AS spageviews, s.lastolupdate, s.seccode, m.uid AS discuz_uid,
			m.username AS discuz_user, m.password AS discuz_pw, m.secques AS discuz_secques, m.adminid, m.groupid, m.groupexpiry,
			m.extgroupids, m.email, m.timeoffset, m.tpp, m.ppp, m.posts, m.digestposts, m.oltime, m.pageviews, m.credits, m.extcredits1, m.extcredits2, m.extcredits3,
			m.extcredits4, m.extcredits5, m.extcredits6, m.extcredits7, m.extcredits8, m.timeformat, m.dateformat, m.pmsound,
			m.sigstatus, m.invisible, m.lastvisit, m.lastactivity, m.lastpost, m.newpm, m.accessmasks
			FROM {$tablepre}sessions s, {$tablepre}members m
			WHERE m.uid=s.uid AND s.sid='$sid' AND CONCAT_WS('.',s.ip1,s.ip2,s.ip3,s.ip4)='$onlineip' AND m.uid='$discuz_uid'
			AND m.password='$discuz_pw' AND m.secques='$discuz_secques'");
	} else {
		$query = $db->query("SELECT sid, uid AS sessionuid, groupid, groupid='6' AS ipbanned, pageviews AS spageviews, styleid, lastolupdate, seccode
			FROM {$tablepre}sessions WHERE sid='$sid' AND CONCAT_WS('.',ip1,ip2,ip3,ip4)='$onlineip'");
	}
	if($_DSESSION = $db->fetch_array($query)) {
		$sessionexists = 1;
		if(!empty($_DSESSION['sessionuid'])) {
			$query = $db->query("SELECT m.uid AS discuz_uid, m.username AS discuz_user, m.password AS discuz_pw,
				m.secques AS discuz_secques, m.adminid, m.groupid, m.groupexpiry, m.extgroupids, m.email, m.timeoffset,
				m.tpp, m.ppp, m.posts, m.digestposts, m.oltime, m.pageviews, m.credits, m.extcredits1, m.extcredits2, m.extcredits3, m.extcredits4, m.extcredits5,
				m.extcredits6, m.extcredits7, m.extcredits8, m.timeformat, m.dateformat, m.pmsound, m.sigstatus, m.invisible,
				m.lastvisit, m.lastactivity, m.lastpost, m.newpm, m.accessmasks
				FROM {$tablepre}members m WHERE uid='{$_DSESSION['sessionuid']}'");
			$_DSESSION = array_merge($_DSESSION, $db->fetch_array($query));
		}
	} else {
		$query = $db->query("SELECT sid, groupid, groupid='6' AS ipbanned, pageviews AS spageviews, styleid, lastolupdate, seccode
			FROM {$tablepre}sessions WHERE sid='$sid' AND CONCAT_WS('.',ip1,ip2,ip3,ip4)='$onlineip'");
		if($_DSESSION = $db->fetch_array($query)) {
			clearcookies();
			$sessionexists = 1;
		}
	}
}

if(!$sessionexists) {
	if($discuz_uid) {
		$query = $db->query("SELECT uid AS discuz_uid, username AS discuz_user, password AS discuz_pw, secques AS discuz_secques,
			adminid, groupid, groupexpiry, extgroupids, email, timeoffset, styleid, tpp, ppp, posts, digestposts, oltime, pageviews, credits,
			extcredits1, extcredits2, extcredits3, extcredits4, extcredits5, extcredits6, extcredits7, extcredits8, timeformat,
			dateformat, pmsound, sigstatus, invisible, lastvisit, lastactivity, lastpost, newpm, accessmasks
			FROM {$tablepre}members WHERE uid='$discuz_uid' AND password='$discuz_pw' AND secques='$discuz_secques'");
		if(!($_DSESSION = $db->fetch_array($query))) {
			clearcookies();
		}
	}

	if(ipbanned($onlineip)) {
		$_DSESSION['ipbanned'] = 1;
	}

	$_DSESSION['sid'] = random(6);
	$_DSESSION['seccode'] = random(4, 1);
}

$_DSESSION['dateformat'] = empty($_DSESSION['dateformat']) ? $_DCACHE['settings']['dateformat'] : $_DSESSION['dateformat'];
$_DSESSION['timeformat'] = empty($_DSESSION['timeformat']) ? $_DCACHE['settings']['timeformat'] : ($_DSESSION['timeformat'] == 1 ? 'h:i A' : 'H:i');
$_DSESSION['timeoffset'] = isset($_DSESSION['timeoffset']) && $_DSESSION['timeoffset'] != 9999 ? $_DSESSION['timeoffset'] : $_DCACHE['settings']['timeoffset'];

@extract($_DSESSION, EXTR_SKIP);

$lastvisit = empty($lastvisit) ? $timestamp - 86400 : $lastvisit;
$timenow = array('time' => gmdate("$dateformat $timeformat", $timestamp + 3600 * $timeoffset),
	'offset' => ($timeoffset >= 0 ? ($timeoffset == 0 ? '' : '+'.$timeoffset) : $timeoffset));

if(empty($discuz_uid) || empty($discuz_user)) {
	$discuz_user = $extgroupids = '';
	$discuz_uid = $adminid = $posts = $digestposts = $pageviews = $oltime = $invisible
		= $credits = $extcredits1 = $extcredits2 = $extcredits3 = $extcredits4
		= $extcredits5 = $extcredits6 = $extcredits7 = $extcredits8 = 0;
	$groupid = empty($groupid) || $groupid != 6 ? 7 : 6;
	$avatarshowid = 0;
} else {
	$discuz_userss = $discuz_user;
	$discuz_user = addslashes($discuz_user);
}

define('FORMHASH', formhash());
$attachdir = substr($attachdir, 0, 2) == './' ? DISCUZ_ROOT.$attachdir : $attachdir;

$statstatus && require_once DISCUZ_ROOT.'./include/counter.inc.php';
$rssauth = $rssstatus && $discuz_uid ? authcode("$discuz_uid\t$fid\t".substr(md5($discuz_pw.$discuz_secques), 0, 8), 'ENCODE', md5($_DCACHE['settings']['authkey'])) : '0';

$navtitle = $navigation = '';
$extra = isset($extra) && preg_match("/^[&=a-z0-9]+$/i", $extra) ? $extra : '';
$tpp = intval(empty($_DSESSION['tpp']) ? $topicperpage : $_DSESSION['tpp']);
$ppp = intval(empty($_DSESSION['ppp']) ? $postperpage : $_DSESSION['ppp']);

if($discuz_uid && $accessmasks) {
	$accessadd1 = ', a.allowview, a.allowpost, a.allowreply, a.allowgetattach, a.allowpostattach';
	$accessadd2 = "LEFT JOIN {$tablepre}access a ON a.uid='$discuz_uid' AND a.fid=f.fid";
} else {
	$accessadd1 = $accessadd2 = '';
}

if($discuz_uid && $adminid == 3) {
	$modadd1 = ', m.uid AS ismoderator';
	$modadd2 = "LEFT JOIN {$tablepre}moderators m ON m.uid='$discuz_uid' AND m.fid=f.fid";
} else {
	$modadd1 = $modadd2 = '';
}

$forum = array();

if(!empty($tid) || !empty($fid)) {

	if(empty($tid)) {
		$query = $db->query("SELECT f.fid, f.*, ff.* $accessadd1 $modadd1, f.fid AS fid
			FROM {$tablepre}forums f
			LEFT JOIN {$tablepre}forumfields ff ON ff.fid=f.fid $accessadd2 $modadd2
			WHERE f.fid='$fid'");

		$forum = $db->fetch_array($query);
	} else {
		$query = $db->query("SELECT t.tid, f.*, ff.* $accessadd1 $modadd1, f.fid AS fid
			FROM {$tablepre}threads t
			INNER JOIN {$tablepre}forums f ON f.fid=t.fid
			LEFT JOIN {$tablepre}forumfields ff ON ff.fid=f.fid $accessadd2 $modadd2
			WHERE t.tid='$tid' AND t.displayorder>='0' LIMIT 1");

		$forum = $db->fetch_array($query);
		$tid = $forum['tid'];
	}

	$fid = $forum['fid'];
	$typeid = isset($typeid) ? intval($typeid) : 0;
	$forum['ismoderator'] = !empty($forum['ismoderator']) || $adminid == 1 || $adminid == 2 ? 1 : 0;
	$forum['postcredits'] = $forum['postcredits'] ? unserialize($forum['postcredits']) : array();
	$forum['replycredits'] = $forum['replycredits'] ? unserialize($forum['replycredits']) : array();
	$forum['threadtypes'] = $forum['threadtypes'] ? unserialize($forum['threadtypes']) : array();

} else {

	$fid = $tid = 0;

}

$styleid = intval(!empty($_GET['styleid']) ? $_GET['styleid'] :
		(!empty($_POST['styleid']) ? $_POST['styleid'] :
		(!empty($_DSESSION['styleid']) ? $_DSESSION['styleid'] :
		$_DCACHE['settings']['styleid'])));

if(@!include DISCUZ_ROOT.'./forumdata/cache/style_'.intval(!empty($forum['styleid']) ? $forum['styleid'] : $styleid).'.php') {
	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/style_'.($styleid = $_DCACHE['settings']['styleid']).'.php') ? '' : ' style_'.$styleid;
}

$_DSESSION['groupid'] = $groupid = empty($ipbanned) ? (empty($groupid) ? 7 : intval($groupid)) : 6;
if(!@include DISCUZ_ROOT.'./forumdata/cache/usergroup_'.$groupid.'.php') {
	$query = $db->query("SELECT type FROM {$tablepre}usergroups WHERE groupid='$groupid'");
	$grouptype = $db->result($query, 0);
	if(!empty($grouptype)) {
		$cachelost .= ' usergroup_'.$groupid;
	} else {
		$grouptype = 'member';
	}
}

if($passport_status) {
	$passport_forward = rawurlencode('http://'.($_SERVER['HTTP_HOST'] ?? '').($_SERVER['REQUEST_URI'] ?? ''));
	$link_login = $passport_url.$passport_login_url.(strpos($passport_login_url, '?') === FALSE ? '?' : '&').'forward='.$passport_forward;
	$link_logout = $passport_url.$passport_logout_url.(strpos($passport_logout_url, '?') === FALSE ? '?' : '&').'forward='.$passport_forward;
	$link_register = $passport_url.$passport_register_url.(strpos($passport_register_url, '?') === FALSE ? '?' : '&').'forward='.$passport_forward;
} else {
	$link_login = 'logging.php?action=login';
	$link_logout = 'logging.php?action=logout';
	$link_register = 'register.php';
}

if($discuz_uid && $_DSESSION) {
	if(!empty($groupexpiry) && $groupexpiry < $timestamp && (!defined('CURSCRIPT') || (CURSCRIPT != 'wap' && CURSCRIPT != 'member'))) {
		header("Location: {$boardurl}member.php?action=groupexpiry");
		dexit();
	} elseif($grouptype && $groupid != getgroupid($discuz_uid, array
		(
		'type' => $grouptype,
		'creditshigher' => $groupcreditshigher,
		'creditslower' => $groupcreditslower
		), $_DSESSION)) {
		@extract($_DSESSION, EXTR_SKIP);
		$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/usergroup_'.intval($groupid).'.php') ? '' : ' usergroup_'.$groupid;
	}
}

if(!in_array($adminid, array(1, 2, 3))) {
	if(!$errorreport) {
		error_reporting(0);
	}
	$alloweditpost = $alloweditpoll = $allowstickthread = $allowmodpost = $allowdelpost = $allowmassprune
		= $allowrefund = $allowcensorword = $allowviewip = $allowbanip = $allowedituser = $allowmoduser
		= $allowbanuser = $allowpostannounce = $allowviewlog = $disablepostctrl = 0;
} elseif(isset($radminid) && $adminid != $radminid && $adminid != $groupid) {
	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/admingroup_'.intval($adminid).'.php') ? '' : ' admingroup_'.$groupid;
}

if($cachelost) {
	require_once DISCUZ_ROOT.'./include/cache.func.php';
	updatecache();
	dexit('Cache List: '.$cachelost.'<br>Caches successfully created, please refresh.');
}

if(!defined('CURSCRIPT') || CURSCRIPT != 'wap') {
	if($nocacheheaders) {
		@header("Expires: 0");
		@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
	}
	if($headercharset) {
		@header('Content-Type: text/html; charset='.$charset);
	}
	if(empty($_DCOOKIE['sid']) || $sid != $_DCOOKIE['sid']) {
		dsetcookie('sid', $sid, 604800);
	}
}

if($cronnextrun && $cronnextrun <= $timestamp) {
	require_once DISCUZ_ROOT.'./include/cron.func.php';
	runcron();
}

if(!empty($plugins['include']) && is_array($plugins['include'])) {
	foreach($plugins['include'] as $include) {
		if(!$include['adminid'] || ($include['adminid'] && $include['adminid'] >= $adminid)){
			@include_once DISCUZ_ROOT.'./plugins/'.$include['script'].'.inc.php';
		}
	}
}

if(isset($allowvisit) && $allowvisit == 0 && !(defined('CURSCRIPT') && CURSCRIPT == 'member' && $action == 'groupexpiry')) {
	showmessage('user_banned', NULL, 'HALTED');
} elseif(!((defined('CURSCRIPT') && in_array(CURSCRIPT, array('logging', 'wap', 'seccode'))) || $adminid == 1)) {
	if($bbclosed) {
		clearcookies();
		showmessage($closedreason ? $closedreason : 'board_closed', NULL, 'NOPERM');
	}
	periodscheck('visitbanperiods');
}

$advarray = $advlist = array();
if($advertisements) {
	require_once DISCUZ_ROOT.'./include/advertisements.inc.php';
}

if((!empty($advertisements['lateststarttime']) && $advertisements['lateststarttime'] <= $timestamp) ||
	(!empty($advertisements['latestendtime']) && $advertisements['latestendtime'] <= $timestamp)) {
	require_once DISCUZ_ROOT.'./include/cache.func.php';
	updatecache('settings');
}

if((!empty($fromuid) || !empty($fromuser)) && ($creditspolicy['promotion_visit'] || $creditspolicy['promotion_register'])) {
	require_once DISCUZ_ROOT.'/include/promotion.inc.php';
} else {
	$fromuid = $fromuser = '';
}

?>
