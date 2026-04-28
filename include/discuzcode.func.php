<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: discuzcode.func.php,v $
	$Revision: 1.10 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$discuzcodes = array
	(
	'pcodecount' => -1,
	'codecount' => 0,
	'codehtml' => '',
	'searcharray' => array(),
	'replacearray' => array(),
	'seoarray' => array
		(
		0 => '',
		1 => $_SERVER['HTTP_HOST'],
		2 => $bbname,
		3 => $seotitle,
		4 => $seokeywords,
		5 => $seodescription
		)
	);

if(!is_array($_DCACHE['bbcodes']) || !is_array($_DCACHE['smilies'])) {
	@include DISCUZ_ROOT.'./forumdata/cache/cache_bbcodes.php';
}

foreach($_DCACHE['smilies']['replacearray'] as $key => $smiley) {
	$_DCACHE['smilies']['replacearray'][$key] = '<img src="'.SMDIR.'/'.$smiley.'" align="absmiddle" border="0">';
}

mt_srand((double)microtime() * 1000000);

function attachtag($pid, $aid) {
	global $language, $postlist, $attachrefcheck, $attachurl;
	if(isset($postlist[$pid]['attachments'][$aid])) {
		include_once language('misc');
		$attach = $postlist[$pid]['attachments'][$aid];
		unset($postlist[$pid]['attachments'][$aid]);

		$replacement = "<br><br>$attach[attachicon] ";
		if($attach['attachimg']) {
			$replacement .= "<a href=\"member.php?action=credits&view=getattach\" title=\"$language[attach_credits_policy]\" target=\"_blank\">$language[attach_img]</a>: ".
				($attach['readperm'] ? ", $language[attach_readperm] $attach[readperm]" : '').
				($attach['description'] ? "[{$attach[description]}]" : '').
				" <a href=\"attachment.php?aid=$attach[aid]\" target=\"_blank\" class=\"bold\">$attach[filename]</a> ($attach[dateline], $attach[attachsize])<br><br>".
				($attachrefcheck ? "<img src=\"attachment.php?aid=$attach[aid]&noupdate=yes\" border=\"0\" onload=\"if(this.width>screen.width*0.7) {this.resized=true; this.width=screen.width*0.7; this.alt='$language[attach_img_zoom]';}\" onmouseover=\"if(this.resized) this.style.cursor='hand';\" onclick=\"if(!this.resized) {return false;} else {window.open('attachment.php?aid=$attach[aid]');}\" onmousewheel=\"return imgzoom(this);\">" : "<img src=\"$attachurl/$attach[attachment]\" border=\"0\" onload=\"if(this.width>screen.width*0.7) {this.resized=true; this.width=screen.width*0.7; this.alt='$language[attach_img_zoom]';}\" onmouseover=\"if(this.resized) this.style.cursor='hand';\" onclick=\"if(!this.resized) {return false;} else {window.open('".addslashes("$attachurl/$attach[attachment]")."');}\" onmousewheel=\"return imgzoom(this);\">");
		} else {
			$replacement .= "<a href=\"member.php?action=credits&view=getattach\" title=\"$language[attach_credits_policy]\" target=\"_blank\">$language[attach]</a>: ".
					($attach['description'] ? "[{$attach[description]}]" : '').
					" <a href=\"attachment.php?aid=$attach[aid]\" target=\"_blank\" class=\"bold\">$attach[filename]</a> ($attach[dateline], $attach[attachsize])<br><span class=\"smalltxt\">$language[attach_download_count] $attach[downloads]".
					($attach['readperm'] ? ", $language[attach_readperm] $attach[readperm]" : '').
					"</span><br>";
		}

		return $replacement;
	} else {
		return '<strike>[attach]'.$aid.'[/attach]</strike>';
	}
}

function censor($message) {
	global $_DCACHE;
	require_once(DISCUZ_ROOT.'/forumdata/cache/cache_censor.php');

	if($_DCACHE['censor']['banned'] && preg_match($_DCACHE['censor']['banned'], $message)) {
		showmessage('word_banned');
	} else {
		return empty($_DCACHE['censor']['filter']) ? $message :
			@preg_replace($_DCACHE['censor']['filter']['find'], $_DCACHE['censor']['filter']['replace'], $message);
	}
}

function censormod($message) {
	global $_DCACHE;
	require_once(DISCUZ_ROOT.'/forumdata/cache/cache_censor.php');
	return $_DCACHE['censor']['mod'] && preg_match($_DCACHE['censor']['mod'], $message);
}

function creditshide($creditsrequire, $message) {
	global $language;
	include_once language('misc');

	if($GLOBALS['credits'] < $creditsrequire && !$GLOBALS['forum']['ismoderator']) {
		return '<b>'.eval("return \"$language[post_hide_credits_hidden]\";").'</b>';
	} else {
		return '<b>'.eval("return \"$language[post_hide_credits]\";").'</b><br>'.
			'==============================<br><br>'.
			str_replace('\\"', '"', $message).'<br><br>'.
			'==============================';
	}
}

