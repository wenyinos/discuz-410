<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: topicadmin.php,v $
	$Revision: 1.11 $
	$Date: 2006/02/23 13:44:02 $
*/


require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/post.func.php';
require_once DISCUZ_ROOT.'./include/misc.func.php';

$discuz_action = 201;
$modpostsnum = 0;
$resultarray = array();

if(!$discuz_uid || !$forum['ismoderator']) {
	showmessage('admin_nopermission', NULL, 'HALTED');
}

if($forum['type'] == 'forum') {
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a>";
	$navtitle = ' - '.strip_tags($forum['name']);
} else {
	$query = $db->query("SELECT fid, name FROM {$tablepre}forums WHERE fid='$forum[fup]'");
	$fup = $db->fetch_array($query);
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> ";
	$navtitle = ' - '.strip_tags($fup['name']).' - '.strip_tags($forum['name']);
}

if(!empty($tid)) {
	$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid='$tid' AND fid='$fid' AND displayorder>='0'");
	if($thread = $db->fetch_array($query)) {
		$navigation .= " &raquo; <a href=\"viewthread.php?tid=$tid\">$thread[subject]</a> ";
		$navtitle .= ' - '.$thread['subject'];
	} else {
		showmessage('thread_nonexistence');
	}
} elseif(!in_array($action, array('moderate', 'delpost', 'getip'))) {
	showmessage('undefined_action', NULL, 'HALTED');
}

// Reason P.M. Preprocess Start
$reasonpmcheck = $reasonpm == 2 || $reasonpm == 3 ? 'checked disabled' : '';
if(($reasonpm == 2 || $reasonpm == 3) || !empty($sendreasonpm)) {
	$forumname = strip_tags($forum['name']);
	$sendreasonpm = 1;
} else {
	$sendreasonpm = 0;
}
// End

$postcredits = $forum['postcredits'] ? $forum['postcredits'] : $creditspolicy['post'];
$replycredits = $forum['replycredits'] ? $forum['replycredits'] : $creditspolicy['reply'];

