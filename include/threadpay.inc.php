<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: threadpay.inc.php,v $
	$Revision: 1.5 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(!isset($extcredits[$creditstrans])) {
	showmessage('credits_transaction_disabled');
}

if($thread['rate']) {
	$query = $db->query("SELECT pid FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' AND first='1'");
	$pid = $db->result($query, 0);
}

$query = $db->query("SELECT COUNT(*) AS payers, SUM(netamount) AS income FROM {$tablepre}paymentlog WHERE tid='$tid'");
$payment = $db->fetch_array($query);

$thread['payers'] = $payment['payers'];
$thread['netprice'] = !$maxincperthread || ($maxincperthread && $payment['income'] < $maxincperthread) ? floor($thread['price'] * (1 - $creditstax)) : 0;
$thread['creditstax'] = sprintf('%1.2f', $creditstax * 100).'%';
$thread['endtime'] = $maxchargespan ? gmdate("$dateformat $timeformat", $timestamp + $maxchargespan * 3600 + $timeoffset * 3600) : 0;

$query = $db->query("SELECT * FROM {$tablepre}posts WHERE tid='$tid' AND first='1' LIMIT 1");
$firstpost = $db->fetch_array($query);
if(preg_match("/\[free\].+?\[\/free\]/is", $firstpost['message'])) {
	$thread['freemessage'] = discuzcode(preg_replace("/.*\[free\](.+?)\[\/free\].*/is", "\\1", $firstpost['message']), $firstpost['smileyoff'], $firstpost['bbcodeoff'], $firstpost['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], 0);
}

if($thread['replies'] >= 1) {
	include_once language('misc');

	$postlist = array();
	$query = $db->query("SELECT * FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' AND first='0' ORDER BY dateline DESC LIMIT 10");
	while($post = $db->fetch_array($query)) {
		$post['thisbg'] = $thisbg = isset($thisbg) && $thisbg == 'altbg1' ? 'altbg2' : 'altbg1';
		$post['dateline'] = gmdate("$dateformat $timeformat", $post['dateline'] + $timeoffset * 3600);;
		$post['message'] = preg_replace("/\[hide=?\d*\](.+?)\[\/hide\]/is", "[b]$language[post_hidden][/b]", $post['message']);
		$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'], $forum['allowsmilies'], $forum['allowbbcode'], $forum['allowimgcode'], $forum['allowhtml'], $forum['jammer']);

		$postlist[] = $post;
	}
}

if($postlist) {
	$postlist = array_reverse($postlist);
}

include template('viewthread_pay');
dexit();

?>