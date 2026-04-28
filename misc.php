<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: misc.php,v $
	$Revision: 1.28 $
	$Date: 2006/02/23 13:44:02 $
*/

require_once './include/common.inc.php';

if($action == 'maxpages') {

	$pages = intval($pages);
	if(empty($pages)) {
		showmessage('undefined_action', NULL, 'HALTED');
	} else {
		showmessage('max_pages');
	}

} elseif($action == 'customtopics') {

	if(!submitcheck('keywordsubmit', 1)) {

		if($_DCOOKIE['customkw']) {
			$customkwlist = array();
			foreach(explode("\t", trim($_DCOOKIE['customkw'])) as $key => $keyword) {
				$keyword = dhtmlspecialchars(trim(stripslashes($keyword)));
				$customkwlist[$key]['keyword'] = $keyword;
				$customkwlist[$key]['url'] = '<a href="topic.php?keyword='.rawurlencode($keyword).'" target="_blank">'.$keyword.'</a> ';
			}
		}

		include template('customtopics');

	} else {

		if(!empty($delete) && is_array($delete)) {
			$keywords = implode("\t", array_diff(explode("\t", $_DCOOKIE['customkw']), $delete));
		} else {
			$keywords = $_DCOOKIE['customkw'];
		}

		if($newkeyword = cutstr(dhtmlspecialchars(preg_replace("/[\s\|\t\,\'\<\>]/", '', $newkeyword)), 20)) {
			if($_DCOOKIE['customkw']) {
				if(!preg_match("/(^|\t)".preg_quote($newkeyword, '/')."($|\t)/i", $keywords)) {
					if(count(explode("\t", $keywords)) >= $qihoo_maxtopics) {
						$keywords = substr($keywords, (strpos($keywords, "\t") + 1))."\t".$newkeyword;
					} else {
						$keywords .= "\t".$newkeyword;
					}
				}
			} else {
				$keywords = $newkeyword;
			}
		}

		dsetcookie('customkw', stripslashes($keywords), 315360000);
		header("Location: {$boardurl}misc.php?action=customtopics");

	}

} else {

	if(empty($forum['allowview'])) {
		if(!$forum['viewperm'] && !$readaccess) {
			showmessage('group_nopermission', NULL, 'NOPERM');
		} elseif($forum['viewperm'] && !forumperm($forum['viewperm'])) {
			showmessage('forum_nopermission', NULL, 'NOPERM');
		}
	} elseif($thread['readperm'] && $thread['readperm'] > $readaccess && !$forum['ismoderator'] && $thread['authorid'] != $discuz_uid) {
		showmessage('thread_nopermission', NULL, 'NOPERM');
	}

	$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid='$tid' AND displayorder>='0'");
	if(!$thread = $db->fetch_array($query)) {
		showmessage('thread_nonexistence');
	}

	if($forum['type'] == 'forum') {
		$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$thread[subject]</a> ";
		$navtitle = ' - '.strip_tags($forum['name']).' - '.$thread['subject'];
	} elseif($forum['type'] == 'sub') {
		$query = $db->query("SELECT name, fid FROM {$tablepre}forums WHERE fid='$forum[fup]'");
		$fup = $db->fetch_array($query);
		$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$thread[subject]</a> ";
		$navtitle = ' - '.strip_tags($fup['name']).' - '.strip_tags($forum['name']).' - '.$thread['subject'];
	}

}

