<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: pm.php,v $
	$Revision: 1.7 $
	$Date: 2006/03/02 02:19:32 $
*/

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/discuzcode.func.php';

$discuz_action = 101;

if(empty($discuz_uid)) {
	showmessage('not_loggedin', NULL, 'HALTED');
} elseif($maxpmnum == 0) {
	showmessage('group_nopermission', NULL, 'NOPERM');
}

$query = $db->query("SELECT COUNT(*) FROM {$tablepre}pms WHERE msgfromid='$discuz_uid' AND folder='outbox'");
$pm_outbox = $db->result($query, 0);
$query = $db->query("SELECT COUNT(*) FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox'");
$pm_inbox = $db->result($query, 0);

$pm_total = $pm_outbox + $pm_inbox;

@$storage_percent = round((100 * $pm_total / $maxpmnum) + 1).'%';

if(empty($action)) {

	$page = empty($page) || !ispage($page) ? 1 : $page;
	$start_limit = ($page - 1) * $tpp;

	switch(isset($folder) ? $folder : 'inbox') {

		case 'outbox':
			$pmnum = $pm_outbox;
			$query = $db->query("SELECT p.*, m.username AS msgto FROM {$tablepre}pms p
				LEFT JOIN {$tablepre}members m ON m.uid=p.msgtoid
				WHERE p.msgfromid='$discuz_uid' AND p.folder='outbox'
				ORDER BY p.dateline DESC LIMIT $start_limit, $tpp");
			break;

		case 'track':
			$query = $db->query("SELECT COUNT(*) FROM {$tablepre}pms WHERE msgfromid='$discuz_uid' AND folder='inbox'");
			$pmnum = $db->result($query, 0);
			$query = $db->query("SELECT p.*, m.username AS msgto FROM {$tablepre}pms p
				LEFT JOIN {$tablepre}members m ON m.uid=p.msgtoid
				WHERE p.msgfromid='$discuz_uid' AND p.folder='inbox'
				ORDER BY p.dateline DESC LIMIT $start_limit, $tpp");
			break;

		default:
			$folder = 'inbox';
			$pmnum = $pm_inbox;
			$query = $db->query("SELECT * FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox' ORDER BY dateline DESC LIMIT $start_limit, $tpp");
	}

	$multipage = multi($pmnum, $tpp, $page, "pm.php?folder=$folder");

	$pmlist = array();
	while($pm = $db->fetch_array($query)) {
		$pm['dateline'] = gmdate("$dateformat $timeformat", $pm['dateline'] + $timeoffset * 3600);
		$pm['subject'] = $pm['new'] ? "<b>$pm[subject]</b>" : $pm['subject'];
		$pmlist[] = $pm;
	}

} elseif($action == 'view') {

	if($pm_total > $maxpmnum) {
		showmessage('pm_box_isfull', 'pm.php');
	}

	$codecount = 0;

	$query = $db->query("SELECT p.*, m.username AS msgto FROM {$tablepre}pms p
				LEFT JOIN {$tablepre}members m ON m.uid=p.msgtoid
				WHERE pmid='$pmid' AND (msgtoid='$discuz_uid' OR msgfromid='$discuz_uid')");
	if(!$pm = $db->fetch_array($query)) {
		showmessage('pm_nonexistence');
	}

	if($pm['new'] && !($pm['msgfromid'] == $discuz_uid && $pm['msgtoid'] != $discuz_uid && $pm['folder'] == 'inbox')) {
		$db->query("UPDATE {$tablepre}pms SET new='0' WHERE pmid='$pmid'");
	}

	$folder = $folder == 'track' ? $folder : $pm['folder'];

	$pm['dateline'] = gmdate("$dateformat $timeformat", $pm['dateline'] + $timeoffset * 3600);
	$pm['message'] = discuzcode($pm['message'], 0, 0);

} elseif($action == 'send') {

	if(!$adminid && $newbiespan && (!$lastpost || $timestamp - $lastpost < $newbiespan * 3600)) {
		$query = $db->query("SELECT regdate FROM {$tablepre}members WHERE uid='$discuz_uid'");
		if($timestamp - ($db->result($query, 0)) < $newbiespan * 3600) {
			showmessage('pm_newbie_span');
		}
	}

	if($pm_total > $maxpmnum) {
		showmessage('pm_box_isfull', 'pm.php');
	}

	checklowerlimit($creditspolicy['pm'], -1);

	$subject = dhtmlspecialchars(censor($subject));
	$message = trim(censor(parseurl($message)));

	//get secure code checking status (pos. -4)
	$seccodecheck = substr(sprintf('%05b', $seccodestatus), -4, 1);

	if(!submitcheck('pmsubmit', 0, $seccodecheck)) {

		$buddylist = array();
		$query = $db->query("SELECT b.buddyid, m.username AS buddyname FROM {$tablepre}buddys b
					LEFT JOIN {$tablepre}members m ON m.uid=b.buddyid
					WHERE b.uid='$discuz_uid'");
		while($buddy = $db->fetch_array($query)) {
			$buddylist[] = $buddy;
		}

		$subject = $message = '';

		if(isset($pmid)) {

			$query = $db->query("SELECT * FROM {$tablepre}pms WHERE pmid='$pmid' AND (msgtoid='$discuz_uid' OR msgfromid='$discuz_uid')");
			$pm = $db->fetch_array($query);

			$pm['subject'] = $message = preg_replace("/^(Re:|Fw:)\s*/", "", $pm['subject']);
			$username = $pm['msgfrom'];

			if($do == 'reply') {
				$subject = "Re: $pm[subject]";
				$message = '[quote]'.dhtmlspecialchars(trim(preg_replace("/(\[quote])(.*)(\[\/quote])/siU", '', $pm['message']))).'[/quote]'."\n";
				$touser = $pm['msgfrom'];
			} elseif($do == 'forward') {
				$pm['dateline'] = gmdate($_DCACHE['settings']['dateformat'].' '.$_DCACHE['settings']['timeformat'], $pm['dateline'] + $timeoffset * 3600);
				$subject = "Fw: $pm[subject]";
				$message = '[quote]'.dhtmlspecialchars($pm['message']).'[/quote]'."\n";
				$touser = '';
			}

		} elseif(isset($uid)) {

			$query = $db->query("SELECT username FROM {$tablepre}members WHERE uid='$uid'");
			$touser = dhtmlspecialchars($db->result($query, 0));

		} else {

			$touser = dhtmlspecialchars($touser);

		}

		if($seccodecheck) {
			$seccode = random(4, 1);
		}

	} else {

		$floodctrl = $floodctrl * 2;
		if($floodctrl && !$disablepostctrl && $timestamp - $lastpost < $floodctrl) {
			showmessage('pm_flood_ctrl');
		}

		if(empty($msgto)) {
			$msgto = array_merge($msgtobuddys, array());
		} else {
			$msgtoid = 0;
			$query = $db->query("SELECT uid, username FROM {$tablepre}members WHERE username='$msgto'");
			while($member = $db->fetch_array($query)) {
				if(!strcasecmp(addslashes($member['username']), $msgto)) {
					$msgtoid = $member['uid'];
					break;
				}
			}

			if(!$msgtoid) {
				showmessage('pm_send_nonexistence');
			}

			if(is_array($msgtobuddys)) {
				$msgto = array_merge($msgtobuddys, array($msgtoid));
			} else {
				$msgto = array($msgtoid);
			}
		}

		$subject = cutstr(trim($subject), 75);
		$msgto_count = count($msgto);
		$maxpmsend = ceil($maxpmnum / 10);
		if($msgto_count > $maxpmsend) {
			showmessage('pm_send_toomany');
		}
		if(!$msgto_count || !$subject) {
			showmessage('pm_send_invalid');
		}

		$uids = $comma = '';
		foreach($msgto as $uid) {
			$uids .= $comma.$uid;
			$comma = ',';
		}

		$ignorenum = 0;
		$query = $db->query("SELECT m.username, mf.ignorepm FROM {$tablepre}members m
			LEFT JOIN {$tablepre}memberfields mf USING(uid)
			WHERE m.uid IN ($uids)");

		$msgto_count = $db->num_rows($query);
		while($member = $db->fetch_array($query)) {
			if(preg_match("/(^{ALL}$|(,|^)\s*".preg_quote($discuz_user, '/')."\s*(,|$))/i", $member['ignorepm'])) {
				showmessage('pm_send_ignore');
			}
		}

		updatecredits($discuz_uid, $creditspolicy['pm'], -1);

		foreach($msgto as $uid) {
			$db->query("INSERT INTO {$tablepre}pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
				VALUES('$discuz_user', '$discuz_uid', '$uid', 'inbox', '1', '$subject', '$timestamp', '$message')");
		}
		$db->query("UPDATE {$tablepre}members SET newpm='1' WHERE uid IN ($uids)", 'UNBUFFERED');

		if($floodctrl) {
			$db->query("UPDATE {$tablepre}members SET lastpost='$timestamp' WHERE uid='$discuz_uid'");
		}

		if($saveoutbox) {
			$db->query("INSERT INTO {$tablepre}pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
				VALUES('$discuz_user', '$discuz_uid', '$msgto[0]', 'outbox', '1', '$subject', '$timestamp', '$message')");
		}

		showmessage('pm_send_succeed', 'pm.php');

	}

} elseif($action == 'search') {

	$cachelife_text = 3600;		// Life span for cache of text searching

	if(!$allowsearch) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	}

	if(!submitcheck('searchsubmit', 1) && empty($page)) {

		$ftdisabled = $allowsearch != 2 ? 'disabled' : '';

	} else {

		$orderby = isset($orderby) && $orderby == 'msgfrom' ? 'msgfrom' : 'dateline';
		$ascdesc = isset($ascdesc) && $ascdesc == 'asc' ? 'asc' : 'desc';

		if(isset($searchid)) {

			$page = !ispage($page) ? 1 : $page;
			$start_limit = ($page - 1) * $tpp;

			$query = $db->query("SELECT searchstring, keywords, pms, pmids FROM {$tablepre}pmsearchindex WHERE searchid='$searchid'");
			if(!$index = $db->fetch_array($query)) {
				showmessage('search_id_invalid');
			}
			$index['keywords'] = rawurlencode($index['keywords']);
			$index['folder'] = preg_replace("/^([a-z]+)\|.*/", "\\1", $index['searchstring']);

			$pmlist = array();
			$query = $db->query("SELECT p.*, m.username AS msgto FROM {$tablepre}pms p
				LEFT JOIN {$tablepre}members m ON p.msgtoid=m.uid
				WHERE p.pmid IN ($index[pmids])
				ORDER BY p.$orderby $ascdesc LIMIT $start_limit, $tpp");

			while($pm = $db->fetch_array($query)) {
				$pm['dateline'] = gmdate("$dateformat $timeformat", $pm['dateline'] + $timeoffset * 3600);
				$pm['subject'] = $pm['new'] ? "<b>$pm[subject]</b>" : $pm['subject'];
				$pmlist[] = $pm;
			}

			$multipage = multi($index['pms'], $tpp, $page, "pm.php?action=search&searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

		} else {

			checklowerlimit($creditspolicy['search'], -1);

			$srchtxt = isset($srchtxt) ? trim($srchtxt) : '';
			$srchuname = isset($srchuname) ? trim($srchuname) : '';
			$srchfolder = in_array($srchfolder, array('inbox', 'outbox', 'track')) ? $srchfolder : 'inbox';

			if($allowsearch == 2 && $srchtype == 'fulltext') {
				periodscheck('searchbanperiods');
			} else {
				$srchtype = 'title';
			}

			if(empty($srchread) && empty($srchunread)) {
				$srchread = $srchunread = 1;
			}

			$searchstring = $srchfolder.'|'.$srchtype.'|'.addslashes($srchtxt).'|'.trim($srchuname).'|'.intval($srchread).'|'.intval($srchunread).'|'.intval($srchfrom).'|'.intval($before);
			$searchindex = array('id' => 0, 'dateline' => '0');

			$query = $db->query("SELECT searchid, dateline,
				('$searchctrl'<>'0' AND uid='$discuz_uid' AND $timestamp-dateline<$searchctrl) AS flood,
				(searchstring='$searchstring' AND expiration>'$timestamp') AS indexvalid
				FROM {$tablepre}pmsearchindex
				WHERE ('$searchctrl'<>'0' AND uid='$discuz_uid' AND $timestamp- dateline <$searchctrl) OR (searchstring='$searchstring' AND expiration>'$timestamp')
				ORDER BY flood");

			while($index = $db->fetch_array($query)) {
				if($index['indexvalid'] && $index['dateline'] > $searchindex['dateline']) {
					$searchindex = array('id' => $index['searchid'], 'dateline' => $index['dateline']);
					break;
				} elseif($index['flood']) {
					showmessage('search_ctrl');
				}
			}

			if($searchindex['id']) {

				$searchid = $searchindex['id'];

			} else {

				if(!$srchtxt && !$srchuname) {
					showmessage('search_invalid');
				}

				if($maxspm) {
					$query = $db->query("SELECT COUNT(*) FROM {$tablepre}pmsearchindex WHERE dateline>'$timestamp'-60");
					if(($db->result($query, 0)) >= $maxspm) {
						showmessage('search_toomany');
					}
				}

				$sqlsrch = '';

				if($srchfolder == 'outbox') {
					$sqlsrch .= "msgfromid='$discuz_uid' AND folder='outbox'";
				} elseif($srchfolder == 'track') {
					$sqlsrch .= "msgfromid='$discuz_uid' AND folder='inbox'";
				} else {
					$sqlsrch .= "msgtoid='$discuz_uid' AND folder='inbox'";
				}

				if($srchread == 1 && empty($srchunread)) {
					$sqlsrch .= " AND new='0'";
				}
				if($srchunread == 1 && empty($srchread)) {
					$sqlsrch .= " AND new>'0'";
				}

				$srchuid = '';
				if($srchuname) {
					$comma = '';
					$srchuname = str_replace('*', '%', addcslashes($srchuname, '%_'));
					$query = $db->query("SELECT uid FROM {$tablepre}members WHERE username LIKE '".str_replace('_', '\_', $srchuname)."' LIMIT 50");
					while($member = $db->fetch_array($query)) {
						$srchuid .= "$comma'$member[uid]'";
						$comma = ', ';
					}
					if(!$srchuid) {
						$sqlsrch .= ' AND 0';
					}
				}

				if($srchtxt) {
					if(preg_match("(AND|\+|&|\s)", $srchtxt) && !preg_match("(OR|\|)", $srchtxt)) {
						$andor = ' AND ';
						$sqltxtsrch = '1';
						$srchtxt = preg_replace("/( AND |&| )/is", "+", $srchtxt);
					} else {
						$andor = ' OR ';
						$sqltxtsrch = '0';
						$srchtxt = preg_replace("/( OR |\|)/is", "+", $srchtxt);
					}
					$srchtxt = str_replace('*', '%', addcslashes($srchtxt, '%_'));
					foreach(explode('+', $srchtxt) as $text) {
						$text = trim($text);
						if($text) {
							$sqltxtsrch .= $andor;
							$sqltxtsrch .= $srchtype == 'fulltext' ? "(message LIKE '%".str_replace('_', '\_', $text)."%' OR subject LIKE '%$text%')" : "subject LIKE '%$text%'";
						}
					}
					$sqlsrch .= " AND ($sqltxtsrch)";
				}

				if($srchuid) {
					$sqlsrch .= ' AND '.($srchfolder == 'inbox' ? 'msgfromid' : 'msgtoid')." IN ($srchuid)";
				}

				if(!empty($srchfrom)) {
					$searchfrom = ($before ? '<=' : '>=').($timestamp - $srchfrom);
					$sqlsrch .= " AND dateline$searchfrom";
				}
				$keywords = str_replace('%', '+', $srchtxt).(trim($srchuname) ? '+'.str_replace('%', '+', $srchuname) : '');
				$expiration = $timestamp + $cachelife_text;

				$pmids = 0;
				$query = $db->query("SELECT pmid FROM {$tablepre}pms WHERE $sqlsrch ORDER BY pmid DESC LIMIT $maxsearchresults");
				while($pm = $db->fetch_array($query)) {
					$pmids .= ','.$pm['pmid'];
				}
				$pms = $db->num_rows($query);
				$db->free_result($query);

				$db->query("INSERT INTO {$tablepre}pmsearchindex (keywords, searchstring, uid, dateline, expiration, pms, pmids)
						VALUES ('$keywords', '$searchstring', '$discuz_uid', '$timestamp', '$expiration', '$pms', '$pmids')");
				$searchid = $db->insert_id();

				updatecredits($discuz_uid, $creditspolicy['search'], -1);

			}

			showmessage('search_redirect', "pm.php?action=search&searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

		}

	}

} elseif($action == 'delete' && in_array($folder, array('inbox', 'outbox', 'track'))) {

	$msg_field = $folder == 'inbox' ? 'msgtoid' : 'msgfromid';
	$folderadd = $folder == 'track' ? "AND folder='inbox' AND new>'0'" : "AND folder='$folder'";

	if(!$pmid) {
		if(is_array($delete)) {
			$pmids = 0;
			foreach($delete as $pmid) {
				$pmids .= ','.intval($pmid);
			}
			$db->query("DELETE FROM {$tablepre}pms WHERE $msg_field='$discuz_uid' AND pmid IN ($pmids) $folderadd");
		}
	} else {
		$db->query("DELETE FROM {$tablepre}pms WHERE $msg_field='$discuz_uid' AND pmid='$pmid' $folderadd");
	}

	showmessage('pm_delete_succeed', "pm.php?folder=$folder");

} elseif($action == 'markunread' && !empty($pmid)) {

	$db->query("UPDATE {$tablepre}pms SET new='2' WHERE pmid='$pmid' AND msgtoid='$discuz_uid'");
	showmessage('pm_mark_unread_succeed', "pm.php?folder=$folder");

} elseif($action == 'archive' && (!empty($pmid) || submitcheck('archivesubmit'))) {

	$sql = $limitadd = '';

	if(empty($pmid)) {
		$days = intval($days);
		$amount = intval($amount);
		$sql .= $folder == 'inbox' ? " AND p.folder='inbox' AND p.msgtoid='$discuz_uid'" : " AND p.folder='outbox' AND p.msgfromid='$discuz_uid'";
		$sql .= $days > 0 ? ' AND p.dateline'.($newerolder == 'older' ? '<' : '>').($timestamp - intval($days) * 86400) : '';
		$limitadd = 'LIMIT '.(($amount > 0 AND $amount <= $maxpmnum ) ? $amount : $maxpmnum);
	} else {
		$sql = "AND p.pmid='$pmid' AND ((p.folder='inbox' AND p.msgtoid='$discuz_uid') OR (p.folder='outbox' AND p.msgfromid='$discuz_uid'))";
	}

	$pmids = 0;
	$pmlist = array();
	$query = $db->query("SELECT p.pmid, p.folder, p.msgfrom, p.msgfromid, m.username AS msgto, p.msgtoid, p.subject, p.dateline, p.message
		FROM {$tablepre}pms p LEFT JOIN {$tablepre}members m ON m.uid=p.msgtoid
		WHERE 1 $sql ORDER BY p.folder, p.dateline DESC $limitadd");

	while($pm = $db->fetch_array($query)) {
		$pmids .= ','.$pm['pmid'];
		$pm['dateline'] = gmdate("$dateformat $timeformat", $pm['dateline'] + $timeoffset * 3600);
		$pm['message'] = discuzcode($pm['message'], 0, 0);
		$pmlist[] = $pm;
	}

	if(!$pmlist) {
		showmessage('pm_nonexistence');
	} elseif($delete) {
		$db->query("DELETE FROM {$tablepre}pms WHERE pmid IN ($pmids)");
	}

	ob_end_clean();
	header('Content-Encoding: none');
	header('Content-Type: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'application/octetstream' : 'application/octet-stream'));
	header('Content-Disposition: attachment; filename="PM_'.$discuz_userss.'_'.gmdate('ymd_Hi', $timestamp + $timeoffset * 3600).'.htm"');
	header('Pragma: no-cache');
	header('Expires: 0');

	include template('pm_archive_html');
	dexit();

} elseif($action == 'ignore') {

	if(!submitcheck('ignoresubmit')) {
		$query = $db->query("SELECT ignorepm FROM {$tablepre}memberfields WHERE uid='$discuz_uid'");
		$ignorepm = $db->result($query, 0);
	} else {
		$db->query("UPDATE {$tablepre}memberfields SET ignorepm='$ignorelist' WHERE uid='$discuz_uid'");
		showmessage('pm_ignore_succeed', 'pm.php');
	}

}

include template('pm');

?>