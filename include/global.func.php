<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: global.func.php,v $
	$Revision: 1.26 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function authcode ($string, $operation, $key = '') {

	$key = md5($key ? $key : $GLOBALS['discuz_auth_key']);
	$key_length = strlen($key);

	$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
	$string_length = strlen($string);

	$rndkey = $box = array();
	$result = '';

	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($key[$i % $key_length]);
		$box[$i] = $i;
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
			return substr($result, 8);
		} else {
			return '';
		}
	} else {
		return str_replace('=', '', base64_encode($result));
	}

}

function avatarshow($id, $gender = 0) {
	global $discuz_uid, $avatarshowid, $avatarshow_license, $avatarshowlink, $avatarshowheight, $avatarshowwidth;
	return '<iframe marginwidth="0" marginheight="0" frameborder="0" scrolling="no" height="'.$avatarshowheight.'" width="'.$avatarshowwidth.'" src="api/avatarshow.php?uid='.$discuz_uid.'&thisid='.$avatarshowid.'&id='.$id.'&license='.$avatarshow_license.'&width='.$avatarshowwidth.'&height='.$avatarshowheight.'gender='.$gender.'&link='.$avatarshowlink.'"></iframe>';
}

function clearcookies() {
	global $timestamp, $cookiepath, $cookiedomain, $discuz_uid, $discuz_user, $discuz_pw, $discuz_secques, $adminid, $groupid, $credits;
	dsetcookie('auth', '', -86400 * 365);
	dsetcookie('visitedfid', '', -86400 * 365);

	// clear cookies defined in older version (transitional operation)
	dsetcookie('_discuz_uid', '', -86400 * 365, 0);
	dsetcookie('_discuz_pw', '', -86400 * 365, 0);
	dsetcookie('_discuz_secques', '', -86400 * 365, 0);
	dsetcookie('onlinedetail', '', -86400 * 365, 0);
	// end

	$discuz_uid = $adminid = $credits = 0;
	$discuz_user = $discuz_pw = $discuz_secques = '';
}

function checklowerlimit($creditsarray, $coef = 1) {
	if(is_array($creditsarray)) {
		global $extcredits, $id;
		foreach($creditsarray as $id => $addcredits) {
			if($addcredits * $coef < 0 && $GLOBALS['extcredits'.$id] < $extcredits[$id]['lowerlimit']) {
				showmessage('credits_policy_lowerlimit');
			}
		}
	}
}

function cutstr($string, $length) {
	$strcut = '';
	if(strlen($string) > $length) {
		for($i = 0; $i < $length - 3; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
		return $strcut.' ...';
	} else {
		return $string;
	}
}

function daddslashes($string, $force = 0) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = daddslashes($val, $force);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}

function debuginfo() {
	if($GLOBALS['debug']) {
		global $db, $discuz_starttime, $debuginfo;
		$mtime = explode(' ', microtime());
		$debuginfo = array('time' => number_format(($mtime[1] + $mtime[0] - $discuz_starttime), 6), 'queries' => $db->querynum);
		return TRUE;
	} else {
		return FALSE;
	}
}

function dexit($message = '') {
	echo $message;
	output();
	exit();
}

function dinterpolate($string) {
	// 安全替代 eval() 的变量插值函数
	// 支持 $var、{$var}、$arr[key] 三种模式
	return preg_replace_callback('/\{?\$(\w+(?:\[[\w\'\"]+\])*)\}?/', function($m) {
		$path = $m[1];
		if(strpos($path, '[') !== false) {
			// 处理 $arr[key] 模式
			preg_match('/^(\w+)\[([\'"]?)(\w+)\2\]$/', $path, $pm);
			if($pm && isset($GLOBALS[$pm[1]][$pm[3]])) {
				return $GLOBALS[$pm[1]][$pm[3]];
			}
		} else {
			if(isset($GLOBALS[$path])) {
				return $GLOBALS[$path];
			}
		}
		return $m[0];
	}, $string);
}

function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
			str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

function disuploadedfile($file) {
	return function_exists('is_uploaded_file') && (is_uploaded_file($file) || is_uploaded_file(str_replace('\\\\', '\\', $file)));
}