function codedisp($code) {
	global $discuzcodes;
	$discuzcodes['pcodecount']++;
	$code = htmlspecialchars(str_replace('\\"', '"', preg_replace("/^[\n\r]*(.+?)[\n\r]*$/is", "\\1", $code)));
	$discuzcodes['codehtml'][$discuzcodes['pcodecount']] = "<br><br><div class=\"smalltxt\" style=\"margin-left: 2em; margin-right: 2em; font-weight: bold\"><div style=\"float: left\">CODE:</div><div style=\"text-align: right; float: right\"><a href=\"###\" class=\"smalltxt\" onclick=\"copycode(findobj('code$discuzcodes[codecount]'));\">[Copy to clipboard]</a></div></div><div class=\"altbg2\" style=\"margin: 2em; margin-top: 3px; clear: both; padding: 10px; padding-top: 5px; border: ".INNERBORDERWIDTH."px solid ".BORDERCOLOR."; word-break: break-all\" id=\"code$discuzcodes[codecount]\">$code</div>";
	$discuzcodes['codecount']++;
	return "[\tDISCUZ_CODE_$discuzcodes[pcodecount]\t]";
}

function karmaimg($rate, $ratetimes) {
	$karmaimg = '';
	if($rate && $ratetimes) {
		$image = $rate > 0 ? 'agree.gif' : 'disagree.gif';
		for($i = 0; $i < ceil(abs($rate) / $ratetimes); $i++) {
			$karmaimg .= '<img src="'.IMGDIR.'/'.$image.'" border="0" align="right">';
		}
	}
	return $karmaimg;
}

function parseurl($message) {
	return preg_match("/\[code\].+?\[\/code\]/is", $message) ? $message :
		substr(preg_replace(	array(
						"/(?<=[^\]a-z0-9-=\"'\\/])(http:\/\/[a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+\.(jpg|gif|png|bmp))/i",
						"/(?<=[^\]\)a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|rtsp|mms|callto):\/\/|www\.)([a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/i",
						"/(?<=[^\]\)a-z0-9\/\-_.~?=:.])([_a-z0-9-+]+(\.[_a-z0-9-+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4}))/i"
					), array(
						"[img]\\1[/img]",
						"[url]\\1\\3[/url]",
						"[email]\\0[/email]"
					), ' '.$message), 1);
}

