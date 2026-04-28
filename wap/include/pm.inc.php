<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: pm.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:54 $
*/

if(!defined('IN_DISCUZ')) {
        exit('Access Denied');
}

$discuz_action = 197;

if(!$discuz_uid) {
	wapmsg('not_loggedin');
}

if(empty($do)) {

	$num_read = $num_unread = 0;
	$query = $db->query("SELECT COUNT(*) AS num, new FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox' GROUP BY new='0'");
	while($pm = $db->fetch_array($query)) {
		$pm['new'] ? $num_unread = $pm['num'] : $num_read = $pm['num'];
	}

	echo "<p><a href=\"index.php?action=pm&amp;do=list&amp;unread=yes\">$lang[pm_unread]($num_unread)</a><br />\n".
		"<a href=\"index.php?action=pm&amp;do=list\">$lang[pm_all](".($num_read + $num_unread).")</a><br />\n".
		"<a href=\"index.php?action=pm&amp;do=send\">$lang[pm_send]</a></p>\n";

} else {

	echo "<p align=\"center\"><a href=\"index.php?action=pm\">$lang[pm_home]</a><br /></p>\n";

	if($do == 'list') {

		$unreadadd = empty($unread) ? '' : 'AND new>\'0\'';
		$page = empty($page) || !ispage($page) ? 1 : $page;
		$start_limit = $number = ($page - 1) * $waptpp;

		$query = $db->query("SELECT COUNT(*) FROM {$tablepre}pms WHERE msgtoid='$discuz_uid' AND folder='inbox' $unreadadd");
		if(!($totalpms = $db->result($query, 0))) {
			wapmsg('pm_nonexistence');
		}

		$query = $db->query("SELECT pmid, new, msgfrom, subject, dateline FROM {$tablepre}pms
			WHERE msgtoid='$discuz_uid' AND folder='inbox' $unreadadd
			ORDER BY dateline DESC
			LIMIT $start_limit, $waptpp");
		while($pm = $db->fetch_array($query)) {
			echo "<p><a href=\"index.php?action=pm&amp;do=view&amp;pmid=$pm[pmid]\">#".++$number.' '.(empty($unread) && $pm['new'] ? "($lang[unread])" : '').cutstr($pm['subject'], 30)."</a><br />\n".
				"&nbsp; <small>".gmdate("$wapdateformat $timeformat", $pm['dateline'] + $timeoffset * 3600)."<br />\n".
				"&nbsp; $pm[msgfrom]</small></p>\n";
		}
		echo "<p><br />$lang[page]$page ".
			($start_limit + $waptpp < $totalpms ? "<a href=\"index.php?action=pm&amp;do=$do&amp;page=".($page + 1)."\">&gt;&gt;$lang[next_page]</a>" : $lang['end']).
			"</p>\n";

	} elseif($do == 'view') {

		$query = $db->query("SELECT * FROM {$tablepre}pms WHERE pmid='$pmid' AND msgtoid='$discuz_uid' AND folder='inbox'");
		if(!$pm = $db->fetch_array($query)) {
			wapmsg('pm_nonexistence');
		}

		echo "<p>$pm[subject]</p>\n".
			"<p><small>$pm[msgfrom]</small></p>\n".
			"<p><small>".gmdate("$wapdateformat $timeformat", $pm['dateline'] + $timeoffset * 3600)."</small></p>\n".
			"<p><small><br />".nl2br(dhtmlspecialchars(trim($pm['message'])))."<br /></small></p>\n".
			"<p align=\"center\"><a href=\"index.php?action=pm&amp;do=send&amp;pmid=$pmid\">$lang[reply]</a></p>\n".
			"<p align=\"center\"><a href=\"index.php?action=pm&amp;do=delete&amp;pmid=$pmid\">$lang[delete]</a></p>\n";
		$db->query("UPDATE {$tablepre}pms SET new='0' WHERE pmid='$pmid'");

	} elseif($do == 'send') {

		if(empty($msgto)) {

			if(!empty($pmid)) {
				$query = $db->query("SELECT msgfrom, subject FROM {$tablepre}pms WHERE pmid='$pmid' AND msgtoid='$discuz_uid' AND folder='inbox'");
				$pm = $db->fetch_array($query);
				$pm['subject'] = 'Re: '.$pm['subject'];
			} else {
				$pm = array('msgfrom' => '', 'subject' => '');
			}

			echo "<p>$lang[pm_to]:<input type=\"text\" name=\"msgto\" value=\"$pm[msgfrom]\" maxlength=\"15\" format=\"M*m\" /></p>\n".
				"<p>$lang[subject]:<input type=\"text\" name=\"subject\" value=\"$pm[subject]\" maxlength=\"70\" format=\"M*m\" /></p>\n".
				"<p>$lang[message]:<input type=\"text\" name=\"message\" value=\"\" format=\"M*m\" /></p>\n".
				"<p><anchor title=\"$lang[submit]\">$lang[submit]".
				"<go method=\"post\" href=\"index.php?action=pm&amp;do=send&amp;sid=$sid\">\n".
				"<postfield name=\"msgto\" value=\"$(msgto)\" />\n".
				"<postfield name=\"subject\" value=\"$(subject)\" />\n".
				"<postfield name=\"message\" value=\"$(message)\" />\n".
				"</go></anchor></p>\n";

		} else {

			$floodctrl = $floodctrl * 2;
			if($floodctrl && !$disablepostctrl && $timestamp - $lastpost < $floodctrl) {
				wapmsg('pm_flood_ctrl');
			}

			$query = $db->query("SELECT m.uid AS msgtoid, mf.ignorepm FROM {$tablepre}members m
				LEFT JOIN {$tablepre}memberfields mf USING (uid)
				WHERE username='$msgto'");
			if(!$member = $db->fetch_array($query)) {
				wapmsg('pm_send_nonexistence');
			}
			if(preg_match("/(^{ALL}$|(,|^)\s*".preg_quote($discuz_user, '/')."\s*(,|$))/i", $member['ignorepm'])) {
				wapmsg('pm_send_ignore');
			}
			if(empty($subject) || empty($message)) {
				wapmsg('pm_sm_isnull');
			}

			$subject = dhtmlspecialchars(cutstr(trim($subject), 75));
			$db->query("INSERT INTO {$tablepre}pms (msgfrom, msgfromid, msgtoid, folder, new, subject, dateline, message)
				VALUES('$discuz_user', '$discuz_uid', '$member[msgtoid]', 'inbox', '1', '$subject', '$timestamp', '$message')");
			$db->query("UPDATE {$tablepre}members SET newpm='1' WHERE uid='$member[msgtoid]'", 'UNBUFFERED');

			if($floodctrl) {
				$db->query("UPDATE {$tablepre}members SET lastpost='$timestamp' WHERE uid='$discuz_uid'");
			}

			wapmsg('pm_send_succeed');

		}

	} elseif($do == 'delete') {

		$db->query("DELETE FROM {$tablepre}pms WHERE pmid='$pmid' AND msgtoid='$discuz_uid' AND folder='inbox'");
		wapmsg('pm_delete_succeed');

	}

}

?>