function dreferer($default = 'index.php') {
	global $referer;

	if(empty($referer) && isset($GLOBALS['_SERVER']['HTTP_REFERER'])) {
		$referer = preg_replace("/([\?&])((sid\=[a-z0-9]{6})(&|$))/i", '\\1', $GLOBALS['_SERVER']['HTTP_REFERER']);
		$referer = substr($referer, -1) == '?' ? substr($referer, 0, -1) : $referer;
	} else {
		$referer = dhtmlspecialchars($referer);
	}

	if(!preg_match("/(\.php|[a-z]+(\-\d+)+\.html)/", $referer) || strpos($referer, 'logging.php')) {
		$referer = $default;
	}
	return $referer;
}

function dsetcookie($var, $value, $life = 0, $prefix = 1) {
	global $tablepre, $cookiedomain, $cookiepath, $timestamp, $_SERVER;
	setcookie(($prefix ? $tablepre : '').$var, $value,
		$life ? $timestamp + $life : 0, $cookiepath,
		$cookiedomain, ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 1 : 0);
}

function emailconv($email, $tolink = 1) {
	$email = str_replace(array('@', '.'), array('&#64;', '&#46;'), $email);
	return $tolink ? '<a href="mailto: '.$email.'">'.$email.'</a>': $email;
}

function errorlog($type, $message, $halt = 1) {
	global $timestamp, $discuz_userss;
	@$fp = fopen(DISCUZ_ROOT.'./forumdata/errorlog.php', 'a');
	@fwrite($fp, "$timestamp\t$type\t$discuz_userss\t".str_replace(array("\r", "\n"), array(' ', ' '), trim(dhtmlspecialchars($message)))."\n");
	@fclose($fp);
	if($halt) {
		dexit();
	}
}

function fileext($filename) {
	return trim(substr(strrchr($filename, '.'), 1));
}

function formhash() {
	global $discuz_user, $discuz_uid, $discuz_pw, $timestamp;
	return substr(md5(substr($timestamp, 0, -7).$discuz_user.$discuz_uid.$discuz_pw), 8, 8);
}

function forumperm($permstr) {
	global $groupid, $extgroupids;

	$groupidarray = array($groupid);
	foreach(explode("\t", $extgroupids) as $extgroupid) {
		if($extgroupid = intval(trim($extgroupid))) {
			$groupidarray[] = $extgroupid;
		}
	}
	return preg_match("/(^|\t)(".implode('|', $groupidarray).")(\t|$)/", $permstr);
}

function getgroupid($uid, $group, &$member) {
	global $creditsformula, $db, $tablepre;

	if(!empty($creditsformula)) {
		$updatearray = array();
		eval("\$credits = round($creditsformula);");

		if($credits != $member['credits']) {
			$updatearray[] = "credits='$credits'";
		}
		if($group['type'] == 'member' && !($member['credits'] >= $group['creditshigher'] && $member['credits'] < $group['creditslower'])) {
			$query = $db->query("SELECT groupid FROM {$tablepre}usergroups WHERE type='member' AND {$member['credits']}>=creditshigher AND {$member['credits']}<creditslower LIMIT 1");
			if($db->num_rows($query)) {
				$member['groupid'] = $db->result($query, 0);
				$updatearray[] = "groupid='{$member['groupid']}'";
			}
		}

		if($updatearray) {
			$db->query("UPDATE {$tablepre}members SET ".implode(', ', $updatearray)." WHERE uid='$uid'");
		}
	}

	return $member['groupid'];
}

function groupexpiry($terms) {
	$terms = is_array($terms) ? $terms : unserialize($terms);
	$groupexpiry = isset($terms['main']['time']) ? intval($terms['main']['time']) : 0;
	if(is_array($terms['ext'])) {
		foreach($terms['ext'] as $expiry) {
			if((!$groupexpiry && $expiry) || $expiry < $groupexpiry) {
				$groupexpiry = $expiry;
			}
		}
	}
	return $groupexpiry;
}

