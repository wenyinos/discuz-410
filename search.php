<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: search.php,v $
	$Revision: 1.13 $
	$Date: 2006/02/23 13:44:02 $
*/

require_once './include/common.inc.php';
require_once DISCUZ_ROOT.'./include/forum.func.php';
require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';

$discuz_action = 111;

$cachelife_time = 300;		// Life span for cache of searching in specified range of time
$cachelife_text = 3600;		// Life span for cache of text searching

if(!submitcheck('searchsubmit', 1) && empty($page)) {

	$forumselect = forumselect();
	$checktype = array(($qihoo_status == 2 || ($qihoo_status == 1 && !$allowsearch) ? 'qihoo' : 'title') => 'checked');

	$disabled = array();
	$disabled['title'] = $disabled['blog'] = !$allowsearch ? 'disabled' : '';
	$disabled['fulltext'] = $allowsearch != 2 ? 'disabled' : '';

	include template('search');

} else {

	if($srchtype == 'qihoo') {

		if(!$srchtxt && !$srchuname) {
			showmessage('search_invalid');
		}

		$keywordlist = '';
		foreach(explode("\n", trim($qihoo_keyword)) as $key => $keyword) {
			$keywordlist .= $comma.trim($keyword);
			$comma = '|';
			if(strlen($keywordlist) >= 100) {
				break;
			}
		}

		if($orderby == 'lastpost') {
			$orderby = 'rdate';
		} elseif($orderby == 'dateline') {
			$orderby = 'pdate';
		} else {
			$orderby = '';
		}

		$stype = intval($stype) ? 'title' : '';

		$url = 'http://search.qihoo.com/usearch.html?site='.rawurlencode(site()).
			'&kw='.rawurlencode($srchtxt).'&ocs='.$charset.($orderby ? '&sort='.$orderby : '').
			($srchfid ? '&chanl='.rawurlencode($_DCACHE['forums'][$srchfid]['name']) : '').
			'&bbskw='.rawurlencode($keywordlist).
			'&summary='.$qihoo_summary.'&stype='.$stype.'&count='.$tpp.'&SITEREFER='.rawurlencode($boardurl).
			'&ALTBG1='.rawurlencode(ALTBG1).'&ALTBG2='.rawurlencode(ALTBG2).
			'&LINK='.rawurlencode(LINK).'&BORDERCOLOR='.rawurlencode(BORDERCOLOR).
			'&BGCODE='.rawurlencode(BGCODE).'&BOLD='.rawurlencode(BOLD).
			'&HEADERTEXT='.rawurlencode(HEADERTEXT).'&TABLETEXT='.rawurlencode(TABLETEXT).
			'&TEXT='.rawurlencode(TEXT).'&BORDERWIDTH='.rawurlencode(BORDERWIDTH).
			'&TABLEWIDTH='.rawurlencode(TABLEWIDTH).'&TABLESPACE='.rawurlencode(TABLESPACE).
			'&FONT='.rawurlencode(FONT).'& FONTSIZE='.rawurlencode(FONTSIZE).
			'&NOBOLD='.rawurlencode(NOBOLD).'&IMGDIR='.rawurlencode(IMGDIR).
			'&CATTEXT='.rawurlencode(CATTEXT).'&SMFONTSIZE='.rawurlencode(SMFONTSIZE).
			'&SMFONT='.rawurlencode(SMFONT).'&MAINTABLESPACE='.rawurlencode(MAINTABLESPACE).
			'&MAINTABLEWIDTH='.rawurlencode(MAINTABLEWIDTH).
			'&INNERBORDERWIDTH='.rawurlencode(INNERBORDERWIDTH).
			'&INNERBORDERCOLOR='.rawurlencode(INNERBORDERCOLOR).
			'&BGCODE MAINTABLEBGCODE='.rawurlencode(BGCODEMAINTABLEBGCODE).
			'&CATBGCODE='.rawurlencode(CATBGCODE).
			'&HEADERBGCODE='.rawurlencode(HEADERBGCODE);
		header("Location: $url");
		dexit();

	}

	if(!$allowsearch) {
		showmessage('group_nopermission', NULL, 'NOPERM');
	}

	$orderby = in_array($orderby, array('dateline', 'replies', 'views')) ? $orderby : 'lastpost';
	$ascdesc = isset($ascdesc) && $ascdesc == 'asc' ? 'asc' : 'desc';

	if(isset($searchid)) {

		require_once DISCUZ_ROOT.'./include/misc.func.php';

		$page = !ispage($page) ? 1 : $page;
		$start_limit = ($page - 1) * $tpp;

		$query = $db->query("SELECT searchstring, keywords, threads, tids FROM {$tablepre}searchindex WHERE searchid='$searchid'");
		if(!$index = $db->fetch_array($query)) {
			showmessage('search_id_invalid');
		}
		$index['keywords'] = rawurlencode($index['keywords']);
		$index['searchtype'] = preg_replace("/^([a-z]+)\|.*/", "\\1", $index['searchstring']);

		$threadlist = array();
		$query = $db->query("SELECT * FROM {$tablepre}threads WHERE tid IN ($index[tids]) AND displayorder>='0' ORDER BY $orderby $ascdesc LIMIT $start_limit, $tpp");
		while($thread = $db->fetch_array($query)) {
			$threadlist[] = procthread($thread);
		}

		$multipage = multi($index['threads'], $tpp, $page, "search.php?searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

		include template($index['searchtype'] != 'blog' ? 'search_threads' : 'search_blog');

	} else {

		checklowerlimit($creditspolicy['search'], -1);

		$srchtxt = isset($srchtxt) ? trim($srchtxt) : '';
		$srchuname = isset($srchuname) ? trim($srchuname) : '';

		if($allowsearch == 2 && $srchtype == 'fulltext') {
			periodscheck('searchbanperiods');
		} elseif(!in_array($srchtype, array('title', 'blog'))) {
			$srchtype = 'title';
		}

		$searchstring = $srchtype.'|'.addslashes($srchtxt).'|'.intval($srchuid).'|'.$srchuname.'|'.intval($srchfid).'|'.intval($srchfrom).'|'.intval($before);
		$searchindex = array('id' => 0, 'dateline' => '0');

		$query = $db->query("SELECT searchid, dateline,
			('$searchctrl'<>'0' AND ".(empty($discuz_uid) ? "useip='$onlineip'" : "uid='$discuz_uid'")." AND $timestamp-dateline<$searchctrl) AS flood,
			(searchstring='$searchstring' AND expiration>'$timestamp') AS indexvalid
			FROM {$tablepre}searchindex
			WHERE ('$searchctrl'<>'0' AND ".(empty($discuz_uid) ? "useip='$onlineip'" : "uid='$discuz_uid'")." AND $timestamp-dateline<$searchctrl) OR (searchstring='$searchstring' AND expiration>'$timestamp')
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

			if(!isset($srchfid)) {
				$srchfid = 'all';
			}

			$fids = $comma = '';
			foreach($_DCACHE['forums'] as $fid => $forum) {
				if($forum['type'] != 'group' && (!$forum['viewperm'] && $readaccess) || ($forum['viewperm'] && forumperm($forum['viewperm']))) {
					$fids .= "$comma'$fid'";
					$comma = ',';
				}
			}

			if(!$srchtxt && !$srchuid && !$srchuname && !$srchfrom) {
				showmessage('search_invalid');
			} elseif(empty($srchfid)) {
				showmessage('search_forum_invalid');
			} elseif(!$fids) {
				showmessage('group_nopermission', NULL, 'NOPERM');
			}

			if($maxspm) {
				$query = $db->query("SELECT COUNT(*) FROM {$tablepre}searchindex WHERE dateline>'$timestamp'-60");
				if(($db->result($query, 0)) >= $maxspm) {
					showmessage('search_toomany');
				}
			}

			if(!empty($srchfrom) && empty($srchtxt) && empty($srchuid) && empty($srchuname)) {

				$searchfrom = $before ? '<=' : '>=';
				$searchfrom .= $timestamp - $srchfrom;
				$sqlsrch = "FROM {$tablepre}threads t WHERE t.fid IN ($fids) AND t.displayorder>='0' AND t.lastpost$searchfrom";
				if($srchfid != "all" && $srchfid) {
					$sqlsrch .= " AND t.fid='$srchfid'";
				}
				$expiration = $timestamp + $cachelife_time;
				$keywords = '';

			} else {

				if(!empty($mytopics) && $srchuid) {
					if($fullmytopics) {
						$srchtype = 'fulltext';
					}
					$srchfrom = 2592000;
					$srchuname = $srchtxt = $before = '';
				}

				$sqlsrch = $srchtype == 'fulltext' ?
					"FROM {$tablepre}posts p, {$tablepre}threads t WHERE t.fid IN ($fids) AND p.tid=t.tid AND p.invisible='0'" :
					"FROM {$tablepre}threads t WHERE t.fid IN ($fids) AND t.displayorder>='0'".($srchtype == 'blog' ? ' AND t.blog=\'1\'' : '');

				if($srchuname) {
					$srchuid = $comma = '';
					$srchuname = str_replace('*', '%', addcslashes($srchuname, '%_'));
					$query = $db->query("SELECT uid FROM {$tablepre}members WHERE username LIKE '".str_replace('_', '\_', $srchuname)."' LIMIT 50");
					while($member = $db->fetch_array($query)) {
						$srchuid .= "$comma'$member[uid]'";
						$comma = ', ';
					}
					if(!$srchuid) {
						$sqlsrch .= ' AND 0';
					}
				} elseif($srchuid) {
					$srchuid = "'$srchuid'";
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
							$sqltxtsrch .= $srchtype == 'fulltext' ? "(p.message LIKE '%".str_replace('_', '\_', $text)."%' OR p.subject LIKE '%$text%')" : "t.subject LIKE '%$text%'";
						}
					}
					$sqlsrch .= " AND ($sqltxtsrch)";
				}

				if($srchuid) {
					$sqlsrch .= ' AND '.($srchtype == 'fulltext' ? 'p' : 't').".authorid IN ($srchuid)";
				}

				if($srchfid != 'all' && $srchfid) {
					$sqlsrch .= ' AND '.($srchtype == 'fulltext' ? 'p' : 't').".fid='$srchfid'";
				}

				if(!empty($srchfrom)) {
					$searchfrom = ($before ? '<=' : '>=').($timestamp - $srchfrom);
					$sqlsrch .= " AND t.lastpost$searchfrom";
				}

				$keywords = str_replace('%', '+', $srchtxt).(trim($srchuname) ? '+'.str_replace('%', '+', $srchuname) : '');
				$expiration = $timestamp + $cachelife_text;

			}

			$threads = $tids = 0;
			$query = $db->query("SELECT DISTINCT t.tid, t.closed $sqlsrch ORDER BY tid DESC LIMIT $maxsearchresults");
			while($thread = $db->fetch_array($query)) {
				if($thread['closed'] <= 1) {
					$tids .= ','.$thread['tid'];
					$threads++;
				}
			}
			$db->free_result($query);

			$db->query("INSERT INTO {$tablepre}searchindex (keywords, searchstring, useip, uid, dateline, expiration, threads, tids)
					VALUES ('$keywords', '$searchstring', '$onlineip', '$discuz_uid', '$timestamp', '$expiration', '$threads', '$tids')");
			$searchid = $db->insert_id();

			updatecredits($discuz_uid, $creditspolicy['search'], -1);

		}

		showmessage('search_redirect', "search.php?searchid=$searchid&orderby=$orderby&ascdesc=$ascdesc&searchsubmit=yes");

	}

}

?>