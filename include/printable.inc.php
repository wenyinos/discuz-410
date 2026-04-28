<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: printable.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$postlist = array();
$attachment = 0;

$thisbg = '#FFFFFF'; //use for discuzcode().

$query = $db->query("SELECT * FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline");
while($post = $db->fetch_array($query)) {

	$post['dateline'] = gmdate("$dateformat $timeformat", $post['dateline'] + ($timeoffset * 3600));
	//$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml']);
	$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], ($forum['jammer'] && $post['authorid'] != $discuz_uid ? 1 : 0));

	if($post['attachment']) {
		$attachment = 1;
	}

	$postlist[$post['pid']] = $post;
}

if($attachment) {

	require_once DISCUZ_ROOT.'./include/attachment.func.php';

	$query = $db->query("SELECT * FROM {$tablepre}attachments WHERE tid='$tid'");
	while($attach = $db->fetch_array($query)) {

		$extension = strtolower(fileext($attach['filename']));
		$attach['dateline'] = gmdate("$dateformat $timeformat", $attach['dateline'] + $timeoffset * 3600);
		$attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
		$attach['attachsize'] = sizecount($attach['filesize']);
		$attach['attachimg'] = $attachimgpost && in_array($extension, array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp')) ? 1 : 0;

		$postlist[$attach['pid']]['attachments'][] = $attach;

	}

}

include template('viewthread_printable');

?>