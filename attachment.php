<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: attachment.php,v $
	$Revision: 1.3 $
	$Date: 2006/03/05 12:34:33 $
*/

require_once './include/common.inc.php';

$discuz_action = 14;

if($attachrefcheck && $_SERVER['HTTP_REFERER'] && preg_replace("/https?:\/\/([^\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) != $_SERVER['HTTP_HOST']) {
	//header("Location: {$boardurl}images/common/invalidreferer.gif");
	showmessage('attachment_referer_invalid', NULL, 'HALTED');
}

/*
$query = $db->query("SELECT a.*, t.fid, p.authorid FROM {$tablepre}attachments a, {$tablepre}threads t, {$tablepre}posts p
	WHERE a.aid='$aid' AND t.tid=a.tid AND p.pid=a.pid AND t.displayorder>='0' AND p.invisible='0'");
$attach = $db->fetch_array($query);
*/

$attachexists = FALSE;
if(!empty($aid)) {
	$query = $db->query("SELECT * FROM {$tablepre}attachments WHERE aid='$aid'");
	if($attach = $db->fetch_array($query)) {
		$query = $db->query("SELECT fid FROM {$tablepre}threads WHERE tid='$attach[tid]' AND displayorder>='0'");
		if($attach['fid'] = $db->result($query, 0)) {
			$query = $db->query("SELECT authorid FROM {$tablepre}posts WHERE pid='$attach[pid]' AND invisible='0'");
			if($db->num_rows($query)) {
				$attach['authorid'] = $db->result($query, 0);
				$attachexists = TRUE;
			}
		}
	}
}


if($allowgetattach && $attach['readperm'] && $attach['readperm'] > $readaccess && $adminid <= 0 && !($discuz_uid && $discuz_uid == $attach['authorid'])) {
	showmessage('attachment_nopermission', NULL, 'NOPERM');
}

$filename = $attachdir.'/'.$attach['attachment'];

if(is_readable($filename) && $attachexists) {

	$query = $db->query("SELECT f.getattachperm, a.allowgetattach FROM {$tablepre}forumfields f
			LEFT JOIN {$tablepre}access a ON a.uid='$discuz_uid' AND a.fid=f.fid
			WHERE f.fid='$attach[fid]'");
	$forum = $db->fetch_array($query);

	if(!$forum['allowgetattach']) {
		if(!$forum['getattachperm'] && !$allowgetattach) {
			showmessage('group_nopermission', NULL, 'NOPERM');
		} elseif($forum['getattachperm'] && !forumperm($forum['getattachperm'])) {
			showmessage('attachment_forum_nopermission', NULL, 'NOPERM');
		}
	}

	
	if(!($isimage = preg_match("/^image\/.+/", $attach['filetype']))) {
		checklowerlimit($creditspolicy['getattach'], -1);
	}

	if(empty($noupdate)) {
		if($delayviewcount == 2 || $delayviewcount == 3) {
			$logfile = './forumdata/cache/cache_attachviews.log';
			if(substr($timestamp, -1) == '0') {
				require_once DISCUZ_ROOT.'./include/misc.func.php';
				updateviews('attachments', 'aid', 'downloads', $logfile);
			}

			if(@$fp = fopen(DISCUZ_ROOT.$logfile, 'a')) {
				fwrite($fp, "$aid\n");
				fclose($fp);
			} elseif($adminid == 1) {
				showmessage('view_log_invalid');
			}
		} else {
			$db->query("UPDATE {$tablepre}attachments SET downloads=downloads+'1' WHERE aid='$aid'", 'UNBUFFERED');
		}
	}

	$filesize = filesize($filename);

	ob_end_clean();
	header('Cache-control: max-age=31536000');
	header('Expires: '.gmdate('D, d M Y H:i:s', $timestamp + 31536000).' GMT');
	header('Content-Encoding: none');
	//header('Content-Length: '.$filesize);

	//forbid flash be opened directly
	//header('Content-Disposition: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'inline; ' : 'attachment; ').'filename='.$attach['filename']);

	header('Content-Disposition: attachment; filename='.$attach['filename']);
	header('Content-Type: '.$attach['filetype']);

	@$fp = fopen($filename, 'rb');
	@flock($fp, 2);
	$attachment = @fread($fp, $filesize);
	@fclose($fp);

	echo $attachment;

	if(!$isimage) {	
		updatecredits($discuz_uid, $creditspolicy['getattach'], -1);
	}

} else {

	showmessage('attachment_nonexistence');

}

?>