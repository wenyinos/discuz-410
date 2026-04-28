<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: post.php,v $
	$Revision: 1.7 $
	$Date: 2006/02/23 13:44:02 $
*/

define('CURSCRIPT', 'post');

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/discuzcode.func.php';
require_once DISCUZ_ROOT.'./include/post.func.php';

if(empty($action)) {
	showmessage('undefined_action', NULL, 'HALTED');
} elseif($action == 'smilies' && $smileyinsert) {
	foreach($_DCACHE['smilies_display'] as $key => $smiley) {
		$_DCACHE['smilies_display'][$key]['codeinsert'] = addcslashes($smiley['code'], '\\\'');
	}
	include template('post_smilies');
	dexit();
}

periodscheck('postbanperiods');

$allowpostattach = !empty($forum['allowpostattach']) || (!$forum['postattachperm'] && $allowpostattach) || ($forum['postattachperm'] && forumperm($forum['postattachperm']));
$attachextensions = $forum['attachextensions'] ? $forum['attachextensions'] : $attachextensions;
$allowanonymous = $forum['allowanonymous'] || $allowanonymous ? 1 : 0;

$postcredits = $forum['postcredits'] ? $forum['postcredits'] : $creditspolicy['post'];
$replycredits = $forum['replycredits'] ? $forum['replycredits'] : $creditspolicy['reply'];

$maxprice = isset($extcredits[$creditstrans]) ? $maxprice : 0;

if(!empty($tid) && !empty($fid)) {
	$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid='$tid' AND displayorder>='0'");
	$thread = $db->fetch_array($query);
	$fid = $thread['fid'];
	$navigation = "&raquo; <a href=\"viewthread.php?tid=$tid\">$thread[subject]</a>";
	$navtitle = " - $thread[subject]";

	if($thread['readperm'] && $thread['readperm'] > $readaccess && !$forum['ismoderator'] && $thread['authorid'] != $discuz_uid) {
		showmessage('thread_nopermission', NULL, 'NOPERM');
	}
}

$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid".($extra ? '&'.preg_replace("/^(&)*/", '', $extra) : '')."\">$forum[name]</a> $navigation";
$navtitle = ' - '.strip_tags($forum['name']).$navtitle;
if($forum['type'] == 'sub') {
	$query = $db->query("SELECT name, fid FROM {$tablepre}forums WHERE fid='$forum[fup]'");
	$fup = $db->fetch_array($query);
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> $navigation";
	$navtitle = ' - '.strip_tags($fup['name']).$navtitle;
}

if(empty($forum['allowview'])) {
	if(!$forum['viewperm'] && !$readaccess) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	} elseif($forum['viewperm'] && !forumperm($forum['viewperm'])) {
		showmessage('forum_nopermission', NULL, 'NOPERM');
	}
}

if(empty($bbcodeoff) && !$allowhidecode && preg_match("/\[hide=?\d*\].+?\[\/hide\]/is", preg_replace("/(\[code\](.+?)\[\/code\])/is", ' ', $message))) {
	showmessage('post_hide_nopermission');
}

if(!$adminid && $newbiespan && (!$lastpost || $timestamp - $lastpost < $newbiespan * 3600)) {
	$query = $db->query("SELECT regdate FROM {$tablepre}members WHERE uid='$discuz_uid'");
	if($timestamp - ($db->result($query, 0)) < $newbiespan * 3600) {
		showmessage('post_newbie_span');
	}
}

$extra = rawurlencode($extra);
$blogcheck = empty($isblog) && empty($addtoblog) ? '' : 'checked';
$notifycheck = empty($emailnotify) ? '' : 'checked';
$stickcheck = empty($sticktopic) ? '' : 'checked';
$digestcheck = empty($addtodigest) ? '' : 'checked';

if(periodscheck('postmodperiods', 0)) {
	$modnewthreads = $modnewreplies = 1;
} else {
	$censormod = censormod($subject."\t".$message);
	$modnewthreads = (!$allowdirectpost || $allowdirectpost == 1) && ($forum['modnewposts'] || $censormod) ? 1 : 0;
	$modnewreplies = (!$allowdirectpost || $allowdirectpost == 2) && ($forum['modnewposts'] == 2 || $censormod) ? 1 : 0;
}

$subject = isset($subject) ? dhtmlspecialchars(censor(trim($subject))) : '';
$message = isset($message) ? censor(trim($message)) : '';
$readperm = isset($readperm) ? intval($readperm) : 0;
$price = isset($price) ? intval($price) : 0;

$urloffcheck = $usesigcheck = $smileyoffcheck = $codeoffcheck = $htmloncheck = $emailcheck = '';

$enctype = $allowpostattach ? 'enctype="multipart/form-data"' : '';
$maxattachsize_kb = $maxattachsize / 1000;

//get secure code checking status (pos. -3)
$seccodecheck = substr(sprintf('%05b', $seccodestatus), -3, 1);

//get trade thread status (pos. -1)
$allowposttrade = substr(sprintf('%02b', $forum['allowtrade']), -1, 1);