if(($action == 'moderate' && $fid) || in_array($action, array('delete', 'move', 'highlight', 'close', 'stick', 'digest'))) {

	if($action != 'moderate' ) {
		$operation = $action;
		$action = 'moderate';
		$moderate = array($tid);
	}

	if(empty($moderate) || !is_array($moderate) || !in_array($operation, array('delete', 'move', 'highlight', 'type', 'close', 'stick', 'digest')) || (!$allowdelpost && $operation == 'delete') || (!$allowstickthread && $operation == 'stick')) {
		showmessage('admin_moderate_invalid');
	}

	$single = count($moderate) == 1 ? true : false;
	$referer = $single ? "forumdisplay.php?fid=$fid" : dreferer();

	$tids = is_array($moderate) ? '\''.implode('\',\'', $moderate).'\'' : '';

	if(!submitcheck('modsubmit')) {

		if($operation == 'move') {
			require_once DISCUZ_ROOT.'./include/forum.func.php';
			$forumselect = forumselect();
		} elseif($operation == 'highlight') {
			$stylecheck = array();
			$colorcheck = array(0 => 'checked');
			if($single) {
				$string = sprintf('%02d', $thread['highlight']);
				$stylestr = sprintf('%03b', $string[0]);
				for($i = 1; $i <= 3; $i++) {
					$stylecheck[$i] = $stylestr[$i - 1] ? 'checked' : '';
				}
				$colorcheck = array($string[1] => 'checked');
			}
		} elseif($operation == 'type') {
			$typeselect = typeselect();
		}

		$threadlist = $loglist = array();
		$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid IN ($tids) AND fid='$fid' AND displayorder>='0' LIMIT $tpp");
		while($thread = $db->fetch_array($query)) {
			$thread['lastposterenc'] = rawurlencode($thread['lastposter']);
			$thread['lastpost'] = gmdate("$dateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600);
			$threadlist[] = $thread;
		}

		if(empty($threadlist)) {
			showmessage('admin_moderate_invalid');
		}

		if(in_array($operation, array('stick', 'digest', 'highlight', 'close'))) {

			$expirationmin = gmdate($dateformat, $timestamp + 86400 + $timeoffset * 3600);
			$expirationmax = gmdate($dateformat, $timestamp + 86400 * 180 + $timeoffset * 3600);

			$expirationdefault = '';
			$stickcheck  = $digestcheck = $closecheck = array();

			if($single) {

				empty($threadlist['0']['displayorder']) ? $stickcheck[1] ='checked' : $stickcheck[$threadlist['0']['displayorder']] = 'checked';
				empty($threadlist['0']['digest']) ? $digestcheck[1] = 'checked' : $digestcheck[$threadlist['0']['digest']] = 'checked';
				empty($threadlist['0']['closed']) ? $closecheck[0] = 'checked' : $closecheck[1] = 'checked';

				if($threadlist['0']['moderated']) {
					switch($operation) {
						case 'stick': $actionarray = array('EST'); break;
						case 'digest': $actionarray = array('EDI'); break;
						case 'highlight': $actionarray = array('EHL'); break;
						case 'close': $actionarray = array('ECL', 'EOP'); break;
					}
					$query = $db->query("SELECT * FROM {$tablepre}threadsmod WHERE tid='{$threadlist[0][tid]}' ORDER BY dateline DESC");
					while($log = $db->fetch_array($query)) {
						$log['dateline'] = gmdate("$dateformat $timeformat", $log['dateline'] + $timeoffset * 3600);
						$log['expiration'] = !empty($log['expiration']) ? gmdate("$dateformat", $log['expiration'] + $timeoffset * 3600) : '';
						if($log['status'] && in_array($log['action'], $actionarray)) { 
							$expirationdefault = $log['expiration'];
						}
						$log['status'] = empty($log['status']) ? 'style="text-decoration: line-through" disabled' : '';
						$loglist[] = $log;
					}
					if(!empty($loglist)) {
						include_once language('modactions');
					}
				}
			}
		}

		include template('topicadmin_moderate');

	} else {

		$moderatetids = '0';
		$threads = array();
		$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid IN ($tids) AND fid='$fid' AND displayorder>='0' LIMIT $tpp");
		while($thread = $db->fetch_array($query)) {
			$threads[] = $thread;
			$moderatetids .= ','.$thread['tid'];
			$modpostsnum ++;
		}

		checkreasonpm();

		if($operation == 'delete') {

			$stickmodify = 0;
			foreach($threads as $thread) {
				if($thread['digest']) {
					updatecredits($thread['authorid'], $creditspolicy['digest'], -$thread['digest'], 'digestposts=digestposts-1');
				}
				if(in_array($thread['displayorder'], array(2, 3))) {
					$stickmodify = 1;
				}
			}

			$losslessdel = $losslessdel > 0 ? $timestamp - $losslessdel * 86400 : 0;

			//Update members' credits and post counter
			$uidarray = $tuidarray = $ruidarray = array();
			$query = $db->query("SELECT first, authorid, dateline FROM {$tablepre}posts WHERE tid IN ($moderatetids)");
			while($post = $db->fetch_array($query)) {
				if($post['dateline'] < $losslessdel) {
					$uidarray[] = $post['authorid'];
				} else {
					if($post['first']) {
						$tuidarray[] = $post['authorid'];
					} else {
						$ruidarray[] = $post['authorid'];
					}
				}
			}

			if($uidarray) {
				updatepostcredits('-', $uidarray, array());
			}
			if($tuidarray) {
				updatepostcredits('-', $tuidarray, $postcredits);
			}
			if($ruidarray) {
				updatepostcredits('-', $ruidarray, $replycredits);
			}
			$modaction = 'DEL';

			if($forum['recyclebin']) {

				$db->query("UPDATE {$tablepre}threads SET displayorder='-1', digest='0', moderated='1' WHERE tid IN ($moderatetids)");
				$db->query("UPDATE {$tablepre}posts SET invisible='-1' WHERE tid IN ($moderatetids)");

			} else {

				$query = $db->query("SELECT attachment FROM {$tablepre}attachments WHERE tid IN ($moderatetids)");
				while($attach = $db->fetch_array($query)) {
					@unlink($attachdir.'/'.$attach['attachment']);
				}

				$db->query("DELETE FROM {$tablepre}attachments WHERE tid IN ($moderatetids)", 'UNBUFFERED');
				$db->query("DELETE FROM {$tablepre}threadsmod WHERE tid IN ($moderatetids)", 'UNBUFFERED');
				$db->query("DELETE FROM {$tablepre}threads WHERE tid IN ($moderatetids)");
				$db->query("DELETE FROM {$tablepre}relatedthreads WHERE tid IN ($moderatetids)");				
				$db->query("DELETE FROM {$tablepre}posts WHERE tid IN ($moderatetids)");
				$db->query("DELETE FROM {$tablepre}polls WHERE tid IN ($moderatetids)");

			}

			if($globalstick && $stickmodify) {
				require_once DISCUZ_ROOT.'./include/cache.func.php';
				updatecache('globalstick');
			}

			updateforumcount($fid);

		 } else {

		 	if(isset($expiration) && !empty($expiration) && in_array($operation, array('stick', 'digest', 'highlight', 'close'))) {
		 		$expiration = strtotime($expiration) - $timeoffset * 3600 + date('Z');
				if(gmdate('Ymd', $expiration + $timeoffset * 3600) <= gmdate('Ymd', $timestamp + $timeoffset * 3600) || ($expiration > $timestamp + 86400 * 180)) {
		 			showmessage('admin_expiration_invalid');
		 		}
		 	} else {
		 		$expiration = 0;
		 	}

		 	if($operation == 'stick' || $operation == 'digest') {

				$level = intval($level);
				if($level < 0 || $level > 3 || ( $operation == 'stick' && $level > $allowstickthread)){
					showmessage('undefined_action');
				}

				$expiration = $level ? $expiration : 0;

				if($operation == 'stick') {

					$db->query("UPDATE {$tablepre}threads SET displayorder='$level', moderated='1' WHERE tid IN ($moderatetids)");

					$stickmodify = 0;
					foreach($threads as $thread) {
						$stickmodify = (in_array($thread['displayorder'], array(2, 3)) || in_array($level, array(2, 3))) && $level != $thread['displayorder'] ? 1 : $stickmodify;
					}

					if($globalstick && $stickmodify) {
						require_once DISCUZ_ROOT.'./include/cache.func.php';
						updatecache('globalstick');
					}

					$modaction = $level ? ($expiration ? 'EST' : 'STK') : 'UST';
					$db->query("UPDATE {$tablepre}threadsmod SET status='0' WHERE tid IN ($moderatetids) AND action IN ('STK', 'UST', 'EST', 'UES')", 'UNBUTTERED');

				} elseif($operation == 'digest') {

					$db->query("UPDATE {$tablepre}threads SET digest='$level', moderated='1' WHERE tid IN ($moderatetids)");

					foreach($threads as $thread) {
						if($thread['digest'] != $level) {
							$digestpostsadd = ($thread['digest'] > 0 && $level == 0) || ($thread['digest'] == 0 && $level > 0) ? 'digestposts=digestposts'.($level == 0 ? '-' : '+').'1' : '';
							updatecredits($thread['authorid'], $creditspolicy['digest'], $level - $thread['digest'], $digestpostsadd);
						}
					}

					$modaction = $level ? ($expiration ? 'EDI' : 'DIG') : 'UDG';
					$db->query("UPDATE {$tablepre}threadsmod SET status='0' WHERE tid IN ($moderatetids) AND action IN ('DIG', 'UDI', 'EDI', 'UED')", 'UNBUTTERED');
				}

			} elseif($operation == 'close') {

				$modaction = empty($close) ? ($expiration ? 'EOP' : 'OPN'): ($expiration ? 'ECL' : 'CLS');
				$close = ($modaction == 'ECL' || $modaction == 'CLS') ? 1 : 0;

				$db->query("UPDATE {$tablepre}threads SET closed='$close', moderated='1' WHERE tid IN ($moderatetids)");
				$db->query("UPDATE {$tablepre}threadsmod SET status='0' WHERE tid IN ($moderatetids) AND action IN ('CLS','OPN', 'ECL', 'UCL', 'EOP', 'UEO')", 'UNBUTTERED');

			} elseif($operation == 'move') {

				if($fid == $moveto) {
					showmessage('admin_move_illegal');
				}
				
				$query = $db->query("SELECT fid, name FROM {$tablepre}forums WHERE fid='$moveto' AND status='1' AND type<>'group'");
				if(!$toforum = $db->fetch_array($query)) {
					showmessage('admin_move_invalid');
				}

				$stickmodify = 0;
				foreach($threads as $thread) {
					if(in_array($thread['displayorder'], array(2, 3))) {
						$stickmodify = 1;
					}
				}

				$displayorderadd = $adminid == 3 ? ', displayorder=\'0\'' : '';

				$db->query("UPDATE {$tablepre}threads SET fid='$moveto', moderated='1' $displayorderadd WHERE tid IN ($moderatetids)");
				$db->query("UPDATE {$tablepre}posts SET fid='$moveto' WHERE tid IN ($moderatetids)");

				if($type == 'redirect') {
					foreach($threads as $thread) {
						$db->query("INSERT INTO {$tablepre}threads (fid, readperm, iconid, author, authorid, subject, dateline, lastpost, lastposter, views, replies, displayorder, digest, closed, poll, attachment)
							VALUES ('$thread[fid]', '$thread[readperm]', '$thread[iconid]', '".addslashes($thread['author'])."', '$thread[authorid]', '".addslashes($thread['subject'])."', '$thread[dateline]', '$thread[lastpost]', '$thread[lastposter]', '0', '0', '0', '0', '$thread[tid]', '0', '0')");
					}
				}

				if($globalstick && $stickmodify) {
					require_once DISCUZ_ROOT.'./include/cache.func.php';
					updatecache('globalstick');
				}

				$modaction = 'MOV';

				updateforumcount($moveto);
				updateforumcount($fid);

			} elseif($operation == 'highlight') {

				$stylebin = '';
				for($i = 1; $i <= 3; $i++) {
					$stylebin .= empty($highlight_style[$i]) ? '0' : '1';
				}

				$highlight_style = bindec($stylebin);
				if($highlight_style < 0 || $highlight_style > 7 || $highlight_color < 0 || $highlight_color > 8) {
					showmessage('undefined_action', NULL, 'HALTED');
				}

				$db->query("UPDATE {$tablepre}threads SET highlight='$highlight_style$highlight_color', moderated='1' WHERE tid IN ($moderatetids)", 'UNBUFFERED');

				$modaction = ($highlight_style + $highlight_color) ? ($expiration ? 'EHL' : 'HLT') : 'UHL';
				$expiration = $modaction == 'UHL' ? 0 : $expiration;
				$db->query("UPDATE {$tablepre}threadsmod SET status='0' WHERE tid IN ($moderatetids) AND action IN ('HLT', 'UHL', 'EHL', 'UEH')", 'UNBUTTERED');

			} elseif($operation == 'type') {

				if(!isset($forum['threadtypes']['types'][$typeid]) && !($typeid == 0 && !$forum['threadtypes']['required'])) {
					showmessage('admin_move_invalid');
				}

				$db->query("UPDATE {$tablepre}threads SET typeid='$typeid', moderated='1' WHERE tid IN ($moderatetids)");

				$modaction = 'TYP';

			}

		}
		
		$resultarray = array(
			'redirect'	=> (preg_match("/^topicadmin/", ($redirect = dreferer("forumdisplay.php?fid=$fid"))) ? "forumdisplay.php?fid=$fid" : $redirect),
			'reasonpm'	=> ($sendreasonpm ? array('data' => $threads, 'var' => 'thread', 'item' => ($operation == 'move' ? 'reason_move' : 'reason_moderate')) : array()),
			'modtids'	=> ($operation == 'delete' && !$forum['recyclebin']) ? 0 : $moderatetids,
			'modlog'	=> $threads,
			'expiration'=> $expiration
		);

		if(in_array($operation, array('stick', 'digest', 'highlight')) && !empty($next) && $next != $operation && in_array($next, array('stick', 'digest', 'highlight'))) {
			if(count($moderate) == 1) {
				$resultarray['redirect'] = "topicadmin.php?tid=$moderate[0]&fid=$fid&action=$next";
			} else {
				$resultarray['redirect'] = "topicadmin.php?action=moderate&fid=$fid&operation=$next";
				if(is_array($moderate)) {
					foreach($moderate as $modtid) {
						$resultarray['redirect'] .= "&moderate[]=$modtid";
					}
				}
			}
			$resultarray['message'] = 'admin_succeed_next';
		}
	}

} elseif($action == 'delpost') {

	if(!$allowdelpost || !$tid) {
		showmessage('admin_nopermission', NULL, 'HALTED');
	} elseif(!is_array($delete) && !count($delete)) {
		showmessage('admin_delpost_invalid');
	} else {
		$deletepids = '\''.implode('\',\'', $delete).'\'';
		$query = $db->query("SELECT pid FROM {$tablepre}posts WHERE pid IN ($deletepids) AND first='1'");
		if($db->num_rows($query)) {
			header("Location: {$boardurl}topicadmin.php?action=delete&tid=$thread[tid]");
			dexit();
		}
	}

	if(!submitcheck('delpostsubmit')) {

		$deleteid = '';
		foreach($delete as $id) {
			$deleteid .= '<input type="hidden" name="delete[]" value="'.$id.'">';
		}

		include template('topicadmin_delpost');

	} else {

		checkreasonpm();

		$pids = 0;
		$posts = $uidarray = $puidarray = array();
		$losslessdel = $losslessdel > 0 ? $timestamp - $losslessdel * 86400 : 0;
		$query = $db->query("SELECT pid, authorid, dateline, message FROM {$tablepre}posts WHERE pid IN ($deletepids) AND tid='$tid'");
		while($post = $db->fetch_array($query)) {
			$posts[] = $post;
			$pids .= ','.$post['pid'];
			$comma = ',';
			if($post['dateline'] < $losslessdel) {
				$uidarray[] = $post['authorid'];
			} else {
				$puidarray[] = $post['authorid'];
			}
			$modpostsnum ++;
		}

		if($uidarray) {
			updatepostcredits('-', $uidarray, array());
		}
		if($puidarray) {
			updatepostcredits('-', $puidarray, $replycredits);
		}

		$query = $db->query("SELECT attachment FROM {$tablepre}attachments WHERE pid IN ($pids)");
		while($attach = $db->fetch_array($query)) {
			@unlink($attachdir.'/'.$attach['attachment']);
		}

		$db->query("DELETE FROM {$tablepre}attachments WHERE pid IN ($pids)");
		$db->query("DELETE FROM {$tablepre}posts WHERE pid IN ($pids)");

		updatethreadcount($tid, 1);
		updateforumcount($fid);

		$modaction = 'DLP';

		$resultarray = array(
			'redirect'	=> "viewthread.php?tid=$tid&page=$page",
			'reasonpm'	=> ($sendreasonpm ? array('data' => $posts, 'var' => 'post', 'item' => 'reason_delete_post') : array()),
			'modtids'	=> 0,
			'modlog'	=> $thread
		);

	}

} elseif($action == 'refund' && $allowrefund && $thread['price'] > 0) {

	if(!isset($extcredits[$creditstrans])) {
		showmessage('credits_transaction_disabled');
	}

	if(!submitcheck('refundsubmit')) {

		$query = $db->query("SELECT COUNT(*) AS payers, SUM(netamount) AS netincome FROM {$tablepre}paymentlog WHERE tid='$tid'");
		$payment = $db->fetch_array($query);

		include template('topicadmin_refund');

	} else {

		$modaction = 'RFD';
		$modpostsnum ++;

		checkreasonpm();

		$totalamount = 0;
		$amountarray = array();

		$logarray = array();
		$query = $db->query("SELECT * FROM {$tablepre}paymentlog WHERE tid='$tid'");
		while($log = $db->fetch_array($query)) {
			$totalamount += $log['amount'];
			$amountarray[$log['amount']][] = $log['uid'];
		}

		$db->query("UPDATE {$tablepre}members SET extcredits$creditstrans=extcredits$creditstrans-$totalamount WHERE uid='$thread[authorid]'");
		$db->query("UPDATE {$tablepre}threads SET price='-1', moderated='1' WHERE tid='$thread[tid]'");

		foreach($amountarray as $amount => $uidarray) {
			$db->query("UPDATE {$tablepre}members SET extcredits$creditstrans=extcredits$creditstrans+$amount WHERE uid IN (".implode(',', $uidarray).")");
		}

		$db->query("UPDATE {$tablepre}paymentlog SET amount='0', netamount='0' WHERE tid='$tid'");

		$resultarray = array(
			'redirect'	=> "viewthread.php?tid=$tid",
			'reasonpm'	=> ($sendreasonpm ? array('data' => array($thread), 'var' => 'thread', 'item' => 'reason_moderate') : array()),
			'modtids'	=> $thread['tid'],
			'modlog'	=> $thread
		);

	}

} elseif($action == 'repair') {

	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0'");
	$replies = $db->result($query, 0) - 1;

	$query = $db->query("SELECT a.aid FROM {$tablepre}posts p, {$tablepre}attachments a WHERE a.tid='$tid' AND a.pid=p.pid AND p.invisible='0' LIMIT 1");
	$attachment = $db->num_rows($query) ? 1 : 0;


	$query  = $db->query("SELECT pid, subject, rate FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline LIMIT 1");
	$firstpost = $db->fetch_array($query);
	$firstpost['subject'] = addslashes(cutstr($firstpost['subject'], 79));
	@$firstpost['rate'] = $firstpost['rate'] / abs($firstpost['rate']);

	$query  = $db->query("SELECT author, dateline FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline DESC LIMIT 1");
	$lastpost = $db->fetch_array($query);

	$db->query("UPDATE {$tablepre}threads SET subject='$firstpost[subject]', replies='$replies', lastpost='$lastpost[dateline]', lastposter='".addslashes($lastpost['author'])."', rate='$firstpost[rate]', attachment='$attachment' WHERE tid='$tid'", 'UNBUFFERED');
	$db->query("UPDATE {$tablepre}posts SET first='1', subject='$firstpost[subject]' WHERE pid='$firstpost[pid]'", 'UNBUFFERED');
	$db->query("UPDATE {$tablepre}posts SET first='0' WHERE tid='$tid' AND pid<>'$firstpost[pid]'", 'UNBUFFERED');
	showmessage('admin_succeed', "viewthread.php?tid=$tid");

} elseif($action == 'getip' && $allowviewip) {

	require_once DISCUZ_ROOT.'./include/misc.func.php';

	$query = $db->query("SELECT m.adminid, p.useip FROM {$tablepre}posts p
				LEFT JOIN {$tablepre}members m ON m.uid=p.authorid
				WHERE pid='$pid' AND tid='$tid'");
	if(!$member = $db->fetch_array($query)) {
		showmessage('thread_nonexistence', NULL, 'HALTED');
	} elseif(($member['adminid'] == 1 && $adminid > 1) || ($member['adminid'] == 2 && $adminid > 2)) {
		showmessage('admin_getip_nopermission', NULL, 'HALTED');
	}

	$member['iplocation'] = convertip($member['useip']);

	include template('topicadmin_getip');

} elseif($action == 'bump') {

	if(!submitcheck('bumpsubmit')) {

		include template('topicadmin_bump');

	} else {

		$modaction = 'BMP';
		$modpostsnum ++;

		$query = $db->query("SELECT tid, subject, lastposter, lastpost FROM {$tablepre}threads WHERE tid='$tid' LIMIT 1");
		$thread = $db->fetch_array($query);
		$thread['subject'] = addslashes($thread['subject']);
		$thread['lastposter'] = addslashes($thread['lastposter']);

		$db->query("UPDATE {$tablepre}threads SET lastpost='$timestamp', moderated='1' WHERE tid='$tid'");
		$db->query("UPDATE {$tablepre}forums SET lastpost='$thread[tid]\t$thread[subject]\t$timestamp\t$thread[lastposter]' WHERE fid='$fid'");

		$resultarray = array(
			'redirect'	=> "forumdisplay.php?fid=$fid",
			'reasonpm'	=> array(),
			'modtids'	=> $thread['tid'],
			'modlog'	=> $thread
		);

	}

} elseif($action == 'split') {

	if(!submitcheck('splitsubmit')) {

		require_once DISCUZ_ROOT.'./include/discuzcode.func.php';

		$replies = $thread['replies'];
		if($replies <= 0) {
			showmessage('admin_split_invalid');
		}

		$postlist = array();
		$query = $db->query("SELECT * FROM {$tablepre}posts WHERE tid='$tid' ORDER BY dateline");
		while($post = $db->fetch_array($query)) {
			$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml']);
			$postlist[] = $post;
		}

		include template('topicadmin_split');

	} else {

		if(!trim($subject)) {
			showmessage('admin_split_subject_invalid');
		}

		$pids = $comma = '';
		if(is_array($split)) {
			foreach($split as $pid) {
				$pids .= $comma.$pid;
				$comma = ',';
			}
		}
		if($pids) {

			$modaction = 'SPL';

			$db->query("INSERT INTO {$tablepre}threads (fid, subject) VALUES ('$fid', '".dhtmlspecialchars($subject)."')");
			$newtid = $db->insert_id();

			$db->query("UPDATE {$tablepre}posts SET tid='$newtid' WHERE pid IN ($pids)");
			$db->query("UPDATE {$tablepre}attachments SET tid='$newtid' WHERE pid IN ($pids)");

			$query = $db->query("SELECT pid FROM {$tablepre}posts WHERE tid='$newtid' AND invisible='0' ORDER BY dateline LIMIT 1");
			$db->query("UPDATE {$tablepre}posts SET first='1', subject='$subject' WHERE pid='".($db->result($query, 0))."'", 'UNBUFFERED');

			$query = $db->query("SELECT pid, author, authorid, dateline FROM {$tablepre}posts WHERE tid='$tid' ORDER BY dateline LIMIT 1");
			$fpost = $db->fetch_array($query);
			$db->query("UPDATE {$tablepre}threads SET author='$fpost[author]', authorid='$fpost[authorid]', dateline='$fpost[dateline]', moderated='1' WHERE tid='$tid'");
			$db->query("UPDATE {$tablepre}posts SET subject='".addslashes($thread['subject'])."' WHERE pid='$fpost[pid]'");

			$query = $db->query("SELECT author, authorid, dateline, rate FROM {$tablepre}posts WHERE tid='$newtid' ORDER BY dateline ASC LIMIT 1");
			$fpost = $db->fetch_array($query);
			$db->query("UPDATE {$tablepre}threads SET author='$fpost[author]', authorid='$fpost[authorid]', dateline='$fpost[dateline]', rate='".intval(@($fpost['rate'] / abs($fpost['rate'])))."', moderated='1' WHERE tid='$newtid'");

			updatethreadcount($tid);
			updatethreadcount($newtid);
			updateforumcount($fid);

			$modpostsnum++;
			$resultarray = array(
				'redirect'	=> "forumdisplay.php?fid=$fid",
				'reasonpm'	=> array(),
				'modtids'	=> $thread['tid'].','.$newtid,
				'modlog'	=> array($thread, array('tid' => $newtid, 'subject' => $subject))
			);

		} else {
			showmessage('admin_split_new_invalid');
		}
	}

} elseif($action == 'merge') {

	if(!submitcheck('mergesubmit')) {

		include template('topicadmin_merge');

	} else {

		$modaction = 'MRG';

		$query = $db->query("SELECT tid, fid, subject, views, replies FROM {$tablepre}threads WHERE tid='$othertid' AND displayorder>='0'");
		if(!$other = $db->fetch_array($query)) {
			showmessage('admin_merge_nonexistence');
		}
		if($othertid == $tid || ($adminid == 3 && $other['fid'] != $forum['fid'])) {
			showmessage('admin_merge_invalid');
		}

		$other['views'] = intval($other['views']);
		$other['replies']++;

		$db->query("UPDATE {$tablepre}posts SET tid='$tid' WHERE tid='$othertid'");
		$postsmerged = $db->affected_rows();

		$db->query("UPDATE {$tablepre}attachments SET tid='$tid' WHERE tid='$othertid'");
		$db->query("DELETE FROM {$tablepre}threads WHERE tid='$othertid'");
		$db->query("DELETE FROM {$tablepre}threadsmod WHERE tid='$othertid'");
		$db->query("DELETE FROM {$tablepre}polls WHERE tid='$othertid'");

		$query = $db->query("SELECT pid, authorid, author, subject, dateline FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline LIMIT 1");
		$firstpost = $db->fetch_array($query);
		$db->query("UPDATE {$tablepre}posts SET first=(pid='$firstpost[pid]') WHERE tid='$tid'");
		$db->query("UPDATE {$tablepre}threads SET authorid='$firstpost[authorid]', author='".addslashes($firstpost['author'])."', subject='".addslashes($firstpost['subject'])."', dateline='$firstpost[dateline]', views=views+$other[views], replies=replies+$other[replies], moderated='1' WHERE tid='$tid'");

		if($fid == $other['fid']) {
			$db->query("UPDATE {$tablepre}forums SET threads=threads-1 WHERE fid='$fid'");
		} else {
			$db->query("UPDATE {$tablepre}forums SET threads=threads-1, posts=posts-$postsmerged WHERE fid='$other[fid]'");
			$db->query("UPDATE {$tablepre}forums SET posts=$posts+$postsmerged WHERE fid='$fid'");
		}
		$modpostsnum ++;
		$resultarray = array(
			'redirect'	=> "forumdisplay.php?fid=$fid",
			'reasonpm'	=> array(),
			'modtids'	=> $thread['tid'],
			'modlog'	=> array($thread, $other)
		);

	}

} else {

	showmessage('undefined_action', NULL, 'HALTED');

}

if($resultarray) {

	if($resultarray['modtids']) {
		updatemodlog($resultarray['modtids'], $modaction, $resultarray['expiration']);
	}
 
	updatemodworks($modaction, $modpostsnum);		
	if(is_array($resultarray['modlog'])) {
		if(isset($resultarray['modlog']['tid'])) {
			modlog($resultarray['modlog'], $modaction);
		} else {
			foreach($resultarray['modlog'] as $thread) {
				modlog($thread, $modaction);
			}
		}
	}

	if($resultarray['reasonpm']) {
		include language('modactions');
		$modaction = $modactioncode[$modaction];
		foreach($resultarray['reasonpm']['data'] as ${$resultarray['reasonpm']['var']}) {
			sendreasonpm($resultarray['reasonpm']['var'], $resultarray['reasonpm']['item']);
		}
	}

	showmessage((isset($resultarray['message']) ? $resultarray['message'] : 'admin_succeed'), $resultarray['redirect']);

}

?>