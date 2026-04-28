<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: global.func.php,v $
	$Revision: 1.6 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

function wapheader($title) {
	global $action, $_SERVER;
	header("Content-type: text/vnd.wap.wml; charset=utf-8");
	/*
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	*/
	echo "<?xml version=\"1.0\"?>\n".
		"<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\">\n".
		"<wml>\n".
		"<head>\n".
		"<meta http-equiv=\"cache-control\" content=\"max-age=180,private\" />\n".
		"</head>\n".
		"<card id=\"discuz_wml\" title=\"$title\">\n";
		// newcontext=\"true\"
}

function wapfooter() {
	global $discuz_uid, $discuz_user, $lang;
	echo "<p align=\"center\"><br /><a href=\"index.php\">$lang[home]</a><br />\n".
		($discuz_uid ? "<a href=\"index.php?action=login&amp;logout=yes\">$discuz_user:$lang[logout]</a>" : "<a href=\"index.php?action=login\">$lang[login]</a>")."<br /><br />\n".
		"<small>Powered by Discuz!</small></p>\n".
		//"<do type=\"prev\" label=\"$lang[return]\"><exit /></do>\n".
		"</card>\n".
		"</wml>";

	updatesession();
	wmloutput();
}

function wapmsg($message, $forward = array()) {
	extract($GLOBALS, EXTR_SKIP);
	if(isset($lang[$message])) {
		eval("\$message = \"".$lang[$message]."\";");
	}
	echo "<p align=\"center\">$message".
		($forward ? "<br /><a href=\"$forward[link]\">".(isset($lang[$forward['title']]) ? $lang[$forward['title']] : $forward['title'])."</a>" : '').
		"</p>\n";

	wapfooter();
	exit();
}

function wapcutstr($string, &$length) {
	$strcut = '';
	if(strlen($string) > $length) {
		for($i = 0; $i < $length - 3; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
		$length = $i;
		return $strcut.' ..';
	} else {
		return $string;
	}
}

function wmloutput() {
	global $sid, $charset, $wapcharset, $chs;
	$content = preg_replace("/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/ies", "transsid('\\3','<a\\1href=\\2',1)", ob_get_contents());
	ob_end_clean();

	if($charset != 'utf-8') {

		$target = $wapcharset == 1 ? 'UTF-8' : 'UNICODE';

		if (empty($chs)) {
			$chs = new Chinese($charset, $target);
		} else {
			$chs->config['SourceLang'] = $chs->_lang($charset);
			$chs->config['TargetLang'] = $target;
		}

		echo ($wapcharset == 1 ? $chs->Convert($content) : str_replace('&#x;', '??', $chs->Convert($content)));

	} else {
		echo $content;
	}
}
?>