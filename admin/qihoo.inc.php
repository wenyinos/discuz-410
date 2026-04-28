<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: qihoo.inc.php,v $
	$Revision: 1.10.2.2 $
	$Date: 2006/03/06 03:29:52 $
*/

if(!defined('IN_DISCUZ') || !isset($PHP_SELF) || !preg_match("/[\/\\\\]admincp\.php$/", $PHP_SELF)) {
        exit('Access Denied');
}

cpheader();

if($action == 'qihoo_config') {

	if(!submitcheck('qihoosubmit')) {

		$settings = $checks = array();
		$query = $db->query("SELECT variable, value FROM {$tablepre}settings WHERE variable IN ('qihoo_status', 'qihoo_searchbox', 'qihoo_summary', 'qihoo_keywords', 'qihoo_relatedthreads', 'qihoo_validity', 'qihoo_maxtopics', 'qihoo_adminemail')");
		while($setting = $db->fetch_array($query)) {
			$settings[$setting['variable']] = $setting['value'];
		}
		$checkstatus = array($settings['qihoo_status'] => 'checked');
		$settings['qihoo_searchbox'] = sprintf('%03b', $settings['qihoo_searchbox']);
		for($i = 1; $i <= 3; $i++) {
			$checks[$i] = $settings['qihoo_searchbox'][3 - $i] ? 'checked' : '';
		}

?>
<form method="post" name="settings" action="admincp.php?action=qihoo_config">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['qihoo_tips']?>
</td></tr></table>
<?

		showtype('qihoo');
		showsetting('qihoo_status', '', '', '<input type="radio" name="settingsnew[qihoo_status]" value="0" '.$checkstatus[0].'> '.$lang['qihoo_status_disable'].'<br><input type="radio" name="settingsnew[qihoo_status]" value="1" '.$checkstatus[1].'> '.$lang['qihoo_status_enable'].'<br><input type="radio" name="settingsnew[qihoo_status]" value="2" '.$checkstatus[2].'> '.$lang['qihoo_status_enable_default']);
		showsetting('qihoo_adminemail', 'settingsnew[qihoo_adminemail]', $settings['qihoo_adminemail'], 'text');
		showsetting('qihoo_searchbox', '', '', '<input type="checkbox" name="settingsnew[qihoo_searchbox][1]" value="1" '.$checks[1].'> '.$lang['qihoo_searchbox_index'].'<br><input type="checkbox" name="settingsnew[qihoo_searchbox][2]" value="1" '.$checks[2].'> '.$lang['qihoo_searchbox_forumdisplay'].'<br><input type="checkbox" name="settingsnew[qihoo_searchbox][3]" value="1" '.$checks[3].'> '.$lang['qihoo_searchbox_viewthread']);
		showsetting('qihoo_summary', 'settingsnew[qihoo_summary]', $settings['qihoo_summary'], 'radio');
		showsetting('qihoo_relatedthreads', 'settingsnew[qihoo_relatedthreads]', $settings['qihoo_relatedthreads'], 'text');
		showsetting('qihoo_validity', 'settingsnew[qihoo_validity]', $settings['qihoo_validity'], 'text');
		showsetting('qihoo_maxtopics', 'settingsnew[qihoo_maxtopics]', $settings['qihoo_maxtopics'], 'text');
		showsetting('qihoo_keywords', 'settingsnew[qihoo_keywords]', $settings['qihoo_keywords'], 'textarea');
		showtype('', 'bottom');

		echo '<br><center><input type="submit" name="qihoosubmit" value="'.$lang['submit'].'"></form>';

	} else {

		$settingsnew['qihoo_searchbox'] = bindec(intval($settingsnew['qihoo_searchbox'][3]).intval($settingsnew['qihoo_searchbox'][2]).intval($settingsnew['qihoo_searchbox'][1]));
		$settingsnew['qihoo_validity'] = $settingsnew['qihoo_validity'] < 1 ? 1 : intval($settingsnew['qihoo_validity']);

		if($settingsnew['qihoo_status'] && $settingsnew['qihoo_adminemail']) {

			if(!isemail($settingsnew['qihoo_adminemail'])) {
				cpmsg('qihoo_adminemail_invalid');
			}

			$key = md5(site().'qihoo_discuz'.gmdate("Ymd", $timestamp));
			@fopen('http://search.qihoo.com/corp/discuz.html?site='.site().'&key='.$key.'&email='.$settingsnew['qihoo_adminemail'].'', 'r');
		}

		if(is_array($settingsnew)) {
			foreach($settingsnew as $variable => $value) {
				$value = $variable != 'qihoo_keywords' && $variable != 'qihoo_adminemail' ? intval($value) : $value;
				$db->query("UPDATE {$tablepre}settings SET value='$value' WHERE variable='$variable'");
			}
		}

		updatecache('settings');
		cpmsg('qihoo_succeed');

	}

} elseif($action == 'qihoo_topics') {

	if(!submitcheck('topicsubmit')) {
		$topics = '';
		$query = $db->query("SELECT value FROM {$tablepre}settings WHERE variable='qihoo_topics'");
		$setting = $db->fetch_array($query);
		if(is_array($setting['qihoo_topics'] = unserialize($setting['value']))) {
			foreach($setting['qihoo_topics'] as $key => $value) {
				$checkstype = array($value['stype'] => 'selected="selected"');
				$checkrelate = array($value['relate'] => 'selected="selected"');

				$topics .= "<tr align=\"center\">\n".
					"<td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[$key]\" value=\"".$value['topic']."\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"20\" name=\"settingsnew[qihoo_topics][$key][topic]\" value=\"$value[topic]\"></td>\n".
					"<td bgcolor=\"".ALTBG1."\"><input type=\"text\" size=\"30\" name=\"settingsnew[qihoo_topics][$key][keyword]\" value=\"$value[keyword]\"></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><input type=\"text\" size=\"10\" name=\"settingsnew[qihoo_topics][$key][length]\" value=\"$value[length]\"></td>\n".
					"<td bgcolor=\"".ALTBG1."\"><select name=\"settingsnew[qihoo_topics][$key][stype]\"><option value=\"0\" $checkstype[0]>$lang[qihoo_topics_type_fulltext]</option><option value=\"title\" $checkstype[title]>$lang[qihoo_topics_type_title]</option></select></td>\n".
					"<td bgcolor=\"".ALTBG2."\"><select name=\"settingsnew[qihoo_topics][$key][relate]\"><option value=\"score\" $checkrelate[score]>$lang[qihoo_topics_orderby_relation]</option><option value=\"pdate\" $checkrelate[pdate]>$lang[qihoo_topics_orderby_dateline]</option><option value=\"rdate\" $checkrelate[rdate]>$lang[qihoo_topics_orderby_lastpost]</option></select></tr>\n".
					"<td bgcolor=\"".ALTBG1."\"><a href=\"###\" onClick=\"window.open('topic.php?topic='+findobj('settingsnew[qihoo_topics][$key][topic]').value+'&keyword='+findobj('settingsnew[qihoo_topics][$key][keyword]').value+'&stype='+findobj('settingsnew[qihoo_topics][$key][stype]').value+'&length='+findobj('settingsnew[qihoo_topics][$key][length]').value+'&relate='+findobj('settingsnew[qihoo_topics][$key][relate]').value+'');\">[$lang[preview]]</a></tr>\n";
			}
		}

?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['qihoo_topics_tips']?>
</td></tr></table>

<br><form method="post"	action="admincp.php?action=qihoo_topics">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr class="header" align="center">
<td><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['qihoo_topics_name']?></td><td><?=$lang['qihoo_topics_keywords']?></td><td><?=$lang['qihoo_topics_length']?></td><td><?=$lang['qihoo_topics_type']?></td><td><?=$lang['qihoo_topics_orderby']?></td><td><?=$lang['preview']?></td></tr>
<?=$topics?>
<tr><td colspan="7" class="singleborder">&nbsp;</td></tr>
<tr bgcolor="<?=ALTBG1?>" align="center">
<td><?=$lang['add_new']?></td>
<td><input type="text" size="20" name="newtopic"></td>
<td><input type="text" size="30" name="newkeyword"></td>
<td><input type="text" size="10" name="newlength" value="0"></td>
<td><select name="newstype"><option value="0" selected><?=$lang['qihoo_topics_type_fulltext']?></option><option value="1"><?=$lang['qihoo_topics_type_title']?></option></select></td>
<td><select name="newrelate"><option value="score"><?=$lang['qihoo_topics_orderby_relation']?></option><option value="pdate"><?=$lang['qihoo_topics_orderby_dateline']?></option><option value="rdate"><?=$lang['qihoo_topics_orderby_lastpost']?></option></select></td>
<td><a href="###" onClick="window.open('topic.php?topic='+findobj('newtopic').value+'&keyword='+findobj('newkeyword').value+'&stype='+findobj('newstype').value+'&length='+findobj('newlength').value+'&relate='+findobj('newrelate').value+'');">[<?=$lang['preview']?>]</a></td>
</tr></table><br>
<center><input type="submit" name="topicsubmit" value="<?=$lang['submit']?>"></center></form></td></tr>
<?

	} else {

		if(is_array($settingsnew['qihoo_topics'])) {
			foreach($settingsnew['qihoo_topics'] as $key => $value) {
				if($delete[$key]) {
					unset($topicarray[$key]);
				} else {
					$topicarray[$key] = array
						(
						'topic'		=> dhtmlspecialchars(stripslashes($value['topic'])),
						'keyword'	=> $value['keyword'] = trim($value['keyword']) ? dhtmlspecialchars(stripslashes($value['keyword'])) : $value['topic'],
						'length'	=> intval($value['length']),
						'stype'		=> $value['stype'],
						'relate'	=> $value['relate']
						);
				}
			}
		} else {
			$topicarray = array();
		}

		if($newtopic) {
			$topicarray[] = array
				(
				'topic'		=> dhtmlspecialchars(stripslashes($newtopic)),
				'keyword'	=> $newkeyword = trim($newkeyword) ? dhtmlspecialchars(stripslashes($newkeyword)) : $newtopic,
				'length'	=> intval($newlength),
				'stype'		=> $newstype > 1 ? 1 : intval($newstype),
				'relate'	=> $newrelate
				);
		}

		$settingsnew['qihoo_topics'] = addslashes(serialize($topicarray));
		$db->query("UPDATE {$tablepre}settings SET value='$settingsnew[qihoo_topics]' WHERE variable='qihoo_topics'");
		updatecache('settings');
		cpmsg('qihoo_topics_succeed', 'admincp.php?action=qihoo_topics');

	}

}

?>