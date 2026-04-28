<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: faq.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

require_once './include/common.inc.php';

$discuz_action = 51;

if(empty($page)) {

	@include language('customfaq');
	include template('faq');

} elseif($page == 'usermaint') {

	include template('faq_usermaint');

} elseif($page == 'using') {

	include template('faq_using');

} elseif($page == 'messages') {

	$smilies = array();
	$query = $db->query("SELECT code, url FROM {$tablepre}smilies WHERE type='smiley'");
	while($smiley = $db->fetch_array($query)) {
		$smilies[] = $smiley;
	}

	include template('faq_messages');

} elseif($page == 'misc') {

	$discuzcodes = array();
	$query = $db->query("SELECT * FROM {$tablepre}bbcodes WHERE available='1'");
	while($discuzcode = $db->fetch_array($query)) {
		$discuzcode['explanation'] = htmlspecialchars($discuzcode['explanation']);
		$discuzcodes[] = $discuzcode;
	}

	include template('faq_misc');

} elseif($page == 'custom') {

	@include language('customfaq');
	if(is_array($customfaq)) {
		for($i = 0; $i < count($customfaq['item']); $i++) {
			$customfaq['item'][$i]['message'] = str_replace('  ', '&nbsp; ', nl2br($customfaq['item'][$i]['message']));
		}
	}
	include template('faq_custom');

}

?>