function discuzcode($message, $smileyoff, $bbcodeoff, $htmlon = 0, $allowsmilies = 1, $allowbbcode = 1, $allowimgcode = 1, $allowhtml = 0, $jammer = 0) {
	global $discuzcodes, $credits, $tid, $discuz_uid, $highlight, $maxsmilies, $db, $tablepre;

	if(!$bbcodeoff && $allowbbcode) {
		$message = preg_replace("/\s*\[code\](.+?)\[\/code\]\s*/ies", "codedisp('\\1')", $message);
	}

	if(!$htmlon && !$allowhtml) {
		$message = $jammer ? preg_replace("/\r\n|\n|\r/e", "jammer()", dhtmlspecialchars($message)) : dhtmlspecialchars($message);
	}

	if(!$smileyoff && $allowsmilies && !empty($GLOBALS['_DCACHE']['smilies']) && is_array($GLOBALS['_DCACHE']['smilies'])) {
		$message = preg_replace($GLOBALS['_DCACHE']['smilies']['searcharray'], $GLOBALS['_DCACHE']['smilies']['replacearray'], $message, $maxsmilies);
	}

	if(!$bbcodeoff && $allowbbcode) {

		if(empty($discuzcodes['searcharray'])) {
			$discuzcodes['searcharray']['bbcode_regexp'] = array(
				"/\s*\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is",
				"/\s*\[free\][\n\r]*(.+?)[\n\r]*\[\/free\]\s*/is",
				"/\[url\]\s*(www.|https?:\/\/|ftp:\/\/|gopher:\/\/|news:\/\/|telnet:\/\/|rtsp:\/\/|mms:\/\/|callto:\/\/|ed2k:\/\/){1}([^\[\"']+?)\s*\[\/url\]/ie",
				"/\[url=www.([^\[\"']+?)\](.+?)\[\/url\]/is",
				"/\[url=(https?|ftp|gopher|news|telnet|rtsp|mms|callto|ed2k){1}:\/\/([^\[\"']+?)\](.+?)\[\/url\]/is",
				"/\[email\]\s*([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+)\s*\[\/email\]/i",
				"/\[email=([a-z0-9\-_.+]+)@([a-z0-9\-_]+[.][a-z0-9\-_.]+)\](.+?)\[\/email\]/is",
				"/\[color=([^\[\<]+?)\]/i",
				"/\[size=([^\[\<]+?)\]/i",
				"/\[font=([^\[\<]+?)\]/i",
				"/\[align=([^\[\<]+?)\]/i"
			);
			$discuzcodes['replacearray']['bbcode_regexp'] = array(
				"<br><br><div class=\"smalltxt\" style=\"margin-left: 2em; font-weight: bold\">QUOTE:</div><div class=\"altbg2\" style=\"margin: 2em; margin-top: 3px; padding: 10px; border: ".INNERBORDERWIDTH."px solid ".BORDERCOLOR."; word-break: break-all\">\\1</div>",
				"<br><br><div class=\"smalltxt\" style=\"margin-left: 2em; font-weight: bold\">FREE:</div><div class=\"altbg2\" style=\"margin: 2em; margin-top: 3px; padding: 10px; border: ".INNERBORDERWIDTH."px solid ".BORDERCOLOR."; word-break: break-all\">\\1</div>",
				"cuturl('\\1\\2')",
				"<a href=\"http://www.\\1\" target=\"_blank\">\\2</a>",
				"<a href=\"\\1://\\2\" target=\"_blank\">\\3</a>",
				"<a href=\"mailto:\\1@\\2\">\\1@\\2</a>",
				"<a href=\"mailto:\\1@\\2\">\\3</a>",
				"<font color=\"\\1\">",
				"<font size=\"\\1\">",
				"<font face=\"\\1\">",
				"<p align=\"\\1\">"
			);

			$discuzcodes['searcharray']['bbcode_regexp'] = array_merge($discuzcodes['searcharray']['bbcode_regexp'], $discuzcodes['searcharray']['bbcode_regexp']);
			$discuzcodes['replacearray']['bbcode_regexp'] = array_merge($discuzcodes['replacearray']['bbcode_regexp'], $discuzcodes['replacearray']['bbcode_regexp']);

			$discuzcodes['searcharray']['bbcode_regexp'][] = "/\[payto\]\s*\(seller\)(.*)\(\/seller\)\s*(\(subject\)(.*)\(\/subject\))?\s*(\(body\)(.*)\(\/body\))?\s*(\(gross\)(.*)\(\/gross\))?\s*(\(price\)(.*)\(\/price\))?\s*(\(url\)(.*)\(\/url\))?\s*(\(type\)(.*)\(\/type\))?\s*(\(transport\)(.*)\(\/transport\))?\s*(\(ordinary_fee\)(.*)\(\/ordinary_fee\))?\s*(\(express_fee\)(.*)\(\/express_fee\))?\s*\[\/payto\]/iesU";
			$discuzcodes['replacearray']['bbcode_regexp'][] = "payto('\\1',array('subject'=>'\\3','body'=>'\\5','price'=>'\\7','price'=>'\\9','url'=>'\\11','type'=>'\\13','transport'=>'\\15','ordinary_fee'=>'\\17','express_fee'=>'\\19'))";

			$discuzcodes['searcharray']['bbcode_str'] = array(
				'[/color]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]',
				'[i]', '[/i]', '[u]', '[/u]', '[list]', '[list=1]', '[list=a]',
				'[list=A]', '[*]', '[/list]'
			);

			$discuzcodes['replacearray']['bbcode_str'] = array(
				'</font>', '</font>', '</font>', '</p>', '<b>', '</b>', '<i>',
				'</i>', '<u>', '</u>', '<ul>', '<ol type=1>', '<ol type=a>',
				'<ol type=A>', '<li>', '</ul></ol>'
			);
		}                

		@$message = str_replace($discuzcodes['searcharray']['bbcode_str'], $discuzcodes['replacearray']['bbcode_str'],
				preg_replace(
					($allowbbcode == 2 && $GLOBALS['_DCACHE']['bbcodes'] ? array_merge($discuzcodes['searcharray']['bbcode_regexp'], $GLOBALS['_DCACHE']['bbcodes']['searcharray']) : $discuzcodes['searcharray']['bbcode_regexp']),
					($allowbbcode == 2 && $GLOBALS['_DCACHE']['bbcodes'] ? array_merge($discuzcodes['replacearray']['bbcode_regexp'], $GLOBALS['_DCACHE']['bbcodes']['replacearray']) : $discuzcodes['replacearray']['bbcode_regexp']),
					$message));

		if(preg_match("/\[hide=?\d*\].+?\[\/hide\]/is", $message)) {
			if(stristr($message, '[hide]')) {
				global $language;
				include_once language('misc');

				$query = $db->query("SELECT pid FROM {$tablepre}posts WHERE tid='$tid' AND authorid='$discuz_uid' LIMIT 1");
				if($GLOBALS['forum']['ismoderator'] || $db->result($query, 0)) {
					$message = preg_replace("/\[hide\]\s*(.+?)\s*\[\/hide\]/is",
						'<span class="bold">'.$language['post_hide_reply'].'</span><br>'.
						'==============================<br><br>'.
						'\\1<br><br>'.
						'==============================',
						$message);
				} else {
					$message = preg_replace("/\[hide\](.+?)\[\/hide\]/is", '<b>'.$language['post_hide_reply_hidden'].'</b>', $message);
				}
			}
			$message = preg_replace("/\[hide=(\d+)\]\s*(.+?)\s*\[\/hide\]/ies", "creditshide(\\1,'\\2')", $message);
		}
	}

	if(!$bbcodeoff && $allowimgcode) {
		if(empty($discuzcodes['searcharray']['imgcode'])) {
			$discuzcodes['searcharray']['imgcode'] = array(
				"/\[swf\]\s*([^\[\<\r\n]+?)\s*\[\/swf\]/ies",
				"/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies",
				"/\[img=(\d{1,3})[x|\,](\d{1,3})\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/ies"
			);
			$discuzcodes['replacearray']['imgcode'] = array(
				"bbcodeurl('\\1', ' <img src=\"images/attachicons/flash.gif\" align=\"absmiddle\"> <a href=\"%s\" target=\"_blank\">Flash: %s</a> ')",
				"bbcodeurl('\\1', '<img src=\"%s\" border=\"0\" onload=\"if(this.width>screen.width*0.7) {this.resized=true; this.width=screen.width*0.7; this.alt=\'Click here to open new window\\nCTRL+Mouse wheel to zoom in/out\';}\" onmouseover=\"if(this.width>screen.width*0.7) {this.resized=true; this.width=screen.width*0.7; this.style.cursor=\'hand\'; this.alt=\'Click here to open new window\\nCTRL+Mouse wheel to zoom in/out\';}\" onclick=\"if(!this.resized) {return true;} else {window.open(\'%s\');}\" onmousewheel=\"return imgzoom(this);\">')",
				"bbcodeurl('\\3', '<img width=\"\\1\" height=\"\\2\" src=\"%s\" border=\"0\">')"
			);
		}
		$message = preg_replace($discuzcodes['searcharray']['imgcode'], $discuzcodes['replacearray']['imgcode'], $message);
	}

	for($i = 0; $i <= $discuzcodes['pcodecount']; $i++) {
		$message = str_replace("[\tDISCUZ_CODE_$i\t]", $discuzcodes['codehtml'][$i], $message);
	}

	if($highlight) {
		foreach(explode('+', $highlight) as $ret) {
			if($ret) {
				$message = preg_replace("/(?<=[\s\"\]>()]|[\x7f-\xff]|^)(".preg_quote($ret, '/').")(([.,:;-?!()\s\"<\[]|[\x7f-\xff]|$))/siU", "<u><b><font color=\"#FF0000\">\\1</font></b></u>\\2", $message);
			}
		}
	}

	return $htmlon || $allowhtml ? $message : nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'), $message));
}

