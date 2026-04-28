<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: forum.func.php,v $
	$Revision: 1.4 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function checkautoclose() {
	global $timestamp, $forum, $thread;

	if(!$forum['ismoderator'] && $forum['autoclose']) {
		$closedby = $forum['autoclose'] > 0 ? 'dateline' : 'lastpost';
		$forum['autoclose'] = abs($forum['autoclose']);
		if($timestamp - $thread[$closedby] > $forum['autoclose'] * 86400) {
			return 'post_thread_closed_by_'.$closedby;
		}
	}
	return FALSE;
}

function forum(&$forum) {
	global $_DCOOKIE, $timestamp, $timeformat, $dateformat, $discuz_uid, $groupid, $lastvisit, $moddisplay, $timeoffset, $hideprivate, $onlinehold;

	if(!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || !empty($forum['allowview']) || (isset($forum['users']) && strstr($forum['users'], "\t$discuz_uid\t"))) {
		$forum['permission'] = 2;
	} elseif(!$hideprivate) {
		$forum['permission'] = 1;
	} else {
		return FALSE;
	}

	if($forum['icon']) {
		$forum['icon'] = '<a href="forumdisplay.php?fid='.$forum['fid'].'">'.image($forum['icon'], '', 'align="left"').'</a>';
	}

	$lastpost = array('tid' => 0, 'dateline' => 0, 'subject' => '', 'subjectsc' => '', 'author' => '');
	list($lastpost['tid'], $lastpost['subject'], $lastpost['dateline'], $lastpost['author']) = is_array($forum['lastpost']) ? $forum['lastpost'] : explode("\t", $forum['lastpost']);
	$forum['folder'] = '<img src="'.IMGDIR.'/'.((isset($_DCOOKIE['fid'.$forum['fid']]) && $_DCOOKIE['fid'.$forum['fid']] > $lastvisit ? $_DCOOKIE['fid'.$forum['fid']] : $lastvisit) < $lastpost['dateline'] ? 'red_' : '').'forum.gif">';

	if($lastpost['tid']) {
		$lastpost['dateline'] = gmdate("$dateformat $timeformat", $lastpost['dateline'] + $timeoffset * 3600);
		if($lastpost['author']) {
			$lastpost['author'] = '<a href="viewpro.php?username='.rawurlencode($lastpost['author']).'">'.$lastpost['author'].'</a>';
		}
		$forum['lastpost'] = $lastpost;
	} else {
		$forum['lastpost'] = '';
	}

	$forum['moderators'] = moddisplay($forum['moderators'], $moddisplay, !empty($forum['inheritedmod'])).'&nbsp;';

	if(isset($forum['subforums'])) {
		$forum['subforums'] = implode(', ', $forum['subforums']);
	}

	return TRUE;
}

function forumselect($groupselectable = FALSE) {
	global $_DCACHE, $discuz_uid, $groupid;

	$forumlist = '';
	if(!isset($_DCACHE['forums'])) {
		require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';
	}

	foreach($_DCACHE['forums'] as $fid1 => $forum1) {
		if($forum1['type'] == 'group') {
			$forumlist .= '<option value="'.($groupselectable ? $forum1['fid'] : '').'">'.$forum1['name'].'</option>';
			foreach($_DCACHE['forums'] as $fid2 => $forum2) {
				if($forum2['fup'] == $fid1 && $forum2['type'] == 'forum' && (!$forum2['viewperm'] || ($forum2['viewperm'] && forumperm($forum2['viewperm'])) || strstr($forum2['users'], "\t$discuz_uid\t"))) {
					$forumlist .= '<option value="'.$fid2.'">&nbsp; &gt; '.$forum2['name'].'</option>';
					foreach($_DCACHE['forums'] as $fid3 => $forum3) {
						if($forum3['fup'] == $fid2 && $forum3['type'] == 'sub' && (!$forum3['viewperm'] || ($forum3['viewperm'] && forumperm($forum3['viewperm'])) || strstr($forum3['users'], "\t$discuz_uid\t"))) {
							$forumlist .= '<option value="'.$fid3.'">&nbsp; &nbsp; &nbsp; &gt; '.$forum3['name'].'</option>';
						}
					}
				}
			}
			$forumlist .= '<option value="">&nbsp;</option>';
		} elseif(!$forum1['fup'] && $forum1['type'] == 'forum' && (!$forum1['viewperm'] || ($forum1['viewperm'] && forumperm($forum1['viewperm'])) || strstr($forum1['users'], "\t$discuz_uid\t"))) {
			$forumlist .= '<option value="'.$fid1.'"> &nbsp; &gt; '.$forum1['name'].'</option>';
			foreach($_DCACHE['forums'] as $fid2 => $forum2) {
				if($forum2['fup'] == $fid1 && $forum2['type'] == 'sub' && (!$forum2['viewperm'] || ($forum2['viewperm'] && forumperm($forum2['viewperm'])) || strstr($forum2['users'], "\t$discuz_uid\t"))) {
					$forumlist .= '<option value="'.$fid2.'">&nbsp; &nbsp; &nbsp; &gt; '.$forum2['name'].'</option>';
				}
			}
			$forumlist .= '<option value="">&nbsp;</option>';
		}

	}

	return $forumlist;
}

function visitedforums() {
	global $_DCACHE, $_DCOOKIE, $forum;

	$count = 0;
	$visitedforums = '';
	$fidarray = array($forum['fid']);
	foreach(explode('D', $_DCOOKIE['visitedfid']) as $fid) {
		if(isset($_DCACHE['forums'][$fid]) && !in_array($fid, $fidarray)) {
			$fidarray[] = $fid;
			if($fid != $forum['fid']) {
				$visitedforums .= '<option value="'.$fid.'">'.$_DCACHE['forums'][$fid]['name'].'</option>';
				if(++$count >= $GLOBALS['visitedforums']) {
					break;
				}
				
			}
		}
	}
	if(($visitedfid = implode('D', $fidarray)) != $_DCOOKIE['visitedfid']) {
		dsetcookie('visitedfid', $visitedfid, 2592000);
	}
	return $visitedforums;
}

function moddisplay($moderators, $type, $inherit = 0) {
	if($type == 'selectbox') {
		$modlist .= '<img src="images/common/online_moderator.gif" align="absmiddle"><select name="modlist" style="width: 100px'.($inherit ? '; font-weight: bold"' : '').'">';

		if($moderators) {
			foreach(explode("\t", $moderators) as $moderator) {
				$modlist .= '<option value="'.rawurlencode($moderator).'">'.$moderator.'</option>';
			}
		}
		$modlist .= '</select>';
		return $modlist;
	} else {
		if($type == 'forumdisplay') {
			$modicon = '<img src="images/common/online_moderator.gif" align="absmiddle"> ';
		} else {
			$modicon = '';
		}
		if($moderators) {
			$modlist = $comma = '';
			foreach(explode("\t", $moderators) as $moderator) {
				$modlist .= $comma.$modicon.'<a href="viewpro.php?username='.rawurlencode($moderator).'">'.($inherit ? '<b>'.$moderator.'</b>' : $moderator).'</a>';
				$comma = ', ';
			}
		} else {
			$modlist = '';
		}
		return $modlist;
	}	
}

?>