<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: cron.func.php,v $
	$Revision: 1.10 $
	$Date: 2006/02/28 06:52:16 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once DISCUZ_ROOT.'./include/cache.func.php';
@include_once DISCUZ_ROOT.'./forumdata/cache/cache_crons.php';

function runcron($cronid = 0) {
	extract($GLOBALS, EXTR_SKIP);

	@set_time_limit(1000);
	@ignore_user_abort(TRUE);

	$cronids = array();
	$crons = $cronid ? array($cronid => $_DCACHE['crons'][$cronid]) : $_DCACHE['crons'];

	if(empty($crons) || !is_array($crons)) return;

	foreach($crons as $id => $cron) {
		if($cron['nextrun'] <= $timestamp || $id == $cronid) {
			$cronids[] = $id;
			if(!include_once DISCUZ_ROOT.($cronfile = "./include/crons/$cron[filename]")) {
				errorlog('CRON', $cron['name']." : Cron script($cronfile) not found or syntax error", 0);
			}
		}
	}

	cronnextrun($cronids);
}

function cronnextrun($cronids) {
	global $db, $tablepre, $_DCACHE, $timestamp;

	if(!is_array($cronids) || !$cronids) {
		return false;
	}

	$minutenow = gmdate('i', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
	$hournow = gmdate('H', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
	$daynow = gmdate('d', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
	$monthnow = gmdate('m', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
	$yearnow = gmdate('Y', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
	$weekdaynow = gmdate('w', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);

	foreach($cronids as $cronid) {
		if(!$cron = $_DCACHE['crons'][$cronid]) {
			continue;
		}

		if($cron['weekday'] == -1) {
			if($cron['day'] == -1) {
				$firstday = $daynow;
				$secondday = $daynow + 1;
			} else {
				$firstday = $cron['day'];
				$secondday = $cron['day'] + gmdate('t', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600);
			}
		} else {
			$firstday = $daynow + ($cron['weekday'] - $weekdaynow);
			$secondday = $firstday + 7;
		}

		if($firstday < $daynow) {
			$firstday = $secondday;
		}

		if($firstday == $daynow) {
			$todaytime = crontodaynextrun($cron);
			if($todaytime['hour'] == -1 && $todaytime['minute'] == -1) {
				$cron['day'] = $secondday;
				$nexttime = crontodaynextrun($cron, 0, -1);
				$cron['hour'] = $nexttime['hour'];
				$cron['minute'] = $nexttime['minute'];
			} else {
				$cron['day'] = $firstday;
				$cron['hour'] = $todaytime['hour'];
				$cron['minute'] = $todaytime['minute'];
			}
		} else {
			$cron['day'] = $firstday;
			$nexttime = crontodaynextrun($cron, 0, -1);
			$cron['hour'] = $nexttime['hour'];
			$cron['minute'] = $nexttime['minute'];
		}

		$nextrun = gmmktime($cron['hour'], $cron['minute'], 0, $monthnow, $cron['day'], $yearnow) - $_DCACHE['settings']['timeoffset'] * 3600;
		$db->query("UPDATE {$tablepre}crons SET lastrun='$timestamp', nextrun='$nextrun' WHERE cronid='$cronid'"); 
	}

	$query = $db->query("SELECT nextrun FROM {$tablepre}crons WHERE available>'0' AND nextrun>'$timestamp' ORDER BY nextrun LIMIT 1");
	$_DCACHE['settings']['cronnextrun'] = $db->result($query, 0);

	updatecache('crons');
	updatesettings();

}

function crontodaynextrun($cron, $hour = -2, $minute = -2) {
	global $timestamp, $_DCACHE;

	$hour = $hour == -2 ? gmdate('H', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600) : $hour;
	$minute = $minute == -2 ? gmdate('i', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600) : $minute;

	$nexttime = array();
	if($cron['hour'] == -1 && !$cron['minute']) {
		$nexttime['hour'] = $hour;
		$nexttime['minute'] = $minute + 1;
	} elseif($cron['hour'] == -1 && $cron['minute'] != '') {
		$nexttime['hour'] = $hour;
		if(($nextminute = cronnextminute($cron['minute'], $minute)) === false) {
			++$nexttime['hour'];
			$nextminute = $cron['minute'][0];
		}
		$nexttime['minute'] = $nextminute;
	} elseif($cron['hour'] != -1 && $cron['minute'] == '') {
		if($cron['hour'] < $hour) {
			$nexttime['hour'] = $nexttime['minute'] = -1;
		} else if ($cron['hour'] == $hour) {
			$nexttime['hour'] = $cron['hour'];
			$nexttime['minute'] = $minute + 1;
		} else {
			$nexttime['hour'] = $cron['hour'];
			$nexttime['minute'] = 0;
		}
	} elseif($cron['hour'] != -1 && $cron['minute'] != '') {
		$nextminute = cronnextminute($cron['minute'], $minute);
		if($cron['hour'] < $hour || ($cron['hour'] == $hour && $nextminute === false)) {
			$nexttime['hour'] = -1;
			$nexttime['minute'] = -1;
		} else {
			$nexttime['hour'] = $cron['hour'];
			$nexttime['minute'] = $nextminute;
		}
	}

	return $nexttime;
}

function cronnextminute($nextminutes, $minutenow) {
	foreach($nextminutes as $nextminute) {
		if($nextminute > $minutenow) {
			return $nextminute;
		}
	}
	return false;
}

?>