<?

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: editpost.inc.php,v $
	$Revision: 1.13 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$discuz_action = 13;

$query = $db->query("SELECT m.adminid, p.first, p.authorid, p.author, p.dateline, u.allowhtml, p.anonymous FROM {$tablepre}posts p
	LEFT JOIN {$tablepre}members m ON m.uid=p.authorid
	LEFT JOIN {$tablepre}usergroups u ON u.groupid=m.groupid
	WHERE pid='$pid' AND tid='$tid' AND fid='$fid'");

$orig = $db->fetch_array($query);

$isfirstpost = $orig['first'] ? 1 : 0;
$isorigauthor = $discuz_uid && $discuz_uid == $orig['authorid'];
$isanonymous = $isanonymous && $allowanonymous ? 1 : 0;
$alloweditpost = $alloweditpost && !(in_array($orig['adminid'], array(1, 2, 3)) && $adminid > $orig['adminid']) ? 1 : 0;

if((!$forum['ismoderator'] || !$alloweditpost) && !$isorigauthor) {
	showmessage('post_edit_nopermission', NULL, 'HALTED');
} elseif($isorigauthor && !$forum['ismoderator']) {
	if($edittimelimit && $timestamp - $orig['dateline'] > $edittimelimit * 60) {
		showmessage('post_edit_timelimit', NULL, 'HALTED');
	} elseif(($isfirstpost && $modnewthreads) || (!$isfirstpost && $modnewreplies)) {
		showmessage('post_edit_moderate');
	}
}

$thread['pricedisplay'] = $thread['price'] == -1 ? 0 : $thread['price'];

if(!submitcheck('editsubmit')) {

	include_once language('misc');

	$typeselect = typeselect($thread['typeid']);

	$icons = '';
	if(is_array($_DCACHE['icons']) && $isfirstpost) {
		$key = 1;
		foreach($_DCACHE['icons'] as $id => $icon) {
			$icons .= ' <input type="radio" name="iconid" value="'.$id.'" '.($thread['iconid'] == $id ? 'checked' : '').'><img src="'.SMDIR.'/'.$icon.'">';
			$icons .= !(++$key % 10) ? '<br>' : '';
		}
	}

	$query = $db->query("SELECT * FROM {$tablepre}posts WHERE pid='$pid' AND tid='$tid' AND fid='$fid'");
	$postinfo = $db->fetch_array($query);

	$usesigcheck = $postinfo['usesig'] ? 'checked' : '';
	$urloffcheck = $postinfo['parseurloff'] ? 'checked' : '';
	$smileyoffcheck = $postinfo['smileyoff'] == 1 ? 'checked' : '';
	$codeoffcheck = $postinfo['bbcodeoff'] == 1 ? 'checked' : '';
	$htmloncheck = $postinfo['htmlon'] ? 'checked' : '';

	$polloptions = '';
	if($isfirstpost) {
		$thread['freecharge'] = $maxchargespan && $timestamp - $thread['dateline'] >= $maxchargespan * 3600 ? 1 : 0;
		if($thread['poll'] && ($alloweditpoll || $thread['authorid'] == $discuz_uid)) {
			$query = $db->query("SELECT pollopts FROM {$tablepre}polls WHERE tid='$tid'");
			$polloptions = unserialize($db->result($query, 0));
			for($i = 0; $i < count($polloptions['options']); $i++) {
				$polloptions['options'][$i][0] = htmlspecialchars(stripslashes($polloptions['options'][$i][0]))."\n";
			}
		}
	}

	if($postinfo['attachment']) {
		require_once DISCUZ_ROOT.'./include/attachment.func.php';

		$attachments = array();
		$query = $db->query("SELECT * FROM {$tablepre}attachments WHERE pid='$postinfo[pid]'");
		while($attach = $db->fetch_array($query)) {
			$attach['dateline'] = gmdate("$dateformat $timeformat", $attach['dateline'] + $timeoffset * 3600);
			$attach['filesize'] = sizecount($attach[filesize]);
			$attach['filetype'] = attachtype(fileext($attach['attachment'])."\t".$attach['filetype']);
			$attachments[] = $attach;
		}
	}

	$postinfo['subject'] = str_replace('"', "&quot;", $postinfo['subject']);
	$postinfo['message'] = dhtmlspecialchars($postinfo['message']);
	$postinfo['message'] = preg_replace($language['post_edit_regexp'], '', $postinfo['message']);

	if(!empty($previewpost)) {
		$postinfo['message'] = $message;
	}

	include template('post_editpost');

} else {

	if(empty($delete)) {

		if($post_invalid = checkpost()) {
			showmessage($post_invalid);
		}

		if($allowpostattach && is_array($_FILES['attach'])) {
			foreach($_FILES['attach']['name'] as $attachname) {
				if($attachname != '') {
					checklowerlimit($creditspolicy['postattach']);
					break;
				}
			}
		}

		if(!$isorigauthor && !$allowanonymous) {
			if($orig['anonymous'] && !$isanonymous) {
				$isanonymous = 0;
				$authoradd = ", author='".addslashes($orig['author'])."'";
				$anonymousadd = ", anonymous='0'";
			} else {
				$isanonymous = $orig['anonymous'];
				$authoradd = $anonymousadd = '';
			}
		} else {
			$authoradd = ", author='".($isanonymous ? '' : addslashes($orig['author']))."'";
			$anonymousadd = ", anonymous='$isanonymous'";
		}
		
		if($isfirstpost) {

			if($subject == '' || $message == '') {
				showmessage('post_sm_isnull');
			}

			$typeid = isset($forum['threadtypes']['types'][$typeid]) ? $typeid : 0;
			$iconid = isset($_DCACHE['icons'][$iconid]) ? $iconid : 0;

			if(!$typeid && $forum['threadtypes']['required']) {
				showmessage('post_type_isnull');
			}

			$readperm = $allowsetreadperm ? $readperm : ($isorigauthor ? 0 : 'readperm');
			$price = $thread['price'] < 0 ?
				($isorigauthor || !$price ? -1 : $price) :
				($maxprice ? ($price <= $maxprice ? ($price > 0 ? $price : 0) : $maxprice) : ($isorigauthor ? 0 : $thread['price']));

			if($price > 0 && floor($price * (1 - $creditstax)) == 0) {
				showmessage('post_net_price_iszero');
			}

			$polladd = '';
			if(($alloweditpoll || $thread['authorid'] == $discuz_uid) && $thread['poll'] && !empty($polloptions)) {
				$query = $db->query("SELECT pollopts FROM {$tablepre}polls WHERE tid='$tid'");
				$pollarray = unserialize($db->result($query, 0));

				$optsdeleted = 0;
				$pollarray['max'] = 0;
				foreach($polloptions as $key => $option) {
					if(trim($option)) {
						$pollarray['options'][$key][0] = $option;
						if($pollarray['options'][$key][1] > $pollarray['max']) {
							$pollarray['max'] = $pollarray['options'][$key][1];
						
						}
					} else {
						$optsdeleted = 1;
						$pollarray['total'] -= $pollarray['options'][$key][1];
						unset($pollarray['options'][$key]);
					}
				}

				if($optsdeleted) {
					$newoptions = array();
					foreach($pollarray['options'] as $option) {
						$newoptions[] = $option;
					}
					$pollarray['options'] = $newoptions;
					unset($newoptions);
				}

				if($pollarray['options']) {
					$polladd = ', poll=\'1\'';
					$pollarray['multiple'] = !empty($multiplepoll);
					$pollopts = addslashes(serialize($pollarray));
					$db->query("UPDATE {$tablepre}polls SET pollopts='$pollopts' WHERE tid='$tid'", 'UNBUFFERED');
				} else {
					$polladd = ', poll=\'0\'';
					$db->query("DELETE FROM {$tablepre}polls WHERE tid='$tid'", 'UNBUFFERED');
				}

			}

			$db->query("UPDATE {$tablepre}threads SET iconid='$iconid', typeid='$typeid', subject='$subject', readperm='$readperm', price='$price' $authoradd $polladd WHERE tid='$tid'", 'UNBUFFERED');

		} else {

			if($subject == '' && $message == '') {
				showmessage('post_sm_isnull');
			}

		}

		if($editedby && ($timestamp - $orig['dateline']) > 60 && $adminid != 1) {
			include_once language('misc');

			$editor = $isanonymous && $isorigauthor ? $language['anonymous'] : $discuz_userss;
			$edittime = gmdate($_DCACHE['settings']['dateformat'].' '.$_DCACHE['settings']['timeformat'], $timestamp + $timeoffset * 3600);
			eval("\$message .= \"$language[post_edit]\";");
		}

		$bbcodeoff = checkbbcodes($message, !empty($bbcodeoff));
		$smileyoff = checksmilies($message, !empty($smileyoff));
		$htmlon = $orig['allowhtml'] && !empty($htmlon) ? 1 : 0;

		$tattachment = 0;
		$pattachment = ($allowpostattach && $attachments = attach_upload()) ? 1 : 0;

		$query = $db->query("SELECT aid, readperm, description FROM {$tablepre}attachments WHERE pid='$pid'");
		while($attach = $db->fetch_array($query)) {
			if(($attachpermadd = $allowsetattachperm && $attach['readperm'] != $attachpermnew[$attach['aid']] ? ", readperm='{$attachpermnew[$attach['aid']]}'": '') || $attach['description'] != ($attachdescnew[$attach['aid']] = cutstr(dhtmlspecialchars($attachdescnew[$attach['aid']]), 100))) {
				$db->query("UPDATE {$tablepre}attachments SET description='{$attachdescnew[$attach['aid']]}' $attachpermadd WHERE aid='$attach[aid]'");
			}
		}

		if(!empty($deleteaid) || $pattachment) {

			if(!empty($deleteaid) && is_array($deleteaid)) {

				$deleteaids = '\''.implode("','", $deleteaid).'\'';
				$query = $db->query("SELECT aid, attachment FROM {$tablepre}attachments WHERE aid IN ($deleteaids) AND pid='$pid'");

				$deleteaids = '0';
				while($attach = $db->fetch_array($query)) {
					@unlink($attachdir.'/'.$attach['attachment']);
					$deleteaids .= ','.$attach['aid'];
				}

				$db->query("DELETE FROM {$tablepre}attachments WHERE aid IN ($deleteaids)");
				updatecredits($orig['authorid'], $creditspolicy['postattach'], -($db->affected_rows()));

			}

			if($pattachment) {
				foreach($attachments as $attach) {
					$db->query("INSERT INTO {$tablepre}attachments (tid, pid, dateline, readperm, filename, description, filetype, filesize, attachment, downloads)
						VALUES ('$tid', '$pid', '$timestamp', '$attach[perm]', '$attach[name]', '$attach[description]', '$attach[type]', '$attach[size]', '$attach[attachment]', '0')");
				}
				updatecredits($orig['authorid'], $creditspolicy['postattach'], count($attachments));
			} else {
				$query = $db->query("SELECT aid FROM {$tablepre}attachments WHERE pid='$pid' LIMIT 1");
				$pattachment = $db->result($query, 0) ? 1 : 0;
			}

			if($pattachment) {
				$tattachment = 1;
			} else {
				$query = $db->query("SELECT a.aid FROM {$tablepre}posts p, {$tablepre}attachments a WHERE a.tid='$tid' AND a.pid=p.pid AND p.invisible='0' LIMIT 1");
				$tattachment = $db->result($query, 0) ? 1 : 0;
			}

			$db->query("UPDATE {$tablepre}threads SET attachment='$tattachment' WHERE tid='$tid'");

		}

		$db->query("UPDATE {$tablepre}posts SET message='$message', usesig='$usesig', htmlon='$htmlon', bbcodeoff='$bbcodeoff', parseurloff='$parseurloff',
			smileyoff='$smileyoff', subject='$subject' ".($pattachment ? ", attachment='1'" : '')." $anonymousadd WHERE pid='$pid'");
		$forum['lastpost'] = explode("\t", $forum['lastpost']);

		if($orig['dateline'] == $forum['lastpost'][2] && ($orig['author'] == $forum['lastpost'][3] || ($forum['lastpost'][3] == '' && $orig['anonymous']))) {
			$lastpost = "$tid\t".($isfirstpost ? $subject : addslashes($thread['subject']))."\t$orig[dateline]\t".($isanonymous ? '' : addslashes($orig['author']));
			$db->query("UPDATE {$tablepre}forums SET lastpost='$lastpost' WHERE fid='$fid'", 'UNBUFFERED');
		}

		if($thread['lastpost'] == $orig['dateline'] && ((!$orig['anonymous'] && $thread['lastposter'] == $orig['author']) || ($orig['anonymous'] && $thread['lastposter'] == '')) && $orig['anonymous'] != $isanonymous) {
			$db->query("UPDATE {$tablepre}threads SET lastposter='".($isanonymous ? '' : addslashes($orig['author']))."' WHERE tid='$tid'", 'UNBUFFERED');
		}

		if(!$isorigauthor) {
			updatemodworks('EDT', 1);
			require_once DISCUZ_ROOT.'./include/misc.func.php';
			modlog($thread, 'EDT');
		}

	} else {

		if(!$isorigauthor || ($isfirstpost && $thread['replies'] >= 1)) {
			showmessage('post_edit_nopermission', NULL, 'HALTED');
		}

		updatepostcredits('-', $orig['authorid'], ($isfirstpost ? $postcredits : $replycredits));

		$thread_attachment = $post_attachment = 0;
		$query = $db->query("SELECT pid, attachment FROM {$tablepre}attachments WHERE tid='$tid'");
		while($attach = $db->fetch_array($query)) {
			if($attach['pid'] == $pid) {
				$post_attachment = 1;
				@unlink($attachdir.'/'.$attach['attachment']);
			} else {
				$thread_attachment = 1;
			}
		}

		if($post_attachment) {
			$db->query("DELETE FROM {$tablepre}attachments WHERE pid='$pid'", 'UNBUFFEREED');
		}

		$db->query("DELETE FROM {$tablepre}posts WHERE pid='$pid'");

		if($isfirstpost) {
			$forumadd = 'threads=threads-1, posts=posts-1';
			$db->query("DELETE FROM {$tablepre}threadsmod WHERE tid='$tid'", 'UNBUFFERED');
			$db->query("DELETE FROM {$tablepre}relatedthreads WHERE tid='$tid'", 'UNBUFFERED');
			$db->query("DELETE FROM {$tablepre}threads WHERE tid='$tid'", 'UNBUFFERED');
			$db->query("DELETE FROM {$tablepre}polls WHERE tid='$tid'", 'UNBUFFERED');
			if($globalstick && in_array($thread['displayorder'], array(2, 3))) {
				require_once DISCUZ_ROOT.'./include/cache.func.php';
				updatecache('globalstick');
			}
		} else {
			$forumadd = 'posts=posts-1';
			$query = $db->query("SELECT author, dateline FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline DESC LIMIT 1");
			$lastpost = $db->fetch_array($query);
			$lastpost['author'] = addslashes($lastpost['author']);
			$db->query("UPDATE {$tablepre}threads SET replies=replies-1, attachment='$thread_attachment', lastposter='$lastpost[author]', lastpost='$lastpost[dateline]' WHERE tid='$tid'", 'UNBUFFERED');
		}

		$forum['lastpost'] = explode("\t", $forum['lastpost']);
		if($orig['dateline'] == $forum['lastpost'][2] && ($orig['author'] == $forum['lastpost'][3] || ($forum['lastpost'][3] == '' && $orig['anonymous']))) {
			$query = $db->query("SELECT tid, subject, lastpost, lastposter FROM {$tablepre}threads
				WHERE fid='$fid' AND displayorder>='0' ORDER BY lastpost DESC LIMIT 1");
			$lastthread = $db->fetch_array($query);
			$forumadd .= ", lastpost='$lastthread[tid]\t$lastthread[subject]\t".addslashes($lastthread['lastpost'])."\t".addslashes($lastthread['lastposter'])."'";
		}

		$db->query("UPDATE {$tablepre}forums SET $forumadd WHERE fid='$fid'", 'UNBUFFERED');

	}

	(!empty($delete) && $isfirstpost) ? showmessage('post_edit_delete_succeed', "forumdisplay.php?fid=$fid") :
		showmessage('post_edit_succeed', "viewthread.php?tid=$tid&page=$page&extra=$extra#pid$pid");

}

?>