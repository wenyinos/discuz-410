<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: advertisements.inc.php,v $
	$Revision: 1.4 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(defined('CURSCRIPT') && CURSCRIPT == 'index') {
	$key = 'index';
} elseif(defined('CURSCRIPT') && in_array(CURSCRIPT, array('forumdisplay', 'viewthread')) && !empty($fid)) {
	$key = 'forum_'.$fid;
} else {
	$key = 'all';
}

$advarray = array();
if(is_array($advertisements['types'])) {
	foreach($advertisements['types'] as $advtype => $advitems) {
		foreach(($key == 'all' ? (!empty($advitems['all']) ? $advitems['all'] : array()) :
			array_unique(array_merge((!empty($advitems[$key]) ? $advitems[$key] : array()), (!empty($advitems['all']) ? $advitems['all'] : array()))))
			as $item) {
			if(isset($advertisements['items'][$item])) {
				$advarray[$advtype][] = $advertisements['items'][$item];
			}
		}
	}
}

foreach($advarray as $advtype => $advcodes) {
	$advcount = count($advcodes);
	if($advtype == 'text') {
		if($advcount > 5) {
			$minfillpercent = 0;
			for($cols = 5; $cols >= 3; $cols--) {
				if(($remainder = $advcount % $cols) == 0) {
					$advcols = $cols;
					break;
				} elseif($remainder / $cols > $minfillpercent)  {
					$minfillpercent = $remainder / $cols;
					$advcols = $cols;
				}
			}
		} else {
			$advcols = $advcount;
		}

		$advlist[$advtype] = '';
		for($i = 0; $i < $advcols * ceil($advcount / $advcols); $i++) {
			$advlist[$advtype] .= (($i + 1) % $advcols == 1 || $advcols == 1 ? '<tr align="center" class="altbg2">' : '').
				'<td width="'.intval(100 / $advcols).'%">'.(isset($advcodes[$i]) ? $advcodes[$i] : '&nbsp;').'</td>'.
				(($i + 1) % $advcols == 0 ? "</tr>\n" : '');
		}
	} elseif($advtype == 'thread') {
		for($i = 0; $i < ($maxthreadads ? $maxthreadads : $ppp); $i++) {
			$advlist[$advtype][$i] = $advcodes[mt_rand(0, $advcount -1)];
		}
	} else {
		if($advcount > 1) {
			$advlist[$advtype] = $advcodes[mt_rand(0, $advcount - 1)];
		} else {
			$advlist[$advtype] = $advcodes[0];
		}
	}
}

?>