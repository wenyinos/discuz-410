<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: misc.func.php,v $
	$Revision: 1.6 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function convertip($ip) {
	if(!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {
		return '';
	}

	if($fd = @fopen(DISCUZ_ROOT.'./ipdata/wry.dat', 'rb')) {

		$ip = explode('.', $ip);
		$ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];

		$DataBegin = fread($fd, 4);
		$DataEnd = fread($fd, 4);
		$ipbegin = implode('', unpack('L', $DataBegin));
		if($ipbegin < 0) $ipbegin += pow(2, 32);
		$ipend = implode('', unpack('L', $DataEnd));
		if($ipend < 0) $ipend += pow(2, 32);
		$ipAllNum = ($ipend - $ipbegin) / 7 + 1;

		$BeginNum = 0;
		$EndNum = $ipAllNum;

		while($ip1num > $ipNum || $ip2num < $ipNum) {
			$Middle= intval(($EndNum + $BeginNum) / 2);

			fseek($fd, $ipbegin + 7 * $Middle);
			$ipData1 = fread($fd, 4);
			if(strlen($ipData1) < 4) {
				fclose($fd);
				return 'System Error';
			}
			$ip1num = implode('', unpack('L', $ipData1));
			if($ip1num < 0) $ip1num += pow(2, 32);

			if($ip1num > $ipNum) {
				$EndNum = $Middle;
				continue;
			}

			$DataSeek = fread($fd, 3);
			if(strlen($DataSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$DataSeek = implode('', unpack('L', $DataSeek.chr(0)));
			fseek($fd, $DataSeek);
			$ipData2 = fread($fd, 4);
			if(strlen($ipData2) < 4) {
				fclose($fd);
				return 'System Error';
			}
			$ip2num = implode('', unpack('L', $ipData2));
			if($ip2num < 0) $ip2num += pow(2, 32);

			if($ip2num < $ipNum) {
				if($Middle == $BeginNum) {
					fclose($fd);
					return 'Unknown';
				}
				$BeginNum = $Middle;
			}
		}

		$ipFlag = fread($fd, 1);
		if($ipFlag == chr(1)) {
			$ipSeek = fread($fd, 3);
			if(strlen($ipSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipSeek = implode('', unpack('L', $ipSeek.chr(0)));
			fseek($fd, $ipSeek);
			$ipFlag = fread($fd, 1);
		}

		if($ipFlag == chr(2)) {
			$AddrSeek = fread($fd, 3);
			if(strlen($AddrSeek) < 3) {
				fclose($fd);
				return 'System Error';
			}
			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}

			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;

			$AddrSeek = implode('', unpack('L', $AddrSeek.chr(0)));
			fseek($fd, $AddrSeek);

			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;
		} else {
			fseek($fd, -1, SEEK_CUR);
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr1 .= $char;

			$ipFlag = fread($fd, 1);
			if($ipFlag == chr(2)) {
				$AddrSeek2 = fread($fd, 3);
				if(strlen($AddrSeek2) < 3) {
					fclose($fd);
					return 'System Error';
				}
				$AddrSeek2 = implode('', unpack('L', $AddrSeek2.chr(0)));
				fseek($fd, $AddrSeek2);
			} else {
				fseek($fd, -1, SEEK_CUR);
			}
			while(($char = fread($fd, 1)) != chr(0))
				$ipAddr2 .= $char;
		}
		fclose($fd);

		if(preg_match('/http/i', $ipAddr2)) {
			$ipAddr2 = '';
		}
		$ipaddr = "$ipAddr1 $ipAddr2";
		$ipaddr = preg_replace('/CZ88\.NET/is', '', $ipaddr);
		$ipaddr = preg_replace('/^\s*/is', '', $ipaddr);
		$ipaddr = preg_replace('/\s*$/is', '', $ipaddr);
		if(preg_match('/http/i', $ipaddr) || $ipaddr == '') {
			$ipaddr = 'Unknown';
		}

		return $ipaddr;

	} else {

		$datadir = DISCUZ_ROOT.'./ipdata/';
		$ip_detail = explode('.', $ip);
		if(file_exists($datadir.$ip_detail[0].'.txt')) {
			$ip_fdata = @fopen($datadir.$ip_detail[0].'.txt', 'r');
		} else {
			if(!($ip_fdata = @fopen($datadir.'0.txt', 'r'))) {
				return 'Invalid IP data file';
			}
		}
		for ($i = 0; $i <= 3; $i++) {
			$ip_detail[$i] = sprintf('%03d', $ip_detail[$i]);
		}
		$ip = join('.', $ip_detail);
		do {
			$ip_data = fgets($ip_fdata, 200);
			$ip_data_detail = explode('|', $ip_data);
			if($ip >= $ip_data_detail[0] && $ip <= $ip_data_detail[1]) {
				fclose($ip_fdata);
				return $ip_data_detail[2].$ip_data_detail[3];
			}
		} while(!feof($ip_fdata));
		fclose($ip_fdata);
		return 'UNKNOWN';

	}

}

function procthread($thread) {
	global $dateformat, $timeformat, $timeoffset, $ppp, $colorarray;

	if(empty($colorarray)) {
		$colorarray = array('', 'red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'purple', 'gray');
	}

	$thread['forumname'] = $GLOBALS['_DCACHE']['forums'][$thread['fid']]['name'];
	$thread['dateline'] = gmdate($dateformat, $thread['dateline'] + $timeoffset * 3600);
	$thread['lastpost'] = gmdate("$dateformat $timeformat", $thread['lastpost'] + $timeoffset * 3600);
	$thread['lastposterenc'] = rawurlencode($thread['lastposter']);

	if($thread['replies'] > $thread['views']) {
		$thread['views'] = $thread['replies'];
	}

	$postsnum = $thread['replies'] + 1;
	$pagelinks = '';
	if($postsnum  > $ppp) {
		$posts = $postsnum;
		$topicpages = ceil($posts / $ppp);
		for ($i = 1; $i <= $topicpages; $i++) {
			$pagelinks .= "<a href=\"viewthread.php?tid=$thread[tid]&page=$i\" target=\"_blank\">$i</a> ";
			if($i == 6) {
				$i = $topicpages + 1;
			}
		}
		if($topicpages > 6) {
			$pagelinks .= " .. <a href=\"viewthread.php?tid=$thread[tid]&page=$topicpages\" target=\"_blank\">$topicpages</a> ";
		}
		$thread['multipage'] = " &nbsp; ( <img src=\"".IMGDIR."/multipage.gif\" align=\"absmiddle\" border=\"0\"> $pagelinks)";
	} else {
		$thread['multipage'] = '';
	}

	if($thread['highlight']) {
		$string = sprintf('%02d', $thread['highlight']);
		$stylestr = sprintf('%03b', $string[0]);

		$thread['highlight'] = 'style="';
		$thread['highlight'] .= $stylestr[0] ? 'font-weight: bold;' : '';
		$thread['highlight'] .= $stylestr[1] ? 'font-style: italic;' : '';
		$thread['highlight'] .= $stylestr[2] ? 'text-decoration: underline;' : '';
		$thread['highlight'] .= $string[1] ? 'color: '.$colorarray[$string[1]] : '';
		$thread['highlight'] .= '"';
	} else {
		$thread['highlight'] = '';
	}

	if($thread['attachment']) {
		require_once DISCUZ_ROOT.'./include/attachment.func.php';
		$thread['attachment'] = attachtype($thread['attachment']).' ';
	} else {
		$thread['attachment'] = '';
	}

	return $thread;
}

function updateviews($table, $idcol, $viewscol, $logfile) {
	global $db, $tablepre;

	$viewlog = $viewarray = array();
	if(@$viewlog = file($logfile = DISCUZ_ROOT.$logfile)) {
		@unlink($logfile);
		$viewlog = array_count_values($viewlog);
		foreach($viewlog as $id => $views) {
			$viewarray[$views] .= ($id > 0) ? ','.intval($id) : '';
		}
		foreach($viewarray as $views => $ids) {
			$db->query("UPDATE $tablepre$table SET $viewscol=$viewscol+$views WHERE $idcol IN (0$ids)", 'UNBUFFERED');
		}
	}
}

function modlog($thread, $action) {
	global $discuz_user, $adminid, $onlineip, $timestamp, $forum, $reason;
	@$fp = fopen(DISCUZ_ROOT.'./forumdata/modslog.php', 'a');
	@flock($fp, 2);
	@fwrite($fp, "$timestamp\t".dhtmlspecialchars($discuz_user)."\t$adminid\t$onlineip\t$forum[fid]\t$forum[name]\t$thread[tid]\t$thread[subject]\t$action\t".dhtmlspecialchars($reason)."\n");
	@fclose($fp);
}

function checkreasonpm() {
	if(($GLOBALS['reasonpm'] == 1 || $GLOBALS['reasonpm'] == 3) && !trim($GLOBALS['reason'])) {
		showmessage('admin_reason_invalid');
	}
}

function sendreasonpm($var, $item) {
	global $$var;
	${$var}['subject'] = strtr(${$var}['subject'], array_flip(get_html_translation_table(HTML_ENTITIES)));
	${$var}['dateline'] = gmdate($GLOBALS['_DCACHE']['settings']['dateformat'].' '.$GLOBALS['_DCACHE']['settings']['timeformat'], ${$var}['dateline'] + ($GLOBALS['timeoffset'] * 3600));
	sendpm(${$var}['authorid'], $item.'_subject', $item.'_message');
}

function logincheck() {
	/* return value
		1=nonexistence;
		2=within limitation;
		3=record expired
	*/

	global $db, $tablepre, $onlineip, $timestamp;
	$query = $db->query("SELECT count, lastupdate FROM {$tablepre}failedlogins WHERE ip='$onlineip'");
	if($login = $db->fetch_array($query)) {
		if($timestamp - $login['lastupdate'] > 900) {
			return 3;
		} elseif($login['count'] < 5) {
			return 2;
		} else {
			return 0;
		}
	} else {
		return 1;
	}
}

function loginfailed($permission) {
	global $db, $tablepre, $onlineip, $timestamp;
	switch($permission) {
		case 1:	$db->query("REPLACE INTO {$tablepre}failedlogins (ip, count, lastupdate)
				VALUES ('$onlineip', '1', '$timestamp')");
			break;
		case 2: $db->query("UPDATE {$tablepre}failedlogins SET count=count+1, lastupdate='$timestamp' WHERE ip='$onlineip'");
			break;
		case 3: $db->query("UPDATE {$tablepre}failedlogins SET count='1', lastupdate='$timestamp' WHERE ip='$onlineip'");
			$db->query("DELETE FROM {$tablepre}failedlogins WHERE lastupdate<$timestamp-901", 'UNBUFFERED');
			break;
	}
}

?>