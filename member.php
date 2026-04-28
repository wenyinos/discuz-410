<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: member.php,v $
	$Revision: 1.6 $
	$Date: 2006/02/28 05:38:32 $
*/

define('CURSCRIPT', 'member');

require_once './include/common.inc.php';

if($action == 'clearcookies') {

	foreach($_DCOOKIE as $key => $val) {
		dsetcookie($key, '', -86400 * 365);
	}

	header("Location: {$boardurl}index.php");

} elseif($action == 'online') {

	$discuz_action = 31;

	@include language('actions');

	$page = empty($page) || !ispage($page) ? 1 : $page;
	$start_limit = ($page - 1) * $memberperpage;

	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}sessions");
	$num = $db->result($query, 0);
	$multipage = multi($num, $memberperpage, $page, "member.php?action=online");

	$onlinelist = array();
	$query = $db->query("SELECT s.*, f.name, t.subject FROM {$tablepre}sessions s
		LEFT JOIN {$tablepre}forums f ON s.fid=f.fid
		LEFT JOIN {$tablepre}threads t ON s.tid=t.tid
		WHERE s.invisible='0'
		ORDER BY s.lastactivity DESC LIMIT $start_limit, $memberperpage");

	while($online = $db->fetch_array($query)) {
		$online['lastactivity'] = gmdate($timeformat, $online['lastactivity'] + $timeoffset * 3600);
		$online['action'] = $actioncode[$online['action']];
		$online['subject'] = $online['subject'] ? cutstr($online['subject'], 35) : NULL;
		$online['ip'] = $online['ip1'].'.'.$online['ip2'].'.'.$online['ip3'].'.'.$online['ip4'];

		$onlinelist[] = $online;
	}

	include template('whosonline');

} elseif($action == 'list') {

	$discuz_action = 41;

	if(!$memliststatus) {
		showmessage('member_list_disable', NULL, 'HALTED');
	}
	
	$order = empty($order) ? '' : $order;
	if($order == 'credits' || $order == 'gender') {
		$orderadd = "ORDER BY $order DESC";
	} elseif($order == 'username' || $order == 'regdate') {
		$orderadd = "ORDER BY $order";
	} else {
		$orderadd = "ORDER BY uid";
		$order = 'uid';
	}

	$admins = empty($admins) ? '' : 'yes';

	$page = empty($page) || !ispage($page) || ($membermaxpages && $page > $membermaxpages) ? 1 : $page;
	$start_limit = ($page - 1) * $memberperpage;

	$sql = $admins ? "WHERE groupid IN (1, 2, 3)" :
		(!empty($srchmem) ? " WHERE BINARY username LIKE '%".str_replace('_', '\_', $srchmem)."%' OR username='$srchmem'" : '');

	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}members $sql");
	$num = $db->result($query, 0);
	$multipage = multi($num, $memberperpage, $page, "member.php?action=list&srchmem=".rawurlencode($srchmem)."&order=$order&admins=$admins", $membermaxpages);

	$memberlist = array();

	$query = $db->query("SELECT m.uid, m.username, m.gender, m.email, m.regdate, m.lastvisit, m.posts, m.credits,
		m.showemail, mf.nickname, mf.site, mf.location FROM {$tablepre}members m
		LEFT JOIN {$tablepre}memberfields mf USING(uid) $sql $orderadd LIMIT $start_limit, $memberperpage");

	while($member = $db->fetch_array($query)) {
		$member['usernameenc'] = rawurlencode($member['username']);
		$member['regdate'] = gmdate($dateformat, $member['regdate'] + $timeoffset * 3600 );
		$member['site'] = str_replace('http://', '', $member['site']);
		$member['lastvisit'] = gmdate("$dateformat $timeformat", $member['lastvisit'] + ($timeoffset * 3600));
		$memberlist[] = $member;
	}

	include template('memberlist');

} elseif($action == 'credits') {

	if(empty($extcredits)) {
		showmessage('credits_disabled');
	}

	$policyarray = array();
	foreach($creditspolicy as $operation => $policy) {
		$policyarray[$operation] = $policy;
		if(in_array($operation, array('post', 'reply'))) {
			if($forum) {
				$policyarray['forum_'.$operation] = $forum[$operation.'credits'] ? $forum[$operation.'credits'] : $creditspolicy[$operation];
			} else {
				foreach($extcredits as $id => $credit) {
					$policyarray['forum_'.$operation][$id] = 'N/A';
				}
			}
		}
	}

	$creditsarray = array();
	for($i = 1; $i <= 8; $i++) {
		if(isset($extcredits[$i])) {
			foreach($policyarray as $operation => $policy) {
				$addcredits = in_array($operation, array('getattach', 'pm', 'search')) ? -$policy[$i] : $policy[$i];
				$creditsarray[$i][$operation] = empty($policy[$i]) ? 0 : (is_numeric($policy[$i]) ? '<b>'.($addcredits > 0 ? '+'.$addcredits : $addcredits).'</b> '.$extcredits[$i]['unit'] : $policy[$i]);
			}
		}
	}

	include template('credits');

} elseif($action == 'check') {

	$username = trim($username);

	if(strlen($username) > 15) {
		showmessage('profile_username_toolang');
	}

	$guestexp = '\xA1\xA1|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
	$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censoruser = trim($censoruser)), '/')).')$/i';
	if(preg_match("/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&]|$guestexp/is", $username) || ($censoruser && @preg_match($censorexp, $username))) {
		showmessage('profile_username_illegal');
	}

	$query = $db->query("SELECT uid FROM {$tablepre}members WHERE username='$username'");
	$username = dhtmlspecialchars(stripslashes($username));

	showmessage(($db->num_rows($query)) ? 'register_check_found' : 'register_check_notfound');

} elseif($action == 'markread') {

	if($discuz_user) {
		$db->query("UPDATE {$tablepre}members SET lastvisit='$timestamp' WHERE uid='$discuz_uid'");
	}

	showmessage('mark_read_succeed', 'index.php');

} elseif($action == 'regverify' && $regverify == 2 && $groupid == 8 && submitcheck('verifysubmit')) {

	$query = $db->query("SELECT uid FROM {$tablepre}validating WHERE uid='$discuz_uid' AND status='1'");
	if($db->num_rows($query)) {
		$db->query("UPDATE {$tablepre}validating SET submittimes=submittimes+1, submitdate='$timestamp', status='0', message='".dhtmlspecialchars($regmessagenew)."'
			WHERE uid='$discuz_uid'");
		showmessage('submit_verify_succeed', 'memcp.php');
	} else {
		showmessage('undefined_action', NULL, 'HALTED');
	}

} elseif($action == 'emailverify') {

	$query = $db->query("SELECT mf.authstr FROM {$tablepre}members m, {$tablepre}memberfields mf
		WHERE m.uid='$discuz_uid' AND mf.uid=m.uid AND m.groupid='8'");

	if(!$member = $db->fetch_array($query)) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	list($dateline, $type, $idstring) = explode("\t", $member['authstr']);
	if($type == 2 && $timestamp - $dateline < 86400) {
		showmessage('email_verify_invalid');
	}

	$idstring = $type == 2 && $idstring ? $idstring : random(6);
	$db->query("UPDATE {$tablepre}memberfields SET authstr='$timestamp\t2\t$idstring' WHERE uid='$discuz_uid'");

	sendmail($email, 'email_verify_subject', 'email_verify_message');
	showmessage('email_verify_succeed');

} elseif($action == 'activate' && $uid && $id) {

	$query = $db->query("SELECT m.uid, m.username, m.credits, mf.authstr FROM {$tablepre}members m, {$tablepre}memberfields mf
		WHERE m.uid='$uid' AND mf.uid=m.uid AND m.groupid='8'");

	$member = $db->fetch_array($query);

	list($dateline, $operation, $idstring) = explode("\t", $member['authstr']);

	if($operation == 2 && $idstring == $id) {
		$query = $db->query("SELECT groupid FROM {$tablepre}usergroups WHERE type='member' AND $member[credits]>=creditshigher AND $member[credits]<creditslower LIMIT 1");
		$db->query("UPDATE {$tablepre}members SET groupid='".$db->result($query, 0)."' WHERE uid='$member[uid]'");
		$db->query("UPDATE {$tablepre}memberfields SET authstr='' WHERE uid='$member[uid]'");
		showmessage('activate_succeed', 'index.php');
	} else {
		showmessage('activate_illegal', NULL, 'HALTED');
	}

} elseif($action == 'lostpasswd') {

	$discuz_action = 141;

	if(!submitcheck('lostpwsubmit')) {
		include template('lostpasswd');
	} else {
		$secques = quescrypt($questionid, $answer);
		$query = $db->query("SELECT uid, username, adminid, email FROM {$tablepre}members WHERE username='$username' AND secques='$secques' AND email='$email'");
		if(!$member = $db->fetch_array($query)) {
			showmessage('getpasswd_account_notmatch', NULL, 'HALTED');
		} elseif($member['adminid'] == 1 || $member['adminid'] == 2) {
			showmessage('getpasswd_account_invalid', NULL, 'HALTED');
		}

		$idstring = random(6);
		$db->query("UPDATE {$tablepre}memberfields SET authstr='$timestamp\t1\t$idstring' WHERE uid='$member[uid]'");

		sendmail($member['email'], 'get_passwd_subject', 'get_passwd_message');
		showmessage('getpasswd_send_succeed');
	}

} elseif($action == 'getpasswd' && $uid && $id) {

	$discuz_action = 141;

	$query = $db->query("SELECT m.username, mf.authstr FROM {$tablepre}members m, {$tablepre}memberfields mf
		WHERE m.uid='$uid' AND mf.uid=m.uid");

	$member = $db->fetch_array($query);

	list($dateline, $operation, $idstring) = explode("\t", $member['authstr']);

	if($dateline < $timestamp - 86400 * 3 || $operation != 1 || $idstring != $id) {
		showmessage('getpasswd_illegal', NULL, 'HALTED');
	}

	if(!submitcheck('getpwsubmit') || $newpasswd1 != $newpasswd2) {
		include template('getpasswd');
	} else {
		$password = md5($newpasswd1);

		$db->query("UPDATE {$tablepre}members SET password='$password' WHERE uid='$uid'");
		$db->query("UPDATE {$tablepre}memberfields SET authstr='' WHERE uid='$uid'");

		showmessage('getpasswd_succeed');
	}	

} elseif($action == 'groupexpiry') {

	if(!$discuz_uid) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	if(!$groupexpiry) {
		showmessage('group_expiry_disabled');
	}

	$query = $db->query("SELECT groupterms FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
	$groupterms = unserialize($db->result($query, 0));

	$expgrouparray = $expirylist = $termsarray = array();

	if(!empty($groupterms['ext']) && is_array($groupterms['ext'])) {
		$termsarray = $groupterms['ext'];
	}
	if(!empty($groupterms['main']['time']) && (empty($termsarray[$groupid]) || $termsarray[$groupid] > $groupterm['main']['time'])) {
		$termsarray[$groupid] = $groupterms['main']['time'];
	}

	foreach($termsarray as $expgroupid => $expiry) {
		if($expiry <= $timestamp) {
			$expgrouparray[] = $expgroupid;
		}
	}

	if(!empty($groupterms['ext'])) {
		foreach($groupterms['ext'] as $extgroupid => $time) {
			$expirylist[$extgroupid] = array('time' => gmdate($dateformat, $time + $timeoffset * 3600), 'type' => 'ext');
		}
	}

	if(!empty($groupterms['main'])) {
		$expirylist[$groupid] = array('time' => gmdate($dateformat, $groupterms['main']['time'] + $timeoffset * 3600), 'type' => 'main');
	}

	if($expirylist) {
		$query = $db->query("SELECT groupid, grouptitle FROM {$tablepre}usergroups WHERE groupid IN (".implode(',', array_keys($expirylist)).")");
		while($group = $db->fetch_array($query)) {
			$expirylist[$group['groupid']]['grouptitle'] = in_array($group['groupid'], $expgrouparray) ? '<s>'.$group['grouptitle'].'</s>' : $group['grouptitle'];
		}
	} else {
		$db->query("UPDATE {$tablepre}members SET groupexpiry='0' WHERE uid='$discuz_uid'");
	}

	if($expgrouparray) {

		$extgroupidarray = array();
		foreach(explode("\t", $extgroupids) as $extgroupid) {
			if(($extgroupid = intval($extgroupid)) && !in_array($extgroupid, $expgrouparray)) {
				$extgroupidarray[] = $extgroupid;
			}
		}

		$groupidnew = $groupid;
		$adminidnew = $adminid;
		foreach($expgrouparray as $expgroupid) {
			if($expgroupid == $groupid) {
				if(!empty($groupterms['main']['groupid'])) {
					$groupidnew = $groupterms['main']['groupid'];
					$adminidnew = $groupterms['main']['adminid'];
				} else {
					$query = $db->query("SELECT groupid FROM {$tablepre}usergroups WHERE type='member' AND '$credits'>=creditshigher AND '$credits'<creditslower LIMIT 1");
					$groupidnew = $db->result($query, 0);
					if(in_array($adminid, array(1, 2, 3))) {
						$query = $db->query("SELECT groupid FROM {$tablepre}usergroups WHERE groupid IN ('".implode('\',\'', $extgroupidarray)."') AND radminid='$adminid' LIMIT 1");
						$adminidnew = ($db->num_rows($query)) ? $adminid : 0;
					} else {
						$adminidnew = 0;
					}
				}
				unset($groupterms['main']);
			}
			unset($groupterms['ext'][$expgroupid]);
		}

		$groupexpirynew = groupexpiry($groupterms);
		$extgroupidsnew = implode("\t", $extgroupidarray);
		$grouptermsnew = addslashes(serialize($groupterms));

		$db->query("UPDATE {$tablepre}members SET adminid='$adminidnew', groupid='$groupidnew', extgroupids='$extgroupidsnew', groupexpiry='$groupexpirynew' WHERE uid='$discuz_uid'");
		$db->query("UPDATE {$tablepre}memberfields SET groupterms='$grouptermsnew' WHERE uid='$discuz_uid'");

	}

	include template('groupexpiry');

} else {

	showmessage('undefined_action', NULL, 'HALTED');

}

?>