function cuturl($url) {
	$length = 65;
	$urllink = "<a href=\"".(substr(strtolower($url), 0, 4) == 'www.' ? "http://$url" : $url).'" target="_blank">';
	if(strlen($url) > $length) {
		$url = substr($url, 0, intval($length * 0.5)).' ... '.substr($url, - intval($length * 0.3));
	}
	$urllink .= $url.'</a>';
	return $urllink;
}

function bbcodeurl($url, $tags) {
	if(!preg_match("/<.+?>/s", $url)) {
		if(!in_array(strtolower(substr($url, 0, 6)), array('http:/', 'ftp://', 'rtsp:/', 'mms://'))) {
			$url = 'http://'.$url;
		}
		return str_replace(array('submit', 'logging.php'), array('', ''), sprintf($tags, $url, addslashes($url)));
	} else {
		return '&nbsp;'.$url;
	}
}

function jammer() {
	$randomstr = '';
	for($i = 0; $i < mt_rand(5, 15); $i++) {
		$randomstr .= chr(mt_rand(0, 59)).chr(mt_rand(63, 126));
	}
	if($GLOBALS['thisbg'] == 'altbg1') {
		$thisbg = ALTBG1;
	} elseif($GLOBALS['thisbg'] == 'altbg2') {
		$thisbg = ALTBG2;
	} else {
		$thisbg = $GLOBALS['thisbg'];
	}
	return mt_rand(0, 1) ? '<font style="font-size:0px;color:'.$thisbg.'">'.$GLOBALS['discuzcodes']['seoarray'][mt_rand(0, 5)].$randomstr.'</font>'."\r\n" :
		"\r\n".'<span style="display:none">'.$randomstr.$GLOBALS['discuzcodes']['seoarray'][mt_rand(0, 5)].'</span>';
}

?>