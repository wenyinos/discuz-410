<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: relatethread.php,v $
	$Revision: 1.1.2.1 $
	$Date: 2006/03/23 02:45:01 $
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);
set_magic_quotes_runtime(0);

define('DISCUZ_ROOT', './');
define('IN_DISCUZ', TRUE);

require_once './forumdata/cache/cache_settings.php';

if(!$_DCACHE['settings']['qihoo_status'] || !$_DCACHE['settings']['qihoo_relatedthreads']) {
	exit;
}

$_SERVER = empty($_SERVER) ? $HTTP_SERVER_VARS : $_SERVER;
$_GET = empty($_GET) ? $HTTP_GET_VARS : $_GET;

$site = preg_replace("/.*?([^\.\/]+)(\.(com|net|org|gov|edu))?\.[^\.\/]+$/", "\\1", $_SERVER['HTTP_HOST']);
$subjectenc = rawurlencode($_GET['subjectenc']);
$tid = intval($_GET['tid']);

require_once './config.inc.php';
if($_GET['verifykey'] <> md5($_DCACHE['settings']['authkey'].$tid.$subjectenc.$charset.$site)) {
	exit;
}

$data = @implode('', file("http://search.qihoo.com/sint/discuz.html?title=$subjectenc&ocs=$charset&site=$site"));

if($data) {
	$qihoo_validity = $_DCACHE['settings']['qihoo_validity'];
	$qihoo_relatedthreads =  $_DCACHE['settings']['qihoo_relatedthreads'];
	$timestamp = time();
	$chs = '';

	if(PHP_VERSION > '5' && $charset != 'utf-8') {
		require_once DISCUZ_ROOT.'./include/chinese.class.php';
		$chs = new Chinese('utf-8', $charset);
	}

	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $values, $index);
	xml_parser_free($parser);

	$xmldata = array('chanl', 'fid', 'title', 'tid', 'author', 'pdate', 'rdate', 'rnum', 'vnum');
	$relatedthreadlist = $keywords = array();

	foreach($index as $tag => $valuearray) {
		if(in_array($tag, $xmldata)) {
			foreach($valuearray as $key => $value) {
				if($qihoo_relatedthreads > $key) {
					$relatedthreadlist[$key][$tag] = !empty($chs) ? $chs->convert(trim($values[$value]['value'])) : trim($values[$value]['value']);
				}
			}
		} elseif($tag == 'kw') {
			foreach($valuearray as $value) {
				$keywords[] = !empty($chs) ? $chs->convert(trim($values[$value]['value'])) : trim($values[$value]['value']);
			}
		} elseif($tag == 'svalid') {
			$svalid = $values[$index['svalid'][0]]['value'];
		}
	}

	if($keywords) {
		$searchkeywords = rawurlencode(implode(' ', $keywords));
		foreach($keywords as $keyword) {
			$relatedkeywords .= '<a href="search.php?srchtype=qihoo&srchtxt='.rawurlencode($keyword).'&searchsubmit=yes" target="_blank"><span class="bold"><font color="red">'.$keyword.'</font></span></a>&nbsp;';
		}
	}

	$keywords = $keywords ? implode("\t", $keywords) : '';
	$relatedthreads = $relatedthreadlist ? addslashes(serialize($relatedthreadlist)) : '';
	$svalid = $svalid > $qihoo_validity * 86400 ? $svalid : $qihoo_validity * 86400;
	$expiration = $timestamp + $svalid;

	require_once './include/db_'.$database.'.class.php';
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);
	unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

	$db->query("REPLACE INTO {$tablepre}relatedthreads (tid, expiration, keywords, relatedthreads)
		VALUES ('$tid', '$expiration', '$keywords', '$relatedthreads')", 'UNBUFFERED');
}

?>