<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: cache.func.php,v $
	$Revision: 1.22.2.3 $
	$Date: 2006/03/07 06:44:55 $
*/

define('DISCUZ_KERNEL_VERSION', '4.1.0');
define('DISCUZ_KERNEL_RELEASE', '20060303');

if(isset($_GET['kernel_version'])) {
	exit('Crossday Discuz! Board<br>Developed by Comsenz Inc.<br><br>Version: '.DISCUZ_KERNEL_VERSION.'<br>Release: '.DISCUZ_KERNEL_RELEASE);
} elseif(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function arrayeval($array, $level = 0) {
	$space = '';
	for($i = 0; $i <= $level; $i++) {
		$space .= "\t";
	}
	$evaluate = "Array\n$space(\n";
	$comma = $space;
	foreach($array as $key => $val) {
		$key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;
		$val = !is_array($val) && (!preg_match("/^\-?[1-9]\d*$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val;
		if(is_array($val)) {
			$evaluate .= "$comma$key => ".arrayeval($val, $level + 1);
		} else {
			$evaluate .= "$comma$key => $val";
		}
		$comma = ",\n$space";
	}
	$evaluate .= "\n$space)";
	return $evaluate;
}

function updatecache($cachename = '') {
	global $db, $bbname, $tablepre;

	$cachescript = array
		(
		'settings'	=> array('settings'),
		'crons'		=> array('crons'),
		'index'		=> array('announcements', 'onlinelist', 'forumlinks'),
		'forumdisplay'	=> array('announcements_forum', 'globalstick', 'forums', 'icons', 'onlinelist'),
		'viewthread'	=> array('forums', 'usergroups', 'ranks', 'bbcodes', 'smilies', 'fields_thread'),
		'post'		=> array('bbcodes', 'smilies_display', 'smilies', 'icons'),
		'blog'		=> array('usergroups', 'ranks', 'bbcodes', 'smilies'),
		'forums'	=> array('forums'),
		'profilefields'	=> array('fields_required', 'fields_optional'),
		'censor'	=> array('censor'),
		'ipbanned'	=> array('ipbanned'),
		'bbcodes'	=> array('bbcodes', 'smilies'),
		'medals'	=> array('medals')
		);

	foreach($cachescript as $script => $cachenames) {
		if(!$cachename || ($cachename && in_array($cachename, $cachenames))) {
			writetocache($script, $cachenames);
		}
	}

	if(!$cachename || $cachename == 'styles') {
		$stylevars = array();
		$query = $db->query("SELECT * FROM {$tablepre}stylevars");
		while($var = $db->fetch_array($query)) {
			$stylevars[$var['styleid']][$var['variable']] = $var['substitute'];
		}
		$query = $db->query("SELECT s.*, t.directory AS tpldir FROM {$tablepre}styles s LEFT JOIN {$tablepre}templates t ON s.templateid=t.templateid");
		while($data = $db->fetch_array($query)) {
			$data = array_merge($data, $stylevars[$data['styleid']]);

			$data['bgcode'] = strpos($data['bgcolor'], '.') ? "background-image: url(\"$data[imgdir]/$data[bgcolor]\")" : "background-color: $data[bgcolor]";
			$data['maintablebgcode'] = strpos($data['maintablecolor'], '.') ? "background=\"$data[maintablecolor]\"" : "bgcolor=\"$data[maintablecolor]\"";
			$data['catbgcode'] = strpos($data['catcolor'], '.') ? "background-image: url(\"$data[imgdir]/$data[catcolor]\")" : "background-color: $data[catcolor]";
			$data['headerbgcode'] = strpos($data['headercolor'], '.') ? "background-image: url(\"$data[imgdir]/$data[headercolor]\")" : "background-color: $data[headercolor]";
			$data['boardlogo'] = image($data['boardimg'], $data['imgdir'], "alt=\"$bbname\"");
			$data['bold'] = $data['nobold'] ? 'normal' : 'bold';

			writetocache($data['styleid'], '', getcachevars($data, 'CONST'), 'style_');
		}
	}

	if(!$cachename || $cachename == 'usergroups') {
		$query = $db->query("SELECT * FROM {$tablepre}usergroups u
					LEFT JOIN {$tablepre}admingroups a ON u.groupid=a.admingid");
		while($data = $db->fetch_array($query)) {
			$ratearray = array();
			if($data['raterange']) {
				foreach(explode("\n", $data['raterange']) as $rating) {
					$rating = explode("\t", $rating);
					$ratearray[$rating[0]] = array('min' => $rating[1], 'max' => $rating[2], 'mrpd' => $rating[3]);
				}
			}
			$data['raterange'] = $ratearray;
			$data['grouptitle'] = $data['color'] ? '<font color="'.$data['color'].'">'.$data['grouptitle'].'</font>' : $data['grouptitle'];
			$data['grouptype'] = $data['type'];
			$data['grouppublic'] = $data['system'] != 'private';
			$data['groupcreditshigher'] = $data['creditshigher'];
			$data['groupcreditslower'] = $data['creditslower'];
			unset($data['type'], $data['system'], $data['creditshigher'], $data['creditslower'], $data['color'], $data['groupavatar'], $data['admingid']);
			foreach($data as $key => $val) {
				if(!isset($data[$key])) {
					unset($data[$key]);
				}
			}
			writetocache($data['groupid'], '', getcachevars($data), 'usergroup_');
		}
	}

	if(!$cachename || $cachename == 'admingroups') {
		$query = $db->query("SELECT * FROM {$tablepre}admingroups");
		while($data = $db->fetch_array($query)) {
			writetocache($data['admingid'], '', getcachevars($data), 'admingroup_');
		}
	}

	if(!$cachename || $cachename == 'plugins') {
		$query = $db->query("SELECT pluginid, available, adminid, name, identifier, datatables, directory, copyright, modules FROM {$tablepre}plugins");
		while($plugin = $db->fetch_array($query)) {
			$data = array_merge($plugin, array('modules' => array()), array('vars' => array()));
			$plugin['modules'] = unserialize($plugin['modules']);
			if(is_array($plugin['modules'])) {
				foreach($plugin['modules'] as $module) {
					$data['modules'][$module['name']] = $module;
				}
			}
			$queryvars = $db->query("SELECT variable, value FROM {$tablepre}pluginvars WHERE pluginid='$plugin[pluginid]'");
			while($var = $db->fetch_array($queryvars)) {
				$data['vars'][$var['variable']] = $var['value'];
			}
			writetocache($plugin['identifier'], '', "\$_DPLUGIN['$plugin[identifier]'] = ".arrayeval($data), 'plugin_');
		}
	}
}

function updatesettings() {
	global $_DCACHE;
	if(isset($_DCACHE['settings']) && is_array($_DCACHE['settings'])) {
		writetocache('settings', '', '$_DCACHE[\'settings\'] = '.arrayeval($_DCACHE['settings']));
	}
}

function writetocache($script, $cachenames, $cachedata = '', $prefix = 'cache_') {
	if(is_array($cachenames) && !$cachedata) {
		foreach($cachenames as $name) {
			$cachedata .= getcachearray($name);
		}
	}

	$dir = DISCUZ_ROOT.'./forumdata/cache/';
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(@$fp = fopen("$dir$prefix$script.php", 'w')) {
		fwrite($fp, "<?php\n//Discuz! cache file, DO NOT modify me!\n".
			"//Created on ".date("M j, Y, G:i")."\n\n$cachedata?>");
		fclose($fp);
	} else {
		dexit('Can not write to cache files, please check directory ./forumdata/ and ./forumdata/cache/ .');
	}
}

function getcachearray($cachename) {
	global $db, $timestamp, $tablepre;

	$cols = '*';
	$conditions = '';
	switch($cachename) {
		case 'settings':
			$table = 'settings';
			$conditions = "WHERE variable NOT IN ('bbrules', 'bbrulestxt', 'maxonlines', 'welcomemsg', 'welcomemsgtxt', 'newsletter', 'creditsnotify', 'custombackup')";
			break;
		case 'crons':
			$table = 'crons';
			$cols = 'cronid, name, filename, nextrun, weekday, day, hour, minute';
			$conditions = "WHERE available>'0'";
			break;
		case 'usergroups':
			$table = 'usergroups';
			$cols = 'groupid, type, grouptitle, creditshigher, creditslower, stars, color, groupavatar, readaccess, allowavatar, allowcusbbcode, allowuseblog';
			$conditions = "ORDER BY creditslower";
			break;
		case 'ranks':
			$table = 'ranks';
			$cols = 'ranktitle, postshigher, stars, color';
			$conditions = "ORDER BY postshigher DESC";
			break;
		case 'announcements':
			$table = 'announcements';
			$cols = 'id, subject, starttime, endtime';
			$conditions = "WHERE starttime<='$timestamp' ORDER BY displayorder, starttime DESC, id DESC";
			break;
		case 'announcements_forum':
			$table = 'announcements a';
			$cols = 'a.id, a.author, a.message, m.uid AS authorid, a.subject, a.starttime';
			$conditions = "LEFT JOIN {$tablepre}members m ON m.username=a.author WHERE a.starttime<='$timestamp' ORDER BY a.displayorder, a.starttime DESC, a.id DESC LIMIT 1";
			break;
		case 'globalstick':
			$table = 'forums';
			$cols = 'fid, type, fup';
			$conditions = "WHERE status='1' AND type IN ('forum', 'sub') ORDER BY type";
			break;
		case 'forums':
			$table = 'forums f';
			$cols = 'f.fid, f.type, f.name, f.fup, ff.viewperm, a.uid';
			$conditions = "LEFT JOIN {$tablepre}forumfields ff ON ff.fid=f.fid LEFT JOIN {$tablepre}access a ON a.fid=f.fid AND a.allowview='1' WHERE f.status='1' ORDER BY displayorder";
			break;
		case 'onlinelist':
			$table = 'onlinelist';
			$conditions = "ORDER BY displayorder";
			break;
		case 'forumlinks':
			$table = 'forumlinks';
			$conditions = "ORDER BY displayorder";
			break;
		case 'bbcodes':
			$table = 'bbcodes';
			$conditions = "WHERE available='1'";
			break;
		case 'smilies':
			$table = 'smilies';
			$cols = 'code, url';
			$conditions = "WHERE type='smiley' ORDER BY LENGTH(code) DESC";
			break;
		case 'smilies_display':
			$table = 'smilies';
			$cols = 'code, url';
			$conditions = "WHERE type='smiley' ORDER BY displayorder";
			break;
		case 'icons':
			$table = 'smilies';
			$cols = 'id, url';
			$conditions = "WHERE type='icon' ORDER BY displayorder";
			break;
		case 'fields_required':
			$table = 'profilefields';
			$cols = 'fieldid, invisible, title, description, required, unchangeable, selective, choices';
			$conditions = "WHERE available='1' AND required='1' ORDER BY displayorder";
			break;
		case 'fields_optional':
			$table = 'profilefields';
			$cols = 'fieldid, invisible, title, description, required, unchangeable, selective, choices';
			$conditions = "WHERE available='1' AND required='0' ORDER BY displayorder";
			break;
		case 'fields_thread':
			$table = 'profilefields';
			$cols = 'fieldid, title, selective, choices';
			$conditions = "WHERE available='1' AND invisible='0' AND showinthread='1' ORDER BY displayorder";
			break;
		case 'ipbanned':
			$db->query("DELETE FROM {$tablepre}banned WHERE expiration<'$timestamp'");
			$table = 'banned';
			$cols = 'ip1, ip2, ip3, ip4, expiration';
			break;
		case 'censor':
			$table = 'words';
			$cols = 'find, replacement';
			break;
		case 'medals':
			$table = 'medals';
			$cols = 'medalid, name, image';
			$conditions = "WHERE available='1'";
			break;
	}

	$data = array();

	$query = $db->query("SELECT $cols FROM {$tablepre}$table $conditions");
	switch($cachename) {
		case 'settings':
			$data['qihoo_links'] = array();
			while($setting = $db->fetch_array($query)) {
				if($setting['variable'] == 'extcredits') {
					if(is_array($setting['value'] = unserialize($setting['value']))) {
						foreach($setting['value'] as $key => $value) {
							if($value['available']) {
								unset($setting['value'][$key]['available']);
							} else {
								unset($setting['value'][$key]);
							}
						}
					}
				} elseif($setting['variable'] == 'qihoo_keywords') {
					foreach(explode("\n", trim($setting['value'])) as $keyword) {
						if($keyword = trim($keyword)) {
							$data['qihoo_links']['keywords'][] = '<a href="search.php?srchtype=qihoo&srchtxt='.rawurlencode($keyword).'&searchsubmit=yes" target="_blank">'.dhtmlspecialchars(trim($keyword)).'</a>';
						}
					}
				} elseif($setting['variable'] == 'qihoo_topics') {
					if(is_array($topics = unserialize($setting['value']))) {
						foreach($topics as $topic) {
							if($topic['topic'] = trim($topic['topic'])) {
								$data['qihoo_links']['topics'][] = '<a href="topic.php?topic='.rawurlencode($topic['topic']).'&keyword='.rawurlencode($topic['keyword']).'&stype='.$topic['stype'].'&length='.$topic['length'].'&relate='.$topic['relate'].'" target="_blank">'.dhtmlspecialchars(trim($topic['topic'])).'</a>';
							}
						}
					}
				} elseif($setting['variable'] == 'creditspolicy') {
					$setting['value'] = unserialize($setting['value']);
				} elseif($setting['variable'] == 'creditsformula') {
					$setting['value'] = preg_replace("/(digestposts|posts|pageviews|oltime|extcredits[1-8])/", "\$member['\\1']", $setting['value']);
				} elseif($setting['variable'] == 'maxsmilies') {
					$setting['value'] = $setting['value'] <= 0 ? -1 : $setting['value'];
				}

				if(!in_array($setting['variable'], array('qihoo_keywords', 'qihoo_topics'))) {
					$GLOBALS[$setting['variable']] = $data[$setting['variable']] = $setting['value'];
				}
			}
			if($data['stylejump']) {
				$data['stylejump'] = array();
				$query = $db->query("SELECT styleid, name FROM {$tablepre}styles WHERE available='1'");
				while($style = $db->fetch_array($query)) {
					$data['stylejump'][$style['styleid']] = dhtmlspecialchars($style['name']);
				}
			}
			$query = $db->query("SELECT COUNT(*) FROM {$tablepre}members");
			$GLOBALS['totlamembers'] = $data['totalmembers'] = $db->result($query, 0);
			$query = $db->query("SELECT username FROM {$tablepre}members ORDER BY uid DESC LIMIT 1");
			$GLOBALS['lastmember'] = $data['lastmember'] = $db->result($query, 0);
			$GLOBALS['version'] = $data['version'] = DISCUZ_KERNEL_VERSION;
			$modreasonsarray = array();
			foreach(explode("\n", trim($data['modreasons'])) as $reason) {
				$reason = trim($reason);
				$modreasonarray[] = $reason ? array(dhtmlspecialchars($reason), $reason) : array('', '--------');
			}
			$GLOBALS['modreasons'] = $data['modreasons'] = $modreasonarray;

			$query = $db->query("SELECT nextrun FROM {$tablepre}crons WHERE available>'0' AND nextrun>'$timestamp' ORDER BY nextrun LIMIT 1");
			$data['cronnextrun'] = $db->result($query, 0);

			$data['todaysbdays'] = '';
			if($data['bdaystatus']) {
				if(empty($_DCACHE['settings']['todaysbdays'])) {
					$bdaymembers = array();
					$query = $db->query("SELECT uid, username, email, bday FROM {$tablepre}members WHERE bday LIKE '%-".gmdate('m-d', $timestamp + $data['timeoffset'] * 3600)."' ORDER BY bday");
					while($bdaymember = $db->fetch_array($query)) {
						$birthyear = intval($bdaymember['bday']);
						$bdaymembers[] = '<a href="viewpro.php?uid='.$bdaymember['uid'].'" target="_blank" '.($birthyear ? 'alt="'.$bdaymember['bday'].'"' : '').'>'.$bdaymember['username'].'</a>';
					}
					$data['todaysbdays'] = implode(', ', $bdaymembers);
				} else {
					$data['todaysbdays'] = $_DCACHE['settings']['todaysbdays'];
				}
			}

			$data['advertisements'] = array();
			$query = $db->query("SELECT * FROM {$tablepre}advertisements WHERE available>'0' AND starttime<='$timestamp' ORDER BY displayorder");
			if($db->num_rows($query)) {
				while($adv = $db->fetch_array($query)) {
					$data['advertisements']['items'][$adv['advid']] = $adv['code'];
					if($adv['targets'] == '') {
						$data['advertisements']['types'][$adv['type']]['all'][] = $adv['advid'];
					} else {
						foreach(explode("\t", $adv['targets']) as $target) {
							if($target == 0) {
								$data['advertisements']['types'][$adv['type']]['index'][] = $adv['advid'];
							} else {
								$data['advertisements']['types'][$adv['type']]['forum_'.$target][] = $adv['advid'];
							}
						}
					}
				}
				$query = $db->query("SELECT starttime FROM {$tablepre}advertisements WHERE available>'0' AND starttime>'$timestamp' ORDER BY starttime LIMIT 1");
				$data['advertisements']['lateststarttime'] = intval($db->result($query, 0));
				$query = $db->query("SELECT endtime FROM {$tablepre}advertisements WHERE available>'0' AND endtime>'$timestamp' ORDER BY endtime LIMIT 1");
				$data['advertisements']['latestendtime'] = intval($db->result($query, 0));
			}

			$data['plugins'] = array();
			$query = $db->query("SELECT available, name, identifier, directory, datatables, modules FROM {$tablepre}plugins");
			while($plugin = $db->fetch_array($query)) {
				$plugin['modules'] = unserialize($plugin['modules']);
				if(is_array($plugin['modules'])) {
					foreach($plugin['modules'] as $module) {
						if($plugin['available'] && isset($module['name'])) {
							switch($module['type']) {
								case 1:
									$data['plugins']['links'][$plugin['identifier']][$module['name']] = array('adminid' => $module['adminid'], 'url' => "<a href=\"$module[url]\">$module[menu]</a>");
									break;
								case 2:
									$data['plugins']['links'][$plugin['identifier']][$module['name']] = array('adminid' => $module['adminid'], 'url' => "<a href=\"plugin.php?identifier=$plugin[identifier]&module=$module[name]\">$module[menu]</a>", 'directory' => $plugin['directory']);
									break;
								case 4:
									$data['plugins']['include'][] = array('adminid' => $module['adminid'], 'script' => $plugin['directory'].$module['name']);
									break;
							}
						}
					}
				}
			}

			$data['hooks'] = array();
			$query = $db->query("SELECT ph.title, ph.code, p.identifier FROM {$tablepre}plugins p
				LEFT JOIN {$tablepre}pluginhooks ph ON ph.pluginid=p.pluginid AND ph.available='1'
				WHERE p.available='1' ORDER BY p.identifier");
			while($hook = $db->fetch_array($query)) {
				if($hook['title'] && $hook['code']) {
					$data['hooks'][$hook['identifier'].'_'.$hook['title']] = $hook['code'];
				}
			}
			break;
		case 'crons':
			while($cron = $db->fetch_array($query)) {
				$cronid = $cron['cronid'];
				$cron['filename'] = str_replace(array('..', '/', '\\'), array('', '', ''), $cron['filename']);
				$cron['minute'] = explode("\t", $cron['minute']);
				unset($cron['cronid']);
				$data[$cronid] = $cron;
			}
			break;
		case 'usergroups':
			global $userstatusby;
			while($group = $db->fetch_array($query)) {
				$groupid = $group['groupid'];
				$group['grouptitle'] = $group['color'] ? '<font color="'.$group['color'].'">'.$group['grouptitle'].'</font>' : $group['grouptitle'];
				if($userstatusby == 2) {
					$group['byrank'] = $group['type'] == 'member' ? 1 : 0;
				}
				if($userstatusby == 0 || ($userstatusby == 2 && $group['type'] == 'member')) {
					unset($group['grouptitle'], $group['stars']);
				}
				if($group['type'] != 'member') {
					unset($group['creditshigher'], $group['creditslower']);
				}
				unset($group['groupid'], $group['color']);
				$data[$groupid] = $group;
			}
			break;
		case 'ranks':
			global $userstatusby;
			if($userstatusby == 2) {
				while($rank = $db->fetch_array($query)) {
					$rank['ranktitle'] = $rank['color'] ? '<font color="'.$rank['color'].'">'.$rank['ranktitle'].'</font>' : $rank['ranktitle'];
					unset($rank['color']);
					$data[] = $rank;
				}
			}
			break;
		case 'announcements_forum':
			if($data = $db->fetch_array($query)) {
				$data['authorid'] = intval($data['authorid']);
				$data['message'] = cutstr(strip_tags($data['message']), 18);
			} else {
				$data = array();
			}
			break;
		case 'globalstick':
			$fuparray = $threadarray = array();
			while($forum = $db->fetch_array($query)) {
				switch($forum['type']) {
					case 'forum':
						$fuparray[$forum['fid']] = $forum['fup'];
						break;
					case 'sub':
						$fuparray[$forum['fid']] = $fuparray[$forum['fup']];
						break;
				}
			}
			$query = $db->query("SELECT tid, fid, displayorder FROM {$tablepre}threads WHERE displayorder IN (2, 3)");
			while($thread = $db->fetch_array($query)) {
				switch($thread['displayorder']) {
					case 2:
						$threadarray[$fuparray[$thread['fid']]][] = $thread['tid'];
						break;
					case 3:
						$threadarray['global'][] = $thread['tid'];
						break;
				}
			}
			foreach(array_unique($fuparray) as $gid) {
				if(!empty($threadarray[$gid])) {
					$data['categories'][$gid] = array(
						'tids'	=> implode(',', $threadarray[$gid]),
						'count'	=> intval(@count($threadarray[$gid]))
					);
				}
			}
			$data['global'] = array(
				'tids'	=> empty($threadarray['global']) ? 0 : implode(',', $threadarray['global']),
				'count'	=> intval(@count($threadarray['global']))
			);
			break;
		case 'censor':
			$banned = $mod = array();
			$data = array('filter' => array(), 'banned' => '', 'mod' => '');
			while($censor = $db->fetch_array($query)) {
				$censor['find'] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($censor['find'], '/'));
				switch($censor['replacement']) {
					case '{BANNED}':
						$banned[] = $censor['find'];
						break;
					case '{MOD}':
						$mod[] = $censor['find'];
						break;
					default:
						$data['filter']['find'][] = '/'.$censor['find'].'/i';
						$data['filter']['replace'][] = $censor['replacement'];
						break;
				}
			}
			if($banned) {
				$data['banned'] = '/('.implode('|', $banned).')/i';
			}
			if($mod) {
				$data['mod'] = '/('.implode('|', $mod).')/i';
			}
			break;
		case 'forums':
			while($forum = $db->fetch_array($query)) {
				if(!isset($data[$forum['fid']])) {
					$forum['name'] = strip_tags($forum['name']);
					if($forum['uid']) {
						$forum['users'] = "\t$forum[uid]\t";
					}
					unset($forum['uid']);
					$data[$forum['fid']] = $forum;
				} elseif($forum['uid']) {
					if(!$data[$forum['fid']]['users']) {
						$data[$forum['fid']]['users'] = "\t";
					}
					$data[$forum['fid']]['users'] .= "$forum[uid]\t";
				}
			}
			break;
		case 'onlinelist':
			$data['legend'] = '';
			while($list = $db->fetch_array($query)) {
				$data[$list['groupid']] = $list['url'];
				$data['legend'] .= "<img src=\"images/common/$list[url]\"> $list[title] &nbsp; &nbsp; &nbsp; ";
			}
			break;
		case 'forumlinks':
			$tightlink_text = $tightlink_logo = '';
			while($flink = $db->fetch_array($query)) {
				if($flink['note']) {
					$forumlink['content'] = "<a href=\"$flink[url]\" target=\"_blank\"><span class=\"bold\">$flink[name]</span></a><br>$flink[note]";
					if($flink['logo']) {
						$forumlink['type'] = 1;
						$forumlink['logo'] = $flink['logo'];
					} else {
						$forumlink['type'] = 2;
					}
					$data[] = $forumlink;
				} else {
					if($flink['logo']) {
						$tightlink_logo .= "<a href=\"$flink[url]\" target=\"_blank\"><img src=\"$flink[logo]\" border=\"0\" alt=\"$flink[name]\"></a> ";
					} else {
						$tightlink_text .= "<a href=\"$flink[url]\" target=\"_blank\">[$flink[name]]</a> ";
					}
				}
			}
			if($tightlink_logo || $tightlink_text) {
				$tightlink_logo .= $tightlink_logo ? '<br>' : '';
				$data[] = array('type' => 3, 'content' => $tightlink_logo.$tightlink_text);
			}
			break;
		case 'bbcodes':
			$regexp = array	(	1 => "/\[{bbtag}](.+?)\[\/{bbtag}\]/is",
						2 => "/\[{bbtag}=(['\"]?)(.+?)(['\"]?)\](.+?)\[\/{bbtag}\]/is",
						3 => "/\[{bbtag}=(['\"]?)(.+?)(['\"]?),(['\"]?)(.+?)(['\"]?)\](.+?)\[\/{bbtag}\]/is"
					);

			while($bbcode = $db->fetch_array($query)) {
				$search = str_replace('{bbtag}', $bbcode['tag'], $regexp[$bbcode['params']]);
				$bbcode['replacement'] = preg_replace("/([\r\n])/", '', $bbcode['replacement']);
				switch($bbcode['params']) {
					case 2:
						$bbcode['replacement'] = str_replace('{1}', '\\2', $bbcode['replacement']);
						$bbcode['replacement'] = str_replace('{2}', '\\4', $bbcode['replacement']);
						break;
					case 3:
						$bbcode['replacement'] = str_replace('{1}', '\\2', $bbcode['replacement']);
						$bbcode['replacement'] = str_replace('{2}', '\\5', $bbcode['replacement']);
						$bbcode['replacement'] = str_replace('{3}', '\\7', $bbcode['replacement']);
						break;
					default:
						$bbcode['replacement'] = str_replace('{1}', '\\1', $bbcode['replacement']);
						break;
				}
				$replace = $bbcode['replacement'];

				for($i = 0; $i < $bbcode['nest']; $i++) {
					$data['searcharray'][] = $search;
					$data['replacearray'][] = $replace;
				}
			}

			break;
		case 'smilies':
			$data = array('searcharray' => array(), 'replacearray' => array());
			while($smiley = $db->fetch_array($query)) {
				$data['searcharray'][] = '/'.preg_quote(dhtmlspecialchars($smiley['code']), '/').'/';
				$data['replacearray'][] = $smiley['url'];
			}
			break;
		case 'smilies_display':
			while($smiley = $db->fetch_array($query)) {
				$smiley['code'] = dhtmlspecialchars($smiley['code']);
				$data[] = $smiley;
			}
			break;
		case 'icons':
			while($icon = $db->fetch_array($query)) {
				$data[$icon['id']] = $icon['url'];
			}
			break;
		case (in_array($cachename, array('fields_required', 'fields_optional', 'fields_thread'))):
			while($field = $db->fetch_array($query)) {
				$choices = array();
				if($field['selective']) {
					foreach(explode("\n", $field['choices']) as $item) {
						list($index, $choice) = explode('=', $item);
						$choices[trim($index)] = trim($choice);
					}
					$field['choices'] = $choices;
				} else {
					unset($field['choices']);
				}
				$data[] = $field;
			}
			break;
		case 'ipbanned':
			if($db->num_rows($query)) {
				$data['expiration'] = 0;
				$data['regexp'] = $separator = '';
			}
			while($banned = $db->fetch_array($query)) {
				$data['expiration'] = !$data['expiration'] || $banned['expiration'] < $data['expiration'] ? $banned['expiration'] : $data['expiration'];
				$data['regexp'] .=	$separator.
							($banned['ip1'] == '-1' ? '\\d+\\.' : $banned['ip1'].'\\.').
							($banned['ip2'] == '-1' ? '\\d+\\.' : $banned['ip2'].'\\.').
							($banned['ip3'] == '-1' ? '\\d+\\.' : $banned['ip3'].'\\.').
							($banned['ip4'] == '-1' ? '\\d+' : $banned['ip4']);
				$separator = '|';
			}
			break;
		case 'medals':
			while($medal = $db->fetch_array($query)) {
				$data[$medal['medalid']] = array('name' => $medal['name'], 'image' => $medal['image']);
			}
			break;
		default:
			while($datarow = $db->fetch_array($query)) {
				$data[] = $datarow;
			}
	}

	return "\$_DCACHE['$cachename'] = ".arrayeval($data).";\n\n";
}

function getcachevars($data, $type = 'VAR') {
	$evaluate = '';
	foreach($data as $key => $val) {
		if(is_array($val)) {
			$evaluate .= "\$$key = ".arrayeval($val).";\n";
		} else {
			$val = addcslashes($val, '\'\\');
			$evaluate .= $type == 'VAR' ? "\$$key = '$val';\n" : "define('".strtoupper($key)."', '$val');\n";
		}
	}
	return $evaluate;
}

?>