function image($imageinfo, $basedir = '', $remark = '') {
	if($basedir) {
		$basedir .= '/';
	}
	if(strstr($imageinfo, ',')) {
		$flash = explode(",", $imageinfo);
		return "<embed src=\"$basedir".trim($flash[0])."\" width=\"".trim($flash[1])."\" height=\"".trim($flash[2])."\" type=\"application/x-shockwave-flash\" $remark></embed>";
	} else {
		return "<img src=\"$basedir$imageinfo\" $remark border=\"0\">";
	}
}

function ipaccess($ip, $accesslist) {
	return preg_match("/^(".str_replace(array("\r\n", ' '), array('|', ''), preg_quote($accesslist, '/')).")/", $ip);
}

function ipbanned($onlineip) {
	global $ipaccess, $timestamp, $cachelost;

	if($ipaccess && !ipaccess($onlineip, $ipaccess)) {
		return TRUE;
	}

	$cachelost .= (@include DISCUZ_ROOT.'./forumdata/cache/cache_ipbanned.php') ? '' : ' ipbanned';
	if(empty($_DCACHE['ipbanned'])) {
		return FALSE;
	} else {
		if($_DCACHE['ipbanned']['expiration'] < $timestamp) {
			@unlink(DISCUZ_ROOT.'./forumdata/cache/cache_ipbanned.php');
		}
		return preg_match("/^(".$_DCACHE['ipbanned']['regexp'].")$/", $onlineip);
	}
}

function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/", $email);
}

function ispage($number) {
	return !empty($number) && preg_match ("/^([0-9]+)$/", $number);
}

function language($file, $templateid = 0, $tpldir = '') {
	$tpldir = $tpldir ? $tpldir : TPLDIR;
	$templateid = $templateid ? $templateid : TEMPLATEID;

	$languagepack = DISCUZ_ROOT.'./'.$tpldir.'/'.$file.'.lang.php';
	if(file_exists($languagepack)) {
		return $languagepack;
	} elseif($templateid != 1 && $tpldir != './templates/default') {
		return language($file, 1, './templates/default');
	} else {
		return FALSE;
	}
}

function multi($num, $perpage, $curpage, $mpurl, $maxpages = 0) {
	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? '&' : '?';
	if($num > $perpage) {
		$page = 10;
		$offset = 2;

		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}

		$multipage = ($curpage - $offset > 1 && $pages > $page ? '<td>&nbsp;<a href="'.$mpurl.'page=1"><b>|</b>&lt;&nbsp;</td>' : '').
			($curpage > 1 ? '<td>&nbsp;<a href="'.$mpurl.'page='.($curpage - 1).'">&lt;</a>&nbsp;</td>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<td bgcolor="'.ALTBG2.'">&nbsp;<u><b>'.$i.'</b></u>&nbsp;</td>' :
				'<td>&nbsp;<a href="'.$mpurl.'page='.$i.'">'.$i.'</a>&nbsp;</td>';
		}

		$multipage .= ($curpage < $pages ? '<td>&nbsp;<a href="'.$mpurl.'page='.($curpage + 1).'">&gt;</a>&nbsp;</td>' : '').
			($to < $pages ? '<td>&nbsp;<a href="'.$mpurl.'page='.$pages.'">&gt;<b>|</b></a>&nbsp;</td>' : '').
			($curpage == $maxpages ? '<td>&nbsp;<a href="misc.php?action=maxpages&pages='.$maxpages.'">&gt;<b>?</b></a>&nbsp;</td>' : '').
			($pages > $page ? '<td style="padding: 0"><input type="text" name="custompage" size="2" style="border: 1px solid '.BORDERCOLOR.'" onKeyDown="if(event.keyCode==13) {window.location=\''.$mpurl.'page=\'+this.value; this.form.submit=false;}"></td>' : '');

		$multipage = $multipage ? '<table cellspacing="0" cellpadding="0" border="0"><tr><td height="3"></td></tr><tr><td>'.
			'<table cellspacing="'.INNERBORDERWIDTH.'" cellpadding="2" class="tableborder"><tr bgcolor="'.ALTBG1.'" class="smalltxt"><td class="header">&nbsp;'.$num.'&nbsp;</td><td class="header">&nbsp;'.$curpage.'/'.$realpages.'&nbsp;</td>'.$multipage.'</tr></table>'.
			'</td></tr><tr><td height="3"></td></tr></table>' : '';
	}
	return $multipage;
}