if(!empty($previewpost) || (empty($previewpost) && empty($topicsubmit) && empty($replysubmit) && empty($editsubmit))) {

	$typeid = preg_replace("/.*typeid%3D(\d+).*/", "\\1", $extra);

	$smilies = '';
	$moresmilies = 0;
	if($smileyinsert && is_array($_DCACHE['smilies_display'])) {
		$smileyinsert = 1;
		$smcols = $smcols ? $smcols : 3;
		$smilies .= '<tr align="center">';
		foreach($_DCACHE['smilies_display'] as $key => $smiley) {
			if($key >= $smcols * 6) {
				$moresmilies = 1;
				break;
			}
			$smilies .= '<td valign="top"><img src="'.SMDIR.'/'.$smiley['url'].'" border="0" onmouseover="this.style.cursor=\'hand\';" onclick="AddText(\''.addcslashes($smiley['code'], '\\\'').'\');"></td>'."\n";
			$smilies .= !(++$key % $smcols) ? '</tr><tr align="center">' : '';
		}
	} else {
		$smileyinsert = 0;
	}

	if($discuz_uid && $sigstatus && !$usesigcheck) {
		$usesigcheck = 'checked';
	}

	if(!empty($trade)) {
		if(isset($seller)) {
			$seller = dhtmlspecialchars(stripslashes($seller));
		} else {
			$query = $db->query("SELECT alipay FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
			$alipay = $db->result($query, 0);
			$seller = $alipay ? $alipay : $email;
		}

		$item_price = isset($item_price) ? (float)$item_price : '';
		$item_name = isset($item_name) ? dhtmlspecialchars(stripslashes($item_name)) : '';
		$item_quality = isset($item_quality) ? dhtmlspecialchars(stripslashes($item_quality)) : '';
		$item_locus = isset($item_locus) ? dhtmlspecialchars(stripslashes($item_locus)) : '';
		$postage_mail = isset($postage_mail) ? dhtmlspecialchars(stripslashes($postage_mail)) : '';
		$postage_express = isset($postage_express) ? dhtmlspecialchars(stripslashes($postage_express)) : '';

		$checktp = array((isset($transport) ? $transport : 'seller') => 'checked');
		$postagedisabled = isset($transport) && $transport == 'buyer' ? '' : 'disabled';
	}

	if(empty($previewpost)) {

		$subject = $message = $polloptions = '';

	} else {

		$currtime = gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600);
		$subject = stripslashes($subject);
		$message = stripslashes($message);
		$polloptions = dhtmlspecialchars(stripslashes($polloptions));
		$subject_preview = $subject;
		$message_preview = discuzcode($message, !empty($smileyoff), !empty($bbcodeoff), !empty($htmlon), $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml']);
		$message = dhtmlspecialchars($message);

		$urloffcheck = !empty($parseurloff) ? 'checked' : '';
		$usesigcheck = !empty($usesig) ? 'checked' : '';
		$smileyoffcheck = !empty($smileyoff) ? 'checked' : '';
		$codeoffcheck = !empty($bbcodeoff) ? 'checked' : '';
		$htmloncheck = !empty($htmlon) ? 'checked' : '';
		$emailcheck = !empty($emailnotify) ? 'checked' : '';

		$topicsubmit = $replysubmit = $editsubmit = '';

	}

} else {

	if(empty($parseurloff)) {
		$message = parseurl($message);
	}

	if((!empty($topicsubmit) || !empty($replysubmit)) && $seccodecheck) {

		if(!isset($seccodeverify)) {
			$seccode = random(4, 1);

			$request = array
				(
				'method' => $_SERVER['REQUEST_METHOD'],
				'action' => $PHP_SELF,
				'elements' => ''
				);

			$quesand = '?';
			foreach($_GET as $key => $value) {
				$request['action'] .= $quesand.rawurlencode($key).'='.rawurlencode($value);
				$quesand = '&';
			}
			foreach($_POST as $key => $value) {
				if(is_array($value)) {
					foreach($value as $arraykey => $arrayvalue) {
						$request['elements'] .= '<input type="hidden" name="'.dhtmlspecialchars($key.'['.$arraykey.']').'" value="'.dhtmlspecialchars(stripslashes($arrayvalue)).'">';
					}
				} else {
					$request['elements'] .= '<input type="hidden" name="'.dhtmlspecialchars($key).'" value="'.dhtmlspecialchars(stripslashes($value)).'">';
				}
			}

			include template('post_seccode');
			dexit();
		}

	}

}

if($forum['password'] && $forum['password'] != $_DCOOKIE['fidpw'.$fid]) {
	header("Location: {$boardurl}forumdisplay.php?fid=$fid&sid=$sid");
	exit();
}

if($action == 'newthread') {
	require_once DISCUZ_ROOT.'./include/newthread.inc.php';
} elseif($action == 'reply') {
	require_once DISCUZ_ROOT.'./include/newreply.inc.php';
} elseif($action == 'edit') {
	require_once DISCUZ_ROOT.'./include/editpost.inc.php';
}

?>