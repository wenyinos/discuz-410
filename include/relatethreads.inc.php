<?
/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: relatethreads.inc.php,v $
	$Revision: 1.10 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$site = site();
$data = @implode('', file("http://search.qihoo.com/sint/discuz.html?title=$thread[subjectenc]&ocs=$charset&site=$site"));

if($data) {

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
	$db->query("REPLACE INTO {$tablepre}relatedthreads (tid, expiration, keywords, relatedthreads)
		VALUES ('$tid', '$expiration', '$keywords', '$relatedthreads')", 'UNBUFFERED');

}

?>