<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: memcp.php,v $
	$Revision: 1.7.2.2 $
	$Date: 2006/03/23 06:05:28 $
*/

require_once './include/common.inc.php';

$discuz_action = 7;
$avatarextarray = array('gif', 'jpg', 'png');

if(!$discuz_uid) {
	showmessage('not_loggedin', NULL, 'HALTED');
}

if(!isset($action)) {

	$validating = array();
	if($regverify == 2 && $groupid == 8) {
		$query = $db->query("SELECT * FROM {$tablepre}validating WHERE uid='$discuz_uid'");
		if($validating = $db->fetch_array($query)) {
			$validating['moddate'] = $validating['moddate'] ? gmdate("$dateformat $timeformat", $validating['moddate'] + $timeoffset * 3600) : 0;
			$validating['adminenc'] = rawurlencode($validating['admin']);
		}
	}

	if($allowavatar || $avatarshowstatus || $allownickname) {
		$query = $db->query("SELECT mf.nickname, mf.avatar, mf.avatarwidth, mf.avatarheight, m.avatarshowid, m.gender
			FROM {$tablepre}memberfields mf, {$tablepre}members m WHERE m.uid='$discuz_uid' AND mf.uid=m.uid");
		$member = $db->fetch_array($query);
	} else {
		$member = array('nickname' => '', 'avatar' => '', 'avatarshowid' => 0);
	}

	$avatarshow = $avatarshowstatus ? $avatar = avatarshow($member['avatarshowid'], $member['gender']) : '';
	$avatar = $avatarshowstatus != 2 && $member['avatar'] ? "<img src=\"$member[avatar]\" width=\"$member[avatarwidth]\" height=\"$member[avatarheight]\" border=\"0\">" : '';

	$buddyonline = $buddyoffline = array();
	$query = $db->query("SELECT b.buddyid AS uid, b.description, m.username, s.username AS onlineuser
				FROM {$tablepre}buddys b
				LEFT JOIN {$tablepre}members m ON m.uid=b.buddyid
				LEFT JOIN {$tablepre}sessions s ON s.uid=m.uid AND s.invisible='0'
				WHERE b.uid='$discuz_uid'");
	while($buddy = $db->fetch_array($query)) {
		$buddyuser = array('uid' => $buddy['uid'], 'username' => ($buddy['username'] ? $buddy['username'] : 'User was Deleted'), 'description' => $buddy['description']);
		$buddy['onlineuser'] ? $buddyonline[] = $buddyuser : $buddyoffline[] = $buddyuser;
	}

	$msgexists = 0;
	$msglist = array();
	$query = $db->query("SELECT * FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox' ORDER BY dateline DESC LIMIT 0, 5");
	while($message = $db->fetch_array($query)) {
		$msgexists = 1;
		$message['dateline'] = gmdate("$dateformat $timeformat", $message['dateline'] + $timeoffset * 3600);
		$message['subject'] = $message['new'] ? "<b>$message[subject]</b>" : $message['subject'];

		$msglist[] = $message;
	}

	$subsexists = 0;
	$subslist = array();
	$query = $db->query("SELECT t.tid, t.fid, t.subject, t.replies, t.lastpost, t.lastposter, f.name
		FROM {$tablepre}subscriptions s, {$tablepre}threads t, {$tablepre}forums f
		WHERE t.tid=s.tid AND t.displayorder>='0' AND f.fid=t.fid AND s.uid='$discuz_uid' ORDER BY t.lastpost DESC LIMIT 5");

	while($subs = $db->fetch_array($query)) {
		$subsexists = 1;
		$subs['lastposterenc'] = rawurlencode($subs['lastposter']);
		$subs['lastpost'] = gmdate("$dateformat $timeformat", $subs['lastpost'] + $timeoffset * 3600);

		$subslist[] = $subs;
	}

	include template('memcp_home');

} elseif($action == 'profile') {

	require_once DISCUZ_ROOT.'./forumdata/cache/cache_profilefields.php';

	$query = $db->query("SELECT * FROM {$tablepre}members m
		LEFT JOIN {$tablepre}memberfields mf ON mf.uid=m.uid
		WHERE m.uid='$discuz_uid'");
	$member = $db->fetch_array($query);

	//get secure code checking status (pos. -5)
	$seccodecheck = substr(sprintf('%05b', $seccodestatus), -5, 1);

	if(!submitcheck('editsubmit', 0, $seccodecheck)) {

		$enctype = $allowavatar == 3 ? 'enctype="multipart/form-data"' : '';

		$invisiblechecked = $member['invisible'] ? 'checked' : '';
		$emailchecked = $member['showemail'] ? 'checked' : '';
		$newschecked = $member['newsletter'] ? 'checked' : '';
		$gendercheck = array($member['gender'] => 'checked');
		$tppchecked = array($member['tpp'] => 'selected="selected"');
		$pppchecked = array($member['ppp'] => 'selected="selected"');
		$toselect = array(strval((float)$member['timeoffset']) => 'selected="selected"');
		$pscheck = array(intval($member['pmsound']) => 'checked');

		$styleselect = '';
		$query = $db->query("SELECT styleid, name FROM {$tablepre}styles WHERE available='1'");
		while($style = $db->fetch_array($query)) {
			$styleselect .= "<option value=\"$style[styleid]\" ".
				($style['styleid'] == $member['styleid'] ? 'selected="selected"' : NULL).
				">$style[name]</option>\n";
		}

		$bday = explode('-', $member['bday']);
		$bday[0] = $bday[0] == '0000' ? '' : $bday[0];
		$month = array(intval($bday[1]) => "selected=\"selected\"");

		$dayselect = '';
		for($num = 1; $num <= 31; $num++) {
			$dayselect .= "<option value=\"$num\" ".($bday[2] == $num ? 'selected="selected"' : '').">$num</option>\n";
		}

		$avatarshow = avatarshow($member['avatarshowid'], $member['gender']);
		if(substr(trim($member['avatar']), 0, 14) == 'customavatars/' && !file_exists(DISCUZ_ROOT.'./'.$member['avatar'])) {
			$db->query("UPDATE {$tablepre}memberfields SET avatar='', avatarwidth='0', avatarheight='0' WHERE uid='$discuz_uid'");
			$member['avatar'] = '';
		}

		$member['dateformat'] = str_replace('n', 'mm', $member['dateformat']);
		$member['dateformat'] = str_replace('j', 'dd', $member['dateformat']);
		$member['dateformat'] = str_replace('y', 'yy', $member['dateformat']);
		$member['dateformat'] = str_replace('Y', 'yyyy', $member['dateformat']);

		$tfcheck = array($member['timeformat'] => 'checked');
		$dfcheck = $member['dateformat'] ? array(1 => 'checked') : array(0 => 'checked');

		if($seccodecheck) {
			$seccode = random(4, 1);
		}

		include template('memcp_profile');

	} else {

		require_once DISCUZ_ROOT.'./include/discuzcode.func.php';
		include_once DISCUZ_ROOT.'./forumdata/cache/cache_bbcodes.php';

		$newpasswdadd = '';
		$secquesnew = $questionidnew == -1 ? $discuz_secques : quescrypt($questionidnew, $answernew);
		if($newpassword || $secquesnew != $discuz_secques) {
			if(md5($oldpassword) != $discuz_pw) {
				showmessage('profile_passwd_wrong', NULL, 'HALTED');
			}
			if($newpassword) {
				if($newpassword != addslashes($newpassword)) {
					showmessage('profile_passwd_illegal');
				} elseif($newpassword != $newpassword2) {
					showmessage('profile_passwd_notmatch');
				}
				$newpasswdadd = ", password='".md5($newpassword)."'";
			}
		}

		if(($adminid == 1 || $adminid == 2 || $adminid == 3) && !$secquesnew && $forcesecques) {
			showmessage('profile_admin_security_invalid');
		}

		$fieldadd = '';
		foreach(array_merge($_DCACHE['fields_required'], $_DCACHE['fields_optional']) as $field) {
			$field_key = 'field_'.$field['fieldid'];
			$field_val = trim(${'field_'.$field['fieldid'].'new'});
			if($field['required'] && $field_val == '' && !($field['unchangeable'] && $member[$field_key])) {
				showmessage('profile_required_info_invalid');
			} elseif($field['selective'] && $field_val != '' && !isset($field['choices'][$field_val])) {
				showmessage('undefined_action', NULL, 'HALTED');
			} elseif(!$field['unchangeable'] || !$member[$field_key]) {
				$fieldadd .= ", $field_key='".dhtmlspecialchars($field_val)."'";
			}
		}

		$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censoruser = trim($censoruser)), '/')).')$/i';
		if($censoruser && (@preg_match($censorexp, $nicknamenew) || @preg_match($censorexp, $cstatusnew))) {
			showmessage('profile_nickname_cstatus_illegal');
		}

		if(!isemail($emailnew = $passport_status ? $member['email'] : $emailnew)) {
			showmessage('profile_email_illegal');
		}

		if($alipaynew && !isemail($alipaynew)) {
			showmessage('profile_alipay_illegal');
		}

		if($maxsigsize) {
			if(strlen($signaturenew) > $maxsigsize) {
				showmessage('profile_sig_toolang');
			}
		} else {
			$signaturenew = '';
		}

		$avataradd = '';
		if($allowavatar == 2 || $allowavatar == 3) {
			if($allowavatar == 3) {
				if(disuploadedfile($_FILES['customavatar']['tmp_name']) && $_FILES['customavatar']['tmp_name'] != 'none' && $_FILES['customavatar']['tmp_name'] && trim($_FILES['customavatar']['name'])) {
					$_FILES['customavatar']['name'] = daddslashes($_FILES['customavatar']['name']);
					$avatarext = strtolower(fileext($_FILES['customavatar']['name']));
					if(!in_array($avatarext, $avatarextarray)) {
						showmessage('profile_avatar_invalid');
					}
					$avatarnew = 'customavatars/'.$discuz_uid.'.'.$avatarext;
					$avatartarget = DISCUZ_ROOT.'./'.$avatarnew;
					if(!@copy($_FILES['customavatar']['tmp_name'], $avatartarget)) {
						@move_uploaded_file($_FILES['customavatar']['tmp_name'], $avatartarget);
					}
					$avatarimagesize = @getimagesize($avatartarget);

					if(!$avatarimagesize || ($maxavatarsize && @filesize($avatartarget) > $maxavatarsize)) {
						@unlink($avatartarget);
						showmessage($avatarimagesize ? 'profile_avatar_toobig' : 'profile_avatar_invalid');
					}
					foreach($avatarextarray as $ext) {
						if($ext != $avatarext) {
							@unlink(DISCUZ_ROOT.'./customavatars/'.$discuz_uid.'.'.$ext);
						}
					}
				}
			}
			$avatarnew = dhtmlspecialchars(trim($avatarnew));
			$avatarext = strtolower(fileext($avatarnew));

			if($avatarnew) {
				if(!preg_match("/^((customavatars\/\d+\.[a-z]+)|(images\/avatars\/.+?)|(http:\/\/.+?))$/i", $avatarnew)
					|| !in_array($avatarext, $avatarextarray)) {
					showmessage('profile_avatar_invalid');
				}
				if($avatarwidthnew == '*' || $avatarheightnew == '*') {
					$avatarwidthnew = $avatarheightnew = round(2 * $maxavatarpixel / 3);
					if(!@list($avatarwidthnew, $avatarheightnew) = $avatarimagesize ? $avatarimagesize : getimagesize($avatarnew)) {
						showmessage('profile_avatar_size_invalid');
					}
				}
				$maxsize = max($avatarwidthnew, $avatarheightnew);
				if($maxsize > $maxavatarpixel) {
					$avatarwidthnew = $avatarwidthnew * $maxavatarpixel / $maxsize;
					$avatarheightnew = $avatarheightnew * $maxavatarpixel / $maxsize;
				}
			}
			$avataradd = ", avatar='$avatarnew', avatarwidth='$avatarwidthnew', avatarheight='$avatarheightnew'";
		}

		$emailnew = dhtmlspecialchars($emailnew);

		$icqnew = preg_match ("/^([0-9]+)$/", $icqnew) && strlen($icqnew) >= 5 && strlen($icqnew) <= 12 ? $icqnew : '';
		$qqnew = preg_match ("/^([0-9]+)$/", $qqnew) && strlen($qqnew) >= 5 && strlen($qqnew) <= 12 ? $qqnew : '';
		$bdaynew = ($month && $day) ? (empty($year) ? '0000' : $year)."-$month-$day" : '';

		$yahoonew = dhtmlspecialchars($yahoonew);
		$msnnew = dhtmlspecialchars($msnnew);
		$taobaonew = dhtmlspecialchars($taobaonew);
		$alipaynew = dhtmlspecialchars($alipaynew);

		$bdaynew = dhtmlspecialchars($bdaynew);

		$signaturenew = censor($signaturenew);
		$sigstatusnew = $signaturenew ? 1 : 0;

		$bionew = censor(dhtmlspecialchars($bionew));
		$sitenew = dhtmlspecialchars(trim(preg_match("/^https?:\/\/.+/i", $sitenew) ? $sitenew : ($sitenew ? 'http://'.$sitenew : '')));

		$tppnew = in_array($tppnew, array(10, 20, 30)) ? $tppnew : 0;
		$pppnew = in_array($pppnew, array(5, 10, 15)) ? $pppnew : 0;

		if($dateformatnew) {
			$dateformatnew = str_replace('mm', 'n', $cdateformatnew);
			$dateformatnew = str_replace('dd', 'j', $dateformatnew);
			$dateformatnew = str_replace('yyyy', 'Y', $dateformatnew);
			$dateformatnew = str_replace('yy', 'y', $dateformatnew);
		} else {
			$dateformatnew = '';
		}

		$invisiblenew = $allowinvisible && $invisiblenew ? 1 : 0;
		$locationnew = cutstr(censor(dhtmlspecialchars($locationnew)), 30);
		$nicknamenew = $allownickname ? cutstr(censor(dhtmlspecialchars($nicknamenew)), 30) : '';

		$cstatusadd = $allowcstatus ? ', customstatus=\''.cutstr(censor(dhtmlspecialchars($cstatusnew)), 30).'\'' : '';

		$authstradd1 = $authstradd2 = '';
		if($regverify == 1 && $adminid == 0 && (($grouptype == 'member' && $adminid == 0) || $groupid == 8)) {
			$query = $db->query("SELECT email FROM {$tablepre}members WHERE uid='$discuz_uid'");
			if($emailnew != $db->result($query, 0)) {
				if(!$doublee) {
					$query = $db->query("SELECT uid FROM {$tablepre}members WHERE email='$emailnew' LIMIT 1");
					if($db->result($query, 0)) {
						showmessage('profile_email_duplicate');
					}
				}

				$idstring = random(6);
				$groupid = 8;

				require_once DISCUZ_ROOT.'./forumdata/cache/usergroup_8.php';

				$authstradd1 = ", groupid='8'";
				$authstradd2 = ", authstr='$timestamp\t2\t$idstring'";
				sendmail($emailnew, 'email_verify_subject', 'email_verify_message');
			}
		}

		$sightmlnew = addslashes(discuzcode(stripslashes($signaturenew), 1, 0, 0, 0, ($allowsigbbcode ? ($allowcusbbcode ? 2 : 1) : 0), $allowsigimgcode, 0));

		$db->query("UPDATE {$tablepre}members SET secques='$secquesnew', gender='$gendernew', email='$emailnew', styleid='$styleidnew', bday='$bdaynew',
			showemail='$showemailnew', timeoffset='$timeoffsetnew', tpp='$tppnew', ppp='$pppnew', newsletter='$newsletternew', invisible='$invisiblenew',
			timeformat='$timeformatnew', dateformat='$dateformatnew', pmsound='$pmsoundnew', styleid='$styleidnew', sigstatus='$sigstatusnew' $newpasswdadd $authstradd1
			WHERE uid='$discuz_uid'");

		$query = $db->query("SELECT uid FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
		if(!$db->num_rows($query)) {
			$db->query("REPLACE INTO {$tablepre}memberfields (uid) VALUES ('$discuz_uid')");
		}

		$db->query("UPDATE {$tablepre}memberfields SET nickname='$nicknamenew', site='$sitenew', location='$locationnew', bio='$bionew', signature='$signaturenew', sightml='$sightmlnew',
			icq='$icqnew', qq='$qqnew', yahoo='$yahoonew', msn='$msnnew', taobao='$taobaonew', alipay='$alipaynew' $avataradd $cstatusadd $fieldadd $authstradd2 WHERE uid='$discuz_uid'");

		$styleid = $styleidnew;

		if(!empty($authstradd1) && !empty($authstradd2)) {
			showmessage('profile_email_verify');
		} else {
			showmessage('profile_succeed', 'memcp.php');
		}
	}

} elseif($action == 'credits') {

	$exchcredits = array();
	foreach($extcredits as $id => $credit) {
		if($credit['ratio']) {
			$exchcredits[$id] = $credit;
		}
	}

	$exchangestatus = count($exchcredits) >= 2 ? 1 : 0;
	$transferstatus = isset($extcredits[$creditstrans]) && $allowtransfer;

	$taxpercent = sprintf('%1.2f', $creditstax * 100).'%';

	if($operation == 'transfer' && $transferstatus) {

		if(!submitcheck('creditssubmit')) {

			include template('memcp_credits');

		} else {

			$amount = intval($amount);

			if(md5($password) != $discuz_pw) {
				showmessage('credits_password_invalid');
			} elseif($amount <= 0) {
				showmessage('credits_transaction_amount_invalid');
			} elseif(${'extcredits'.$creditstrans} - $amount < ($minbalance = $transfermincredits)) {
				showmessage('credits_balance_insufficient');
			} elseif(!($netamount = floor($amount * (1 - $creditstax)))) {
				showmessage('credits_net_amount_iszero');
			}

			$query = $db->query("SELECT uid, username FROM {$tablepre}members WHERE username='$to'");
			if(!$member = $db->fetch_array($query)) {
				showmessage('credits_transfer_send_nonexistence');
			} elseif($member['uid'] == $discuz_uid) {
				showmessage('credits_transfer_self');
			}

			$db->query("UPDATE {$tablepre}members SET extcredits$creditstrans=extcredits$creditstrans-'$amount' WHERE uid='$discuz_uid'");
			$db->query("UPDATE {$tablepre}members SET extcredits$creditstrans=extcredits$creditstrans+'$netamount' WHERE uid='$member[uid]'");
			$db->query("INSERT INTO {$tablepre}creditslog (uid, fromto, sendcredits, receivecredits, send, receive, dateline, operation)
				VALUES ('$discuz_uid', '".addslashes($member['username'])."', '$creditstrans', '$creditstrans', '$amount', '0', '$timestamp', 'TFR'),
				('$member[uid]', '$discuz_user', '$creditstrans', '$creditstrans', '0', '$netamount', '$timestamp', 'RCV')");

			if(!empty($transfermessage)) {
				$transfermessage = stripslashes($transfermessage);
				$transfertime = gmdate($GLOBALS['_DCACHE']['settings']['dateformat'].' '.$GLOBALS['_DCACHE']['settings']['timeformat'], $timestamp + $timeoffset * 3600);
				sendpm($member['uid'], 'transfer_subject', 'transfer_message');
			}

			showmessage('credits_transaction_succeed', 'memcp.php?action=credits&operation=creditslog');

		}

	} elseif($operation == 'exchange' && $exchangestatus) {

		if(!submitcheck('creditssubmit')) {

			include template('memcp_credits');

		} elseif($extcredits[$fromcredits]['ratio'] && $extcredits[$tocredits]['ratio']) {

			$amount = intval($amount);

			if(md5($password) != $discuz_pw) {
				showmessage('credits_password_invalid');
			} elseif($fromcredits == $tocredits) {
				showmessage('credits_exchange_invalid');
			} elseif($amount <= 0) {
				showmessage('credits_transaction_amount_invalid');
			} elseif(${'extcredits'.$fromcredits} - $amount < ($minbalance = $exchangemincredits)) {
				showmessage('credits_balance_insufficient');
			} elseif(!($netamount = floor($amount * $extcredits[$fromcredits]['ratio'] * (1 - $creditstax) / $extcredits[$tocredits]['ratio']))) {
				showmessage('credits_net_amount_iszero');
			}

			$db->query("UPDATE {$tablepre}members SET extcredits$fromcredits=extcredits$fromcredits-'$amount', extcredits$tocredits=extcredits$tocredits+'$netamount' WHERE uid='$discuz_uid'");
			$db->query("INSERT INTO {$tablepre}creditslog (uid, fromto, sendcredits, receivecredits, send, receive, dateline, operation)
				VALUES ('$discuz_uid', '$discuz_user', '$fromcredits', '$tocredits', '$amount', '$netamount', '$timestamp', 'EXC')");

			showmessage('credits_transaction_succeed', 'memcp.php?action=credits&operation=creditslog');

		}

	} elseif($operation == 'addfunds' && $ec_ratio) {

		if(!submitcheck('creditssubmit')) {

			include template('memcp_credits');

		} else {

			$amount = intval($amount);
			if(!$amount || ($ec_mincredits && $amount < $ec_mincredits) || ($ec_maxcredits && $amount > $ec_maxcredits)) {
				showmessage('credits_addfunds_amount_invalid');
			}

			$query = $db->query("SELECT COUNT(*) FROM {$tablepre}orders WHERE uid='$discuz_uid' AND submitdate>='$timestamp'-180 LIMIT 1");
			if($db->result($query, 0)) {
				showmessage('credits_addfunds_ctrl');
			}

			if($ec_maxcreditspermonth) {
				$query = $db->query("SELECT SUM(amount) FROM {$tablepre}orders WHERE uid='$discuz_uid' AND submitdate>='$timestamp'-2592000 AND status IN (2, 3)");
				if(($db->result($query, 0)) + $amount > $ec_maxcreditspermonth) {
					showmessage('credits_addfunds_toomuch');
				}
			}

			$price = ceil($amount / $ec_ratio * 100) / 100;
			$orderid = gmdate('YmdHis', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600).random(18);

			$query = $db->query("SELECT orderid FROM {$tablepre}orders WHERE orderid='$orderid'");
			if($db->num_rows($query)) {
				showmessage('credits_addfunds_order_invalid');
			}

			$db->query("INSERT INTO {$tablepre}orders (orderid, status, uid, amount, price, submitdate)
				VALUES ('$orderid', '1', '$discuz_uid', '$amount', '$price', '$timestamp')");

			showmessage('credits_addfunds_succeed', payment($price, $orderid));

		}

	} elseif($operation == 'paymentlog') {

		$page = empty($page) || !ispage($page) ? 1 : $page;
		$start_limit = ($page - 1) * $tpp;

		$query = $db->query("SELECT COUNT(*) FROM {$tablepre}paymentlog WHERE uid='$discuz_uid'");
		$multipage = multi($db->result($query, 0), $tpp, $page, "memcp.php?action=credits&operation=paymentlog");

		$loglist = array();
		$query = $db->query("SELECT p.*, f.fid, f.name, t.subject, t.author, t.dateline AS tdateline FROM {$tablepre}paymentlog p
			LEFT JOIN {$tablepre}threads t ON t.tid=p.tid
			LEFT JOIN {$tablepre}forums f ON f.fid=t.fid
			WHERE p.uid='$discuz_uid' ORDER BY p.dateline DESC
			LIMIT $start_limit, $tpp");
		while($log = $db->fetch_array($query)) {
			$log['authorenc'] = rawurlencode($log['authorenc']);
			$log['dateline'] = gmdate("$dateformat $timeformat", $log['dateline'] + $timeoffset * 3600);
			$log['tdateline'] = gmdate("$dateformat $timeformat", $log['tdateline'] + $timeoffset * 3600);
			$loglist[] = $log;
		}

		include template('memcp_credits');

	} elseif($operation == 'incomelog') {

		$page = empty($page) || !ispage($page) ? 1 : $page;
		$start_limit = ($page - 1) * $tpp;

		$query = $db->query("SELECT COUNT(*) FROM {$tablepre}paymentlog WHERE authorid='$discuz_uid'");
		$multipage = multi($db->result($query, 0), $tpp, $page, "memcp.php?action=credits&operation=incomelog");

		$loglist = array();
		$query = $db->query("SELECT p.*, m.username, f.fid, f.name, t.subject, t.dateline AS tdateline FROM {$tablepre}paymentlog p
			LEFT JOIN {$tablepre}threads t ON t.tid=p.tid
			LEFT JOIN {$tablepre}forums f ON f.fid=t.fid
			LEFT JOIN {$tablepre}members m ON m.uid=p.uid
			WHERE p.authorid='$discuz_uid' ORDER BY p.dateline DESC
			LIMIT $start_limit, $tpp");
		while($log = $db->fetch_array($query)) {
			$log['dateline'] = gmdate("$dateformat $timeformat", $log['dateline'] + $timeoffset * 3600);
			$log['tdateline'] = gmdate("$dateformat $timeformat", $log['tdateline'] + $timeoffset * 3600);
			$loglist[] = $log;
		}

		include template('memcp_credits');

	} else {

		$operation = 'creditslog';

		$page = empty($page) || !ispage($page) ? 1 : $page;
		$start_limit = ($page - 1) * $tpp;

		$query = $db->query("SELECT COUNT(*) FROM {$tablepre}creditslog WHERE uid='$discuz_uid'");
		$multipage = multi($db->result($query, 0), $tpp, $page, "memcp.php?action=credits&operation=creditslog");

		$loglist = array();
		$query = $db->query("SELECT * FROM {$tablepre}creditslog WHERE uid='$discuz_uid' ORDER BY dateline DESC LIMIT $start_limit, $tpp");
		while($log = $db->fetch_array($query)) {
			$log['fromtoenc'] = rawurlencode($log['fromto']);
			$log['dateline'] = gmdate("$dateformat $timeformat", $log['dateline'] + $timeoffset * 3600);
			$loglist[] = $log;
		}

		include template('memcp_credits');

	}

} elseif($action == 'usergroups') {

	if(!$allowmultigroups) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	}

	$switchmaingroup = $grouppublic || $grouptype == 'member' ? 1 : 0;

	if(empty($type)) {

		$query = $db->query("SELECT groupterms FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
		$groupterms = unserialize($db->result($query, 0));

		$grouplist = array();
		$extgroupidarray = explode("\t", $extgroupids);

		$query = $db->query("SELECT groupid, grouptitle, type, system, allowmultigroups FROM {$tablepre}usergroups WHERE (type='special' AND system<>'private' AND radminid='0') OR (type='member' AND '$credits'>=creditshigher AND '$credits'<creditslower) OR groupid IN ('$groupid'".($extgroupids ? ', '.str_replace("\t", ',', $extgroupids) : '').") ORDER BY type, system");
		while($group = $db->fetch_array($query)) {
			if(in_array($group['groupid'], $extgroupidarray) && ($group['groupid'] == $groupid || ($group['type'] != 'member' && $group['system'] == 'private'))) {
				$group['grouptitle'] = '<b><i>'.$group['grouptitle'].'</i></b>';
			} elseif(!$group['allowmultigroups']) {
				$group['grouptitle'] = '<u>'.$group['grouptitle'].'</u>';
			}
			$group['mainselected'] = $group['groupid'] == $groupid ? 'checked' : '';
			$group['maindisabled'] = $switchmaingroup && (($group['system'] != 'private' && ($group['system'] == "0\t0" || $group['groupid'] == $groupid || in_array($group['groupid'], $extgroupidarray))) || $group['type'] == 'member') ? '' : 'disabled';
			$group['dailyprice'] = $group['minspan'] = 0;

			if($group['system'] != 'private') {
				list($group['dailyprice'], $group['minspan']) = explode("\t", $group['system']);
			}

			if($group['groupid'] == $groupid && !empty($groupterms['main'])) {
				$group['expiry'] = gmdate($dateformat, $groupterms['main']['time'] + $timeoffset * 3600);
			} elseif(isset($groupterms['ext'][$group['groupid']])) {
				$group['expiry'] = gmdate($dateformat, $groupterms['ext'][$group['groupid']] + $timeoffset * 3600);
			} else {
				$group['expiry'] = 'N/A';
			}

			$grouplist[$group['groupid']] = $group;
		}

		include template('memcp_usergroups');

	} else {

		if($type == 'main' && submitcheck('groupsubmit') && $switchmaingroup) {

			$query = $db->query("SELECT groupid, type, system, grouptitle FROM {$tablepre}usergroups WHERE groupid='$groupidnew' AND (".($extgroupids ? 'groupid IN ('.str_replace("\t", ',', $extgroupids).') OR ' : '')."(type='special' AND system='0\t0' AND radminid='0') OR (type='member' AND '$credits'>=creditshigher AND '$credits'<creditslower))");
			if(!$group = $db->fetch_array($query)) {
				showmessage('undefined_action', NULL, 'HALTED');
			}

			$extgroupidsnew = $groupid;
			foreach(explode("\t", $extgroupids) as $extgroupid) {
				if($extgroupid && $extgroupid != $groupidnew) {
					$extgroupidsnew .= "\t".$extgroupid;
				}
			}
			$adminidnew = in_array($adminid, array(1, 2, 3)) ? $adminid : ($group['type'] == 'special' ? -1 : 0);

			$db->query("UPDATE {$tablepre}members SET groupid='$groupidnew', adminid='$adminidnew', extgroupids='$extgroupidsnew' WHERE uid='$discuz_uid'");
			showmessage('usergroups_update_succeed', 'memcp.php?action=usergroups');

		} elseif($type == 'extended') {

			$query = $db->query("SELECT groupid, type, system, grouptitle FROM {$tablepre}usergroups WHERE groupid='$edit' AND (".($extgroupids ? 'groupid IN ('.str_replace("\t", ',', $extgroupids).') OR ' : '')."(type='special' AND system<>'private' AND radminid='0'))");
			if(!$group = $db->fetch_array($query)) {
				showmessage('undefined_action', NULL, 'HALTED');
			}

			$join = !in_array($group['groupid'], explode("\t", $extgroupids));
			$group['dailyprice'] = $group['minspan'] = 0;

			if($group['system'] != 'private') {
				list($group['dailyprice'], $group['minspan']) = explode("\t", $group['system']);
			}

			if(!isset($extcredits[$creditstrans])) {
				showmessage('credits_transaction_disabled');
			}

			if(!submitcheck('groupsubmit')) {

				$group['minamount'] = $group['dailyprice'] * $group['minspan'];

				include template('memcp_usergroups');

			} else {

				$query = $db->query("SELECT groupterms FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
				$groupterms = unserialize($db->result($query, 0));

				if($join) {

					$extgroupidsarray = array();
					foreach(array_unique(array_merge(explode("\t", $extgroupids), array($edit))) as $extgroupid) {
						if($extgroupid) {
							$extgroupidsarray[] = $extgroupid;
						}
					}
					$extgroupidsnew = implode("\t", $extgroupidsarray);

					if($group['dailyprice']) {
						if(($days = intval($days)) < $group['minspan']) {
							showmessage('usergroups_span_invalid');
						}

						if(${'extcredits'.$creditstrans} - ($amount = $days * $group['dailyprice']) < ($minbalance = 0)) {
							showmessage('credits_balance_insufficient');
						}

						$groupexpirynew = $timestamp + $days * 86400;
						$groupterms['ext'][$edit] = $groupexpirynew;

						$groupexpirynew = groupexpiry($groupterms);

						$db->query("UPDATE {$tablepre}members SET groupexpiry='$groupexpirynew', extgroupids='$extgroupidsnew', extcredits$creditstrans=extcredits$creditstrans-'$amount' WHERE uid='$discuz_uid'");
						$db->query("UPDATE {$tablepre}memberfields SET groupterms='".addslashes(serialize($groupterms))."' WHERE uid='$discuz_uid'");
						$db->query("INSERT INTO {$tablepre}creditslog (uid, fromto, sendcredits, receivecredits, send, receive, dateline, operation)
							VALUES ('$discuz_uid', '$discuz_user', '$creditstrans', '0', '$amount', '0', '$timestamp', 'UGP')");
					} else {
						$db->query("UPDATE {$tablepre}members SET extgroupids='$extgroupidsnew' WHERE uid='$discuz_uid'");
					}

					showmessage('usergroups_join_succeed', 'memcp.php?action=usergroups');

				} else {

					if($edit != $groupid) {
						if(isset($groupterms['ext'][$edit])) {
							unset($groupterms['ext'][$edit]);
						}
						$groupexpirynew = groupexpiry($groupterms);
						$db->query("UPDATE {$tablepre}memberfields SET groupterms='".addslashes(serialize($groupterms))."' WHERE uid='$discuz_uid'");
					} else {
						$groupexpirynew = 'groupexpiry';
					}

					$extgroupidsarray = array();
					foreach(explode("\t", $extgroupids) as $extgroupid) {
						if($extgroupid && $extgroupid != $edit) {
							$extgroupidsarray[] = $extgroupid;
						}
					}
					$extgroupidsnew = implode("\t", array_unique($extgroupidsarray));
					$db->query("UPDATE {$tablepre}members SET groupexpiry=$groupexpirynew, extgroupids='$extgroupidsnew' WHERE uid='$discuz_uid'");

					showmessage('usergroups_exit_succeed', 'memcp.php?action=usergroups');

				}

			}

		} else {

			showmessage('undefined_action', NULL, 'HALTED');

		}

	}

} elseif($action == 'buddylist') {

	if(!submitcheck('buddysubmit', 1)) {

		$query = $db->query("SELECT b.*, m.username FROM {$tablepre}buddys b, {$tablepre}members m
			WHERE b.uid='$discuz_uid' AND m.uid=b.buddyid ORDER BY dateline DESC");
		while($buddy = $db->fetch_array($query)) {
			$buddy['dateline'] = gmdate("$dateformat $timeformat", $buddy['dateline'] + $timeoffset * 3600);
			$buddylist[] = $buddy;
		}

		include template('memcp_misc');

	} else {

		$buddyarray = array();
		$query = $db->query("SELECT * FROM {$tablepre}buddys WHERE uid='$discuz_uid'");
		while($buddy = $db->fetch_array($query)) {
			$buddyarray[$buddy['buddyid']] = $buddy;
		}

		if(!empty($delete) && is_array($delete)) {
			$db->query("DELETE FROM {$tablepre}buddys WHERE uid='$discuz_uid' AND buddyid IN ('".implode('\',\'', $delete)."')");
		}

		if(is_array($descriptionnew)) {
			foreach($descriptionnew as $buddyid => $desc) {
				if(($desc = cutstr(dhtmlspecialchars($desc), 255)) != addslashes($buddyarray[$buddyid]['description'])) {
					$db->query("UPDATE {$tablepre}buddys SET description='$desc' WHERE uid='$discuz_uid' AND buddyid='$buddyid'");
				}
			}
		}

		if($newbuddy || $newbuddyid) {
			if(!in_array($adminid, array(1, 2, 3))) {
				$query = $db->query("SELECT COUNT(*) FROM {$tablepre}buddys WHERE uid=$discuz_uid");
				if(($db->result($query, 0)) > 20) {
					showmessage('buddy_add_toomany');
				}
			}

			$query = $db->query("SELECT uid FROM {$tablepre}members WHERE ".(empty($newbuddyid) ? "username='$newbuddy'" : "uid='$newbuddyid'"));
			if($buddyid = $db->result($query, 0)) {
				if(isset($buddyarray[$buddyid])) {
					showmessage('buddy_add_invalid');
				}
				$db->query("INSERT INTO {$tablepre}buddys (uid, buddyid, dateline, description)
					VALUES ('$discuz_uid', '$buddyid', '$timestamp', '".cutstr(dhtmlspecialchars($newdescription), 255)."')");
			} else {
				showmessage('buddy_add_nonexistence');
			}
		}

		showmessage('buddy_update_succeed', 'memcp.php?action=buddylist');

	}

} elseif($action == 'favorites') {

	if(isset($favadd) && !submitcheck('favsubmit')) {

		$query = $db->query("SELECT tid FROM {$tablepre}favorites WHERE uid='$discuz_uid' AND tid='$favadd' LIMIT 1");
		if($db->result($query, 0)) {
			showmessage('favorite_exists');
		} else {
			$db->query("INSERT INTO {$tablepre}favorites (uid, tid)
				VALUES ('$discuz_uid', '$favadd')");
			showmessage('favorite_add_succeed', dreferer());
		}

	} elseif(empty($favadd)) {

		if(!submitcheck('favsubmit')) {

			$favlist = array();
			$query = $db->query("SELECT t.tid, t.fid, t.subject, t.replies, t.lastpost, t.lastposter, f.name
				FROM {$tablepre}favorites fav, {$tablepre}threads t, {$tablepre}forums f
				WHERE fav.tid=t.tid AND t.displayorder>='0' AND fav.uid='$discuz_uid' AND t.fid=f.fid ORDER BY t.lastpost DESC");

			while($fav = $db->fetch_array($query)) {
				$fav['lastposterenc'] = rawurlencode($fav['lastposter']);
				$fav['lastpost'] = gmdate("$dateformat $timeformat", $fav['lastpost'] + $timeoffset * 3600);
				$favlist[] = $fav;
			}

			include template('memcp_misc');

		} else {

			$ids = $comma = '';
			if(!empty($delete) && is_array($delete)) {
				foreach($delete as $deleteid) {
					$ids .= $comma.$deleteid;
					$comma = ', ';
				}
			}

			if($ids) {
				$db->query("DELETE FROM {$tablepre}favorites WHERE uid='$discuz_uid' AND tid IN ($ids)");
			}
			showmessage('favorite_update_succeed', dreferer());

		}

	}

} elseif($action == 'subscriptions') {

	if(isset($subadd) && !submitcheck('subsubmit')) {

		$query = $db->query("SELECT tid FROM {$tablepre}subscriptions WHERE tid='$subadd' AND uid='$discuz_uid' LIMIT 1");
		if($db->result($query, 0)) {
			showmessage('subscription_exists');
		} else {
			$db->query("INSERT INTO {$tablepre}subscriptions (uid, tid, lastpost, lastnotify)
				VALUES ('$discuz_uid', '$subadd', '$lastpost', '')");
			showmessage('subscription_add_succeed', dreferer());
		}

	} elseif(empty($subadd)) {

		if(!submitcheck('subsubmit')) {

			$subslist = array();
			$query = $db->query("SELECT t.tid, t.fid, t.subject, t.replies, t.lastpost, t.lastposter, f.name
				FROM {$tablepre}subscriptions s, {$tablepre}threads t, {$tablepre}forums f
				WHERE t.tid=s.tid AND t.displayorder>='0' AND f.fid=t.fid AND s.uid='$discuz_uid' ORDER BY t.lastpost DESC");

			while($subs = $db->fetch_array($query)) {
				$subs['lastposterenc'] = rawurlencode($subs['lastposter']);
				$subs['lastpost'] = gmdate("$dateformat $timeformat", $subs['lastpost'] + $timeoffset * 3600);
				$subslist[] = $subs;
			}

			include template('memcp_misc');

		} else {

			$ids = $comma = '';
			if(!empty($delete) && is_array($delete)) {
				foreach($delete as $deleteid) {
					$ids .= "$comma$deleteid";
					$comma = ", ";
				}
			}

			if($ids) {
				$db->query("DELETE FROM {$tablepre}subscriptions WHERE uid='$discuz_uid' AND tid IN ($ids)");
			}
			showmessage('subscription_update_succeed', dreferer());

		}

	}

} elseif($action == 'viewavatars') {

	if(!$allowavatar) {
		showmessage('undefined_action', NULL, 'HALTED');
	}

	if(!submitcheck('avasubmit', 1)) {

		$app = 16;
		$avatarsdir = DISCUZ_ROOT.'./images/avatars';
		$page = !ispage($page) ? 1 : $page;

		$query = $db->query("SELECT avatar FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
		$member = $db->fetch_array($query);

		$avatarlist = '';
		$avatars = array('');
		if(is_dir($avatarsdir)) {
			$adir = dir($avatarsdir);
			while($entry = $adir->read()) {
				if(in_array(strtolower(fileext($entry)), $avatarextarray) && is_file("$avatarsdir/$entry")) {
					$avatars[] = $entry;
				}
			}
			$adir->close();
		} else {
			showmessage('profile_avatardir_nonexistence');
		}

		sort($avatars, SORT_REGULAR);
		$num = count($avatars);

		$start = ($page - 1) * $app;
		$end = ($start + $app > $num) ? ($num) : ($start + $app - 1);

		$multipage = multi($num, $app, $page, 'memcp.php?action=viewavatars');
		for($i = $start; $i <= $end; $i += 4) {
			$avatarlist .= "<tr>\n";
			for($j = 0; $j < 4; $j++) {
				$avatarlist .= '<td class="'.($thisbg = isset($thisbg) && $thisbg == 'altbg1' ? 'altbg2' : 'altbg1').'" width="25%" align="center">';
				if($avatars[$i + $j] && ($i + $j)) {
					$avatarlist .= '<img src="images/avatars/'.$avatars[$i + $j].'"></td>';
				} else {
					$avatarlist .= '&nbsp;</td>';
				}
			}
			$avatarlist .= '</tr><tr>';
			for($j = 0; $j < 4; $j++) {
				$avatarlist .= '<td class="'.$thisbg.'" width="25%" align="center">';
				if($avatars[$i + $j] && ($i + $j)) {
					if(strpos($member['avatar'], $avatars[$i + $j])) {
						$checked = 'checked';
					} else {
						$checked = '';
					}
					$avatarlist .= '<input type="radio" value="images/avatars/'.$avatars[$i + $j].'" name="avatarnew" '.$checked.'>'.$avatars[$i + $j];
				} elseif($i + $j == 0) {
					if(!$member['avatar']) {
						$checked = 'checked';
					}
					$avatarlist .= '<input type="radio" value="" name="avatarnew" '.$checked.'><span class="bold">None</span>';
				} else {
					$avatarlist .= '&nbsp;</td>';
				}
				$thisbg = isset($thisbg) && $thisbg == 'altbg1' ? 'altbg2' : 'altbg1';
			}
			$avatarlist .= '</tr><tr><td colspan="4" class="singleborder"></td></tr>';
		}

		include template('memcp_misc');

	} else {

		@list($avatarwidthnew, $avatarheightnew) = getimagesize($avatarnew);
		$maxsize = max($avatarwidthnew, $avatarheightnew);
		if($maxsize > $maxavatarpixel) {
			$avatarwidthnew = $avatarwidthnew * $maxavatarpixel / $maxsize;
			$avatarheightnew = $avatarheightnew * $maxavatarpixel / $maxsize;
		}
		$db->query("UPDATE {$tablepre}memberfields SET avatar='".dhtmlspecialchars($avatarnew)."', avatarwidth='$avatarwidthnew', avatarheight='$avatarheightnew' WHERE uid='$discuz_uid'");
		showmessage('profile_avatar_succeed', 'memcp.php?action=profile');

	}

}

?>