if($action == 'votepoll') {

	if(!$allowvote) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	}

	$query = $db->query("SELECT pollopts FROM {$tablepre}polls WHERE tid='$tid'");
	$pollarray = unserialize($db->result($query, 0));
	if(!is_array($pollarray) || !$pollarray) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	if(!empty($thread['closed'])) {
		showmessage('thread_poll_closed');
	}

	if(in_array(($discuz_uid ? $discuz_user : $onlineip), $pollarray['voters'])) {
		showmessage('thread_poll_voted');
	}

	if(!is_array($pollanswers) || count($pollanswers) < 1) {
		showmessage('thread_poll_invalid');
	}

	if(empty($pollarray['multiple']) && count($pollanswers) > 1) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	foreach($pollanswers as $id) {
		if(isset($pollarray['options'][$id][0])) {
			if(++$pollarray['options'][$id][1] > $pollarray['max']) {
				$pollarray['max'] = $pollarray['options'][$id][1];
			}
			$pollarray['total']++;
		} else {
			showmessage('undefined_action', NULL, 'HALTED');
		}
	}

	$pollarray['voters'][] = $discuz_uid ? $discuz_user : $onlineip;

	$pollopts = addslashes(serialize($pollarray));
	$db->query("UPDATE {$tablepre}polls SET pollopts='$pollopts' WHERE tid='$tid'", 'UNBUFFERED');
	$db->query("UPDATE {$tablepre}threads SET lastpost='$timestamp' WHERE tid='$tid'", 'UNBUFFERED');

	showmessage('thread_poll_succeed', "viewthread.php?tid=$tid");

} elseif($action == 'emailfriend') {

	if(!$discuz_uid) {
		showmessage('not_loggedin', NULL, 'NOPERM');
	}

	$discuz_action = 122;

	if(!submitcheck('sendsubmit')) {

		$threadurl = "{$boardurl}viewthread.php?tid=$tid";

		$query = $db->query("SELECT email FROM {$tablepre}members WHERE uid='$discuz_uid'");
		$email = $db->result($query, 0);

		include template('emailfriend');

	} else {

		if(empty($fromname) || empty($fromemail) || empty($sendtoname) || empty($sendtoemail)) {
			showmessage('email_friend_invalid');
		}

		sendmail($sendtoemail, 'email_to_friend_subject', 'email_to_friend_message', "$fromname <$fromemail>");

		showmessage('email_friend_succeed', "viewthread.php?tid=$tid");

	}

} elseif($action == 'rate' && $pid) {

	if(!$raterange) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	} elseif($modratelimit && $adminid == 3 && !$forum['ismoderator']) {
		showmessage('thread_rate_moderator_invalid');
	}

	$reasonpmcheck = $reasonpm == 2 || $reasonpm == 3 ? 'checked disabled' : '';
	if(($reasonpm == 2 || $reasonpm == 3) || !empty($sendreasonpm)) {
		$forumname = strip_tags($forum['name']);
		$sendreasonpm = 1;
	} else {
		$sendreasonpm = 0;
	}

	foreach($raterange as $id => $rating) {
		$maxratetoday[$id] = $rating['mrpd'];
	}

	//maxratetoday: how much quota of rating left today
	$query = $db->query("SELECT extcredits, SUM(ABS(score)) AS todayrate FROM {$tablepre}ratelog
		WHERE uid='$discuz_uid' AND dateline>=$timestamp-86400
		GROUP BY extcredits");
	while($rate = $db->fetch_array($query)) {
		$maxratetoday[$rate['extcredits']] = $raterange[$rate['extcredits']]['mrpd'] - $rate['todayrate'];
	}

	$query = $db->query("SELECT * FROM {$tablepre}posts WHERE pid='$pid' AND invisible='0' AND authorid<>'0'");
	if(!($post = $db->fetch_array($query)) || $post['tid'] != $thread['tid'] || !$post['authorid']) {
		showmessage('undefined_action');
	} elseif(!$forum['ismoderator'] && $karmaratelimit && $timestamp - $post['dateline'] > $karmaratelimit * 3600) {
		showmessage('thread_rate_timelimit');
	} elseif($post['authorid'] == $discuz_uid || $post['tid'] != $tid) {
		showmessage('thread_rate_member_invalid');
	}

	if(!$dupkarmarate) {
		$query = $db->query("SELECT pid FROM {$tablepre}ratelog WHERE uid='$discuz_uid' AND pid='$pid' LIMIT 1");
		if($db->num_rows($query)) {
			showmessage('thread_rate_duplicate');
		}
	}

	$discuz_action = 71;

	$page = intval($page);

	if(!submitcheck('ratesubmit')) {

		$referer = $boardurl.'viewthread.php?tid='.$tid.'&page='.$page.'#pid'.$pid;

		$ratelist = array();
		foreach($raterange as $id => $rating) {
			if(isset($extcredits[$id])) {
				$ratelist[$id] = '';
				$offset = abs(ceil(($rating['max'] - $rating['min']) / 32));
				for($vote = $rating['min']; $vote <= $rating['max']; $vote += $offset) {
					$ratelist[$id] .= $vote ? '<option value="'.$vote.'">'.($vote > 0 ? '+'.$vote : $vote).'</option>' : '';
				}
			}
		}

		include template('rate');

	} else {

		require_once DISCUZ_ROOT.'./include/misc.func.php';
		checkreasonpm();

		$rate = $ratetimes = 0;
		$creditsarray = array();
		foreach($raterange as $id => $rating) {
			$score = intval(${'score'.$id});
			if(isset($extcredits[$id]) && !empty($score)) {
				if(abs($score) <= $maxratetoday[$id]) {
					if($score > $rating['max'] || $score < $rating['min']) {
						showmessage('thread_rate_range_invalid');
					} else {
						$creditsarray[$id] = $score;
						$rate += $score;
						$ratetimes += ceil(max(abs($rating['min']), abs($rating['max'])) / 5);
					}
				} else {
					showmessage('thread_rate_ctrl');
				}
			}
		}

		if(!$creditsarray) {
			showmessage('thread_rate_range_invalid');
		}

		updatecredits($post['authorid'], $creditsarray);

		$db->query("UPDATE {$tablepre}posts SET rate=rate+($rate), ratetimes=ratetimes+$ratetimes WHERE pid='$pid'");
		if($post['first']) {
			$threadrate = intval(@($post['rate'] + $rate) / abs($post['rate'] + $rate));
			$db->query("UPDATE {$tablepre}threads SET rate='$threadrate' WHERE tid='$tid'");
		}

		$sqlvalues = $comma = '';
		$sqlreason = cutstr($reason, 40);
		foreach($creditsarray as $id => $addcredits) {
			$sqlvalues .= "$comma('$pid', '$discuz_uid', '$discuz_user', '$id', '$timestamp', '$addcredits', '$sqlreason')";
			$comma = ', ';
		}
		$db->query("INSERT INTO {$tablepre}ratelog (pid, uid, username, extcredits, dateline, score, reason)
			VALUES $sqlvalues", 'UNBUFFERED');

		if($sendreasonpm) {
			require_once DISCUZ_ROOT.'./include/misc.func.php';
			$ratescore = $slash = '';
			foreach($creditsarray as $id => $addcredits) {
				$ratescore .= $slash.$extcredits[$id]['title'].' '.($addcredits > 0 ? '+'.$addcredits : $addcredits).' '.$extcredits[$id]['unit'];
				$slash = ' / ';
			}
			sendreasonpm('post', 'rate_reason');
		}

		$reason = dhtmlspecialchars($reason);
		@$fp = fopen(DISCUZ_ROOT.'./forumdata/ratelog.php', 'a');
		@flock($fp, 2);
		foreach($creditsarray as $id => $addcredits) {
			@fwrite($fp, "$timestamp\t".dhtmlspecialchars($discuz_userss)."\t$adminid\t".dhtmlspecialchars($post['author'])."\t$id\t$addcredits\t$tid\t$thread[subject]\t$reason\n");
		}
		@fclose($fp);

		showmessage('thread_rate_succeed', dreferer());

	}

} elseif($action == 'viewratings' && $pid) {

	$queryr = $db->query("SELECT * FROM {$tablepre}ratelog WHERE pid='$pid' ORDER BY dateline");
	$queryp = $db->query("SELECT p.* ".($bannedmessages ? ", m.groupid " : '').
		" FROM {$tablepre}posts p ".
		($bannedmessages ? "LEFT JOIN {$tablepre}members m ON m.uid=p.authorid" : '').
		" WHERE p.pid='$pid' AND p.invisible='0'");

	if(!($db->num_rows($queryr)) || !($db->num_rows($queryp))) {
		showmessage('thread_rate_log_nonexistence');
	}

	$post = $db->fetch_array($queryp);
	if($post['tid'] != $thread['tid']) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	$discuz_action = 72;

	if(!$bannedmessages || !$post['authorid'] || ($bannedmessages && $post['authorid'] && !in_array(intval($author['groupid']), array(0, 4, 5)))) {
		require_once DISCUZ_ROOT.'./include/discuzcode.func.php';
		$post['dateline'] = gmdate("$dateformat $timeformat", $post['dateline'] + $timeoffset * 3600);
		$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], $forum['jammer']);
	} else {
		$post['message'] = '';
	}

	$loglist = array();
	while($log = $db->fetch_array($queryr)) {
		$log['dateline'] = gmdate("$dateformat $timeformat", $log['dateline'] + $timeoffset * 3600);
		$log['score'] = $log['score'] > 0 ? '+'.$log['score'] : $log['score'];
		$log['reason'] = dhtmlspecialchars($log['reason']);
		$loglist[] = $log;
	}

	include template('rate_view');

} elseif($action == 'pay') {

	if(!isset($extcredits[$creditstrans])) {
		showmessage('credits_transaction_disabled');
	} elseif($thread['price'] <= 0) {
		showmessage('undefined_action', NULL, 'HALTED');
	} elseif(!$discuz_uid) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	}

	if(($balance = ${'extcredits'.$creditstrans} - $thread['price']) < ($minbalance = 0)) {
		showmessage('credits_balance_insufficient');
	}

	$discuz_action = 81;

	$thread['netprice'] = floor($thread['price'] * (1 - $creditstax));

	if(!submitcheck('paysubmit')) {

		include template('pay');

	} else {

		$updateauthor = true;
		if($maxincperthread > 0) {
			$query = $db->query("SELECT SUM(netamount) FROM {$tablepre}paymentlog WHERE tid='$tid'");
			if(($db->result($query, 0)) > $maxincperthread) {
				$updateauthor = false;
			}
		}

		if($updateauthor) {
			$db->query("UPDATE {$tablepre}members SET extcredits$creditstrans=extcredits$creditstrans+$thread[netprice] WHERE uid='$thread[authorid]'");
		}

		$db->query("UPDATE {$tablepre}members SET extcredits$creditstrans=extcredits$creditstrans-$thread[price] WHERE uid='$discuz_uid'");
		$db->query("INSERT INTO {$tablepre}paymentlog (uid, tid, authorid, dateline, amount, netamount)
			VALUES ('$discuz_uid', '$tid', '$thread[authorid]', '$timestamp', '$thread[price]', '$thread[netprice]')");

		showmessage('thread_pay_succeed', "viewthread.php?tid=$tid");

	}

} elseif($action == 'viewpayments') {

	$discuz_action = 82;

	$loglist = array();
	$query = $db->query("SELECT p.*, m.username FROM {$tablepre}paymentlog p
		LEFT JOIN {$tablepre}members m USING (uid)
		WHERE tid='$tid' ORDER BY dateline");
	while($log = $db->fetch_array($query)) {
		$log['dateline'] = gmdate("$dateformat $timeformat", $log['dateline'] + $timeoffset * 3600);
		$loglist[] = $log;
	}

	include template('pay_view');

} elseif($action == 'report') {

	if(!$reportpost) {
		showmessage('thread_report_disabled');
	}

	if(!$discuz_uid) {
		showmessage('not_loggedin', NULL, 'HALTED');
	}

	if(!$thread || !is_numeric($pid)) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	$discuz_action = 123;

	$floodctrl = $floodctrl * 3;
	if($timestamp - $lastpost < $floodctrl) {
		showmessage('thread_report_flood_ctrl');
	}

	if(!submitcheck('reportsubmit')) {

		include template('reportpost');

	} else {

		$posturl = "{$boardurl}viewthread.php?tid=$tid".($page || $pid ? "&page=$page#pid$pid" : NULL);

		$uids = 0;
		$adminids = '';
		$reportto = array();

		if(is_array($to) && count($to)) {

			if(isset($to[3])) {
				$query = $db->query("SELECT uid FROM {$tablepre}moderators WHERE fid='$fid'");
				while($member = $db->fetch_array($query)) {
					$uids .= ','.$member['uid'];
				}
			}

			if(!$uids || ($reportpost >= 2 && $to[2])) {
				$adminids .= ',2';
			}

			if($reportpost == 3 && $to[1]) {
				$adminids .= ',1';
			}

			if($adminids) {
				$query = $db->query("SELECT uid FROM {$tablepre}members WHERE adminid IN (".substr($adminids, 1).")");
				if(!$db->num_rows($query)) {
					$query = $db->query("SELECT uid FROM {$tablepre}members WHERE adminid='1'");
				}
				while($member = $db->fetch_array($query)) {
					$uids .= ','.$member['uid'];
				}
			}

			$query = $db->query("SELECT uid, ignorepm FROM {$tablepre}memberfields WHERE uid IN ($uids)");
			while($member = $db->fetch_array($query)) {
				if(!preg_match("/(^{ALL}$|(,|^)\s*".preg_quote($discuz_user, '/')."\s*(,|$))/i", $member['ignorepm'])) {
					if(!in_array($member['uid'], $reportto)) {
						$reportto[] = $member['uid'];
					}
				}
			}

			if($reportto) {
				$reason = stripslashes($reason);
				sendpm(implode(',', $reportto), 'reportpost_subject', 'reportpost_message');
			}

			$db->query("UPDATE {$tablepre}members SET lastpost='$timestamp' WHERE uid='$discuz_uid'");

			showmessage('thread_report_succeed', "viewthread.php?tid=$tid");

		} else {

			showmessage('thread_report_invalid');

		}

	}

} elseif($action == 'blog') {

	if(!$discuz_uid || (!$thread['blog'] && (!$allowuseblog || !$forum['allowblog']))) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	}

	if($thread['authorid'] != $discuz_uid) {
		$query = $db->query("SELECT adminid FROM {$tablepre}members WHERE uid='$thread[authorid]'");
		$thread['adminid'] = $db->result($query, 0);
		if(!$forum['ismoderator'] || (in_array($thread['adminid'], array(1, 2, 3)) && $adminid > $thread['adminid'])) {
			showmessage('blog_add_illegal');
		}
	}

	if(!submitcheck('blogsubmit')) {

		include template('blog_addremove');

	} else {

		$blog = $thread['blog'] ? 0 : 1;
		$db->query("UPDATE {$tablepre}threads SET blog='$blog' WHERE tid='$tid'", 'UNBUFFERED');

		if($forum['ismoderator'] && $thread['authorid'] != $discuz_uid && $blog != $thread['blog']) {
			$reason = '';
			require_once DISCUZ_ROOT.'./include/misc.func.php';
			modlog($thread, ($thread['blog'] ? 'RBL' : 'ABL'));
		}

		showmessage('blog_add_succeed', "viewthread.php?tid=$tid");

	}

} elseif($action == 'viewthreadmod' && $tid) {

	$loglist = array();
	$query = $db->query("SELECT * FROM {$tablepre}threadsmod WHERE tid='$tid' ORDER BY dateline DESC");
	while($log = $db->fetch_array($query)) {
		$log['dateline'] = gmdate("$dateformat $timeformat", $log['dateline'] + $timeoffset * 3600);
		$log['expiration'] = !empty($log['expiration']) ? gmdate("$dateformat", $log['expiration'] + $timeoffset * 3600) : '';
		$log['status'] = empty($log['status']) ? 'style="text-decoration: line-through" disabled' : '';
		$loglist[] = $log;
	}

	if(empty($loglist)) {
		showmessage('threadmod_nonexistence');
	} else {
		include_once language('modactions');
	}

	include template('viewthread_mod');
}

?>