function output() {
	global $sid, $transsidstatus, $rewritestatus;

	if(($transsidstatus = empty($GLOBALS['_DCOOKIE']['sid']) && $transsidstatus) || in_array($rewritestatus, array(2, 3))) {
		$content = ob_get_contents();

		if($transsidstatus) {
			$content = preg_replace_callback(
				"/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/is",
				function($m) { return transsid($m[3], '<a'.$m[1].'href='.$m[2]); },
				$content
			);
			$content = preg_replace(
				"/(\<form.+?\>)/is",
				"\\1\n<input type=\"hidden\" name=\"sid\" value=\"$sid\">",
				$content
			);
		} else {
			$content = preg_replace_callback(
				"/\<a href\=\"forumdisplay\.php\?fid\=(\d+)(&page\=(\d+))?\"([^\>]*)\>/i",
				function($m) { return rewrite_forum($m[1], $m[3], $m[4]); },
				$content
			);
			$content = preg_replace_callback(
				"/\<a href\=\"viewthread\.php\?tid\=(\d+)(&extra\=page\%3D(\d+))?(&page\=(\d+))?\"([^\>]*)\>/i",
				function($m) { return rewrite_thread($m[1], $m[5], $m[3], $m[6]); },
				$content
			);
			$content = preg_replace_callback(
				"/\<a href\=\"viewpro\.php\?(uid\=(\d+)|username\=([^&]+?))\"([^\>]*)\>/i",
				function($m) { return rewrite_profile($m[2], $m[3], $m[4]); },
				$content
			);
		}

		ob_end_clean();
		$GLOBALS['gzipcompress'] ? ob_start('ob_gzhandler') : ob_start();

		echo $content;
	}
}

function rewrite_thread($tid, $page = 0, $prevpage = 0, $extra = '') {
	return '<a href="thread-'.$tid.'-'.($page ? $page : 1).'-'.($prevpage ? $prevpage : 1).'.html"'.stripslashes($extra).'>';
}

function rewrite_forum($fid, $page = 0, $extra = '') {
	return '<a href="forum-'.$fid.'-'.($page ? $page : 1).'.html"'.stripslashes($extra).'>';
}


function rewrite_profile($uid, $username, $extra = '') {
	return '<a href="profile-'.($uid ? 'uid-'.$uid : 'username-'.$username).'.html"'.stripslashes($extra).'>';
}

function payment($amount, $orderid) {
	global $bbname, $boardurl, $extcredits, $creditstrans, $timestamp, $authkey, $ec_ratio, $ec_account, $ec_securitycode, $discuz_userss, $onlineip;

	$ec_securitycode = authcode($ec_securitycode, 'DECODE', $authkey);

	$params = array
		(
		'subject'	=> $bbname.' - '.$discuz_userss.' - ���ֳ�ֵ('.$boardurl.')',
		'body'		=> '��̳���ֳ�ֵ '.$extcredits[$creditstrans]['title'].' '.intval($amount * $ec_ratio).' '.$extcredits[$creditstrans]['unit'].' ('.$onlineip.')',
		'order_no'	=> $orderid,
		'date'		=> gmdate("Ymd", $timestamp + 8 * 3600),
		'price'		=> $amount,
		'type'		=> 2,
		'number'	=> 1,
		'transport'	=> 3,
		'seller'	=> $ec_account,
		'partner'	=> '20880020258585430156'
		);

	$ac = '';
	$url = 'https://www.alipay.com/trade/direct_pay.htm?';

	foreach($params as $key => $val) {
		$ac .= $key.$val;
		$url .= $key.'='.rawurlencode($val).'&';
	}

	$ac .= $ec_securitycode;
	$url .= 'ac='.md5($ac);

	return $url;
}

function payto($seller, $detail) {
	$detailarray = array();
	foreach(array_merge($detail, array('partner' => '20880020258585430156', 'readonly' => 'true')) as $key => $val) {
		if($val = trim($val)) {
			$detailarray[] = $key.'='.rawurlencode($val);
		}
	}
	return '<a href="https://www.alipay.com/payto:'.$seller.'?'.implode('&', $detailarray).'" target="_blank"><img src="'.IMGDIR.'/alipaybutton.gif" border="0"></a>';
}

function periodscheck($periods, $showmessage = 1) {
	global $timestamp, $disableperiodctrl, $_DCACHE, $banperiods;

	if(!$disableperiodctrl && $_DCACHE['settings'][$periods]) {
		$now = gmdate('G.i', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
		foreach(explode("\r\n", str_replace(':', '.', $_DCACHE['settings'][$periods])) as $period) {
			list($periodbegin, $periodend) = explode('-', $period);
			if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($oeriodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
				$banperiods = str_replace("\r\n", ', ', $_DCACHE['settings'][$periods]);
				if($showmessage) {
					showmessage('period_nopermission', NULL, 'NOPERM');
				} else {
					return TRUE;
				}
			}
		}
	}
	return FALSE;
}

function quescrypt($questionid, $answer) {
	return $questionid > 0 && $answer != '' ? substr(md5($answer.md5($questionid)), 16, 8) : '';
}

function random($length, $numeric = 0) {
	mt_srand((double)microtime() * 1000000);
	if($numeric) {
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
}

function sendmail($email_to, $email_subject, $email_message, $email_from = '') {
	extract($GLOBALS, EXTR_SKIP);
	require DISCUZ_ROOT.'./include/sendmail.inc.php';
}

function sendpm($toid, $subject, $message, $fromid = '', $from = '') {
	extract($GLOBALS, EXTR_SKIP);
	include language('pms');

	if(isset($language[$subject])) {
		$subject = addslashes(dinterpolate($language[$subject]));
	}
	if(isset($language[$message])) {
		$message = addslashes(dinterpolate($language[$message]));
	}

	if(!$fromid && !$from) {
		$fromid = $discuz_uid;
		$from = $discuz_user;
	}

	foreach(explode(',', $toid) as $uid) {
		$db->query("INSERT INTO {$tablepre}pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
			VALUES ('$from', '$fromid', '$uid', 'inbox', '1', '$subject', '$timestamp', '$message')");
	}

	$db->query("UPDATE {$tablepre}members SET newpm='1' WHERE uid IN ($toid)");
}

function showmessage($show_message, $url_forward = '', $extra = '') {
	extract($GLOBALS, EXTR_SKIP);

	global $extrahead, $discuz_action, $debuginfo, $seccode, $fid, $tid;

	if(in_array($extra, array('HALTED', 'NOPERM'))) {
		$fid = $tid = 0;
		$discuz_action = 254;
	} else {
		$discuz_action = 255;
	}

	include language('messages');

	if(isset($language[$show_message])) {
		$show_message = dinterpolate($language[$show_message]);
	}
	$extrahead .= $url_forward ? '<meta http-equiv="refresh" content="3;url='.
		(empty($_DCOOKIE['sid']) && $transsidstatus ? transsid($url_forward) : $url_forward).
		'">' : '';

	if($extra == 'NOPERM' && !$passport_status) {
		//get secure code checking status (pos. -2)
		if($seccodecheck = substr(sprintf('%05b', $seccodestatus), -2, 1)) {
			$seccode = random(4, 1);
		}
		include template('nopermission');
	} else {
		include template('showmessage');
	}

	dexit();
}

function showstars($num) {
	global $starthreshold;

	$alt = 'alt="Rank: '.$num.'"';
	if(empty($starthreshold)) {
		for($i = 0; $i < $num; $i++) {
			echo '<img src="'.IMGDIR.'/star_level1.gif" '.$alt.'>';
		}
	} else {
		for($i = 3; $i > 0; $i--) {
			$numlevel = intval($num / pow($starthreshold, ($i - 1)));
			$num = ($num % pow($starthreshold, ($i - 1)));
			for($j = 0; $j < $numlevel; $j++) {
				echo '<img src="'.IMGDIR.'/star_level'.$i.'.gif" '.$alt.'>';
			}
		}
	}
}

function site() {
	return $_SERVER['HTTP_HOST'] ?? '';
}

function strexists($haystack, $needle) {
	return !(strpos($haystack, $needle) === FALSE);
}

function submitcheck($var, $allowget = 0, $seccodecheck = 0) {
	if(empty($GLOBALS[$var])) {
		return FALSE;
	} else {
		global $_SERVER, $adminid, $submitrefcheck, $seccode, $seccodeverify;
		if($allowget || (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && $GLOBALS['formhash'] == formhash() && (empty($_SERVER['HTTP_REFERER']) ||
			preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER'] ?? '') == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'] ?? '')))) {
			if($seccodecheck) {
				if(intval($seccodeverify) == intval($seccode)) {
					$seccode = random(4, 1);
					return TRUE;
				} else {
					showmessage('submit_seccode_invalid');
				}
			} else {
				return TRUE;
			}
		} else {
			showmessage('submit_invalid');
		}
	}
}

function template($file, $templateid = 0, $tpldir = '') {
	global $tplrefresh;

	$tpldir = $tpldir ? $tpldir : TPLDIR;
	$templateid = $templateid ? $templateid : TEMPLATEID;

	$tplfile = DISCUZ_ROOT.'./'.$tpldir.'/'.$file.'.htm';
	$objfile = DISCUZ_ROOT.'./forumdata/templates/'.$templateid.'_'.$file.'.tpl.php';
	if(TEMPLATEID != 1 && $templateid != 1 && !file_exists($tplfile)) {
		return template($file, 1, './templates/default/');
	}
	if($tplrefresh == 1 || ($tplrefresh > 1 && substr($GLOBALS['timestamp'], -1) > $tplrefresh)) {
		if(@filemtime($tplfile) > @filemtime($objfile)) {
			require_once DISCUZ_ROOT.'./include/template.func.php';
			parse_template($file, $templateid, $tpldir);
		}
	}
	return $objfile;
}

function transsid($url, $tag = '', $wml = 0) {
	global $sid;
	$tag = stripslashes($tag);
	if(!$tag || (!preg_match("/^(http:\/\/|mailto:|#|javascript)/i", $url) && !strpos($url, 'sid='))) {
		if($pos = strpos($url, '#')) {
			$urlret = substr($url, $pos);
			$url = substr($url, 0, $pos);
		} else {
			$urlret = '';
		}
		$url .= (strpos($url, '?') ? ($wml ? '&amp;' : '&') : '?').'sid='.$sid.$urlret;
	}
	return $tag.$url;
}

function typeselect($curtypeid = 0) {
	if($threadtypes = $GLOBALS['forum']['threadtypes']) {
		$html = '<select name="typeid"><option value="0">&nbsp;</option>';
		foreach($threadtypes['types'] as $typeid => $name) {
			$html .= '<option value="'.$typeid.'" '.($curtypeid == $typeid ? 'selected' : '').'>'.strip_tags($name).'</option>';
		}
		$html .= '</select>';
		return $html;
	} else {
		return '';
	}
}

function updatecredits($uids, $creditsarray, $coef = 1, $extrasql = '') {
	if($uids && ((!empty($creditsarray) && is_array($creditsarray)) || $extrasql)) {
		global $db, $tablepre;
		$creditsadd = $comma = '';
		foreach($creditsarray as $id => $addcredits) {
			$creditsadd .= $comma.'extcredits'.$id.'=extcredits'.$id.'+('.intval($addcredits).')*('.$coef.')';
			$comma = ', ';
		}

		if($creditsadd || $extrasql) {
			$db->query("UPDATE {$tablepre}members SET $creditsadd ".($creditsadd && $extrasql ? ', ' : '')." $extrasql WHERE uid IN ('$uids')", 'UNBUFFERED');
		}
	}
}

function updatesession() {
	if(!empty($GLOBALS['sessionupdated'])) {
		return TRUE;
	}

	global $db, $tablepre, $sessionexists, $sessionupdated, $sid, $onlineip, $discuz_uid, $discuz_user, $timestamp, $lastactivity, $seccode,
		$pvfrequence, $spageviews, $lastolupdate, $oltimespan, $onlinehold, $groupid, $styleid, $invisible, $discuz_action, $fid, $tid, $bloguid, $onlinehold;

	$fid = intval($fid);
	$tid = intval($tid);

	if($oltimespan && $discuz_uid && $lastactivity && $timestamp - ($lastolupdate ? $lastolupdate : $lastactivity) > $oltimespan * 60) {
		$lastolupdate = $timestamp;
		$db->query("UPDATE {$tablepre}onlinetime SET total=total+'$oltimespan', thismonth=thismonth+'$oltimespan', lastupdate='$timestamp' WHERE uid='$discuz_uid' AND lastupdate<='".($timestamp - $oltimespan * 60)."'");
		if(!$db->affected_rows()) {
			$db->query("INSERT INTO {$tablepre}onlinetime (uid, thismonth, total, lastupdate)
				VALUES ('$discuz_uid', '$oltimespan', '$oltimespan', '$timestamp')", 'SILENT');
		}
	} else {
		$lastolupdate = intval($lastolupdate);
	}

	if($sessionexists == 1) {
		if($pvfrequence && $discuz_uid) {
			if($spageviews >= $pvfrequence) {
				$pageviewsadd = ', pageviews=\'0\'';
				$db->query("UPDATE {$tablepre}members SET pageviews=pageviews+'$spageviews' WHERE uid='$discuz_uid'", 'UNBUFFERED');
			} else {
				$pageviewsadd = ', pageviews=pageviews+1';
			}
		} else {
			$pageviewsadd = '';
		}
		$db->query("UPDATE {$tablepre}sessions SET uid='$discuz_uid', username='$discuz_user', groupid='$groupid', styleid='$styleid', invisible='$invisible', action='$discuz_action', lastactivity='$timestamp', lastolupdate='$lastolupdate', seccode='$seccode', fid='$fid', tid='$tid', bloguid='$bloguid' $pageviewsadd WHERE sid='$sid'");
	} else {
		$ips = explode('.', $onlineip);

		$db->query("DELETE FROM {$tablepre}sessions WHERE sid='$sid' OR lastactivity<($timestamp-$onlinehold) OR ('$discuz_uid'<>'0' AND uid='$discuz_uid') OR (uid='0' AND ip1='$ips[0]' AND ip2='$ips[1]' AND ip3='$ips[2]' AND ip4='$ips[3]' AND lastactivity>$timestamp-60)");
		$db->query("INSERT INTO {$tablepre}sessions (sid, ip1, ip2, ip3, ip4, uid, username, groupid, styleid, invisible, action, lastactivity, lastolupdate, seccode, fid, tid, bloguid)
			VALUES ('$sid', '$ips[0]', '$ips[1]', '$ips[2]', '$ips[3]', '$discuz_uid', '$discuz_user', '$groupid', '$styleid', '$invisible', '$discuz_action', '$timestamp', '$lastolupdate', '$seccode', '$fid', '$tid', '$bloguid')", 'SILENT');
		if($discuz_uid && $timestamp - $lastactivity > 21600) {
			if($oltimespan && $timestamp - $lastactivity > 86400) {
				$query = $db->query("SELECT total FROM {$tablepre}onlinetime WHERE uid='$discuz_uid'");
				$oltimeadd = ', oltime='.round(intval($db->result($query, 0)) / 60);
			} else {
				$oltimeadd = '';
			}
			$db->query("UPDATE {$tablepre}members SET lastip='$onlineip', lastvisit=lastactivity, lastactivity='$timestamp' $oltimeadd WHERE uid='$discuz_uid'", 'UNBUFFERED');
		}
	}

	$sessionupdated = 1;
}

function updatemodworks($modaction, $posts = 1) {
	global $modworkstatus, $db, $tablepre, $discuz_uid, $timestamp, $_DCACHE;
	$today = gmdate('Y-m-d', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
	if($modworkstatus && $modaction && $posts) {
		$db->query("UPDATE {$tablepre}modworks SET count=count+1, posts=posts+'$posts' WHERE uid='$discuz_uid' AND modaction='$modaction' AND dateline='$today'");
		if(!$db->affected_rows()) {
			$db->query("INSERT INTO {$tablepre}modworks (uid, modaction, dateline, count, posts) VALUES ('$discuz_uid', '$modaction', '$today', 1, '$posts')");
		}
	}
}

?>