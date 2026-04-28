<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: javascript.php,v $
	$Revision: 1.5 $
	$Date: 2006/02/23 13:44:02 $
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('IN_DISCUZ', TRUE);
define('DISCUZ_ROOT', '../');

if(PHP_VERSION < '4.1.0') {
	$_GET		=	&$HTTP_GET_VARS;
	$_SERVER	=	&$HTTP_SERVER_VARS;
}

require_once DISCUZ_ROOT.'./config.inc.php';
require_once DISCUZ_ROOT.'./include/global.func.php';
require_once DISCUZ_ROOT.'./include/db_'.$database.'.class.php';
require_once DISCUZ_ROOT.'./forumdata/cache/cache_settings.php';

if($_DCACHE['settings']['gzipcompress']) {
	ob_start('ob_gzhandler');
}

$jsstatus		=	isset($_DCACHE['settings']['jsstatus']) ? $_DCACHE['settings']['jsstatus'] : 1;

if(!$jsstatus) {
	exit("document.write(\"<font color=red>The webmaster did not enable this feature.</font>\");");
}

$jsrefdomains	=	isset($_DCACHE['settings']['jsrefdomains']) ? $_DCACHE['settings']['jsrefdomains'] 
					: preg_replace("/([^\:]+).*/", "\\1", (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : NULL));
$REFERER	=	preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL));
if ($jsrefdomains && (empty($REFERER) | !in_array($REFERER,explode("\r\n",trim($jsrefdomains))))) {
	exit("document.write(\"<font color=red>Referer restriction is taking effect.</font>\");");
}

$authkey	=	isset($_DCACHE['settings']['authkey']) ? $_DCACHE['settings']['authkey'] : '';
$jsurl		=	preg_replace("/^(.+?)\&verify\=[0-9a-f]{32}$/", "\\1", $_SERVER['QUERY_STRING']);
$verify		=	isset($_GET['verify']) ? $_GET['verify'] : NULL;
if (!$verify || !$jsurl || $verify != md5($authkey.$jsurl)) {
	exit("document.write(\"<font color=red>Authentication failed.</font>\");");
}

$timestamp	=	time();
$jscachelife=	isset($_DCACHE['settings']['jscachelife']) ? $_DCACHE['settings']['jscachelife'] : 3600;
$dateformat	=	isset($_DCACHE['settings']['dateformat']) ? $_DCACHE['settings']['dateformat'] : 'm/d';
$timeformat	=	isset($_DCACHE['settings']['timeformat']) ? $_DCACHE['settings']['timeformat'] : 'H:i';
$PHP_SELF	=	$_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
$boardurl	=	'http://'.$_SERVER['HTTP_HOST'].preg_replace("/\/+(api|archiver|wap)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/';

$db = new dbstuff;
$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

$expiration	=	0;
$function	=	isset($_GET['function']) ? $_GET['function'] : NULL;
$fids		=	isset($_GET['fids']) ? $_GET['fids'] : NULL;
$startrow	=	isset($_GET['startrow']) ? intval($_GET['startrow']) : 0;
$items		=	isset($_GET['items']) ? intval($_GET['items']) : 10;
$digest		=	isset($_GET['digest']) ? intval($_GET['digest']) : 0;
$newwindow	=	isset($_GET['newwindow']) ? $_GET['newwindow'] : 1;
$LinkTarget	=	$newwindow ? " target='_blank'" : NULL;

if ($function == 'threads') {
	$orderby	=	isset($_GET['orderby']) ? (in_array($_GET['orderby'],array('lastpost','dateline','replies','views')) ? $_GET['orderby'] : 'lastpost') : 'lastpost';
	$highlight	=	isset($_GET['highlight']) ? $_GET['highlight'] : 0;
	$forum		=	isset($_GET['forum']) ? $_GET['forum'] : 0;
	$author		=	isset($_GET['author']) ? $_GET['author'] : 0;
	$dateline	=	isset($_GET['dateline']) ? $_GET['dateline'] : 0;
	$picpre		=	isset($_GET['picpre']) ? urldecode($_GET['picpre']) : NULL;
	$maxlength	=	isset($_GET['maxlength']) ? intval($_GET['maxlength']) : 50;
	$cachefile	=	DISCUZ_ROOT.'./forumdata/cache/javascript_'.md5("threads|$fids|$startrow|$items|$digest|$orderby").'.php';
	if((@!include($cachefile)) || $expiration < $timestamp) {
		require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';
		$datalist = array();
		$sql	=	($fids ? ' AND `fid` IN (\''.str_replace('_', '\',\'', $fids).'\')' : '')
					.(($digest > 0 && $digest < 15) ? DigestLevel($digest) : '');
		$query	=	$db->query("SELECT `tid`,`fid`,`readperm`,`author`,`authorid`,`subject`,`dateline`,`lastpost`,`views`,`replies`,`highlight`,`digest`
					FROM `{$tablepre}threads`
					WHERE `readperm`='0'
					$sql
					AND `displayorder`>='0'
					AND `fid`>'0'
					ORDER BY `$orderby` DESC
					LIMIT $startrow,$items;"
					);
		while($data = $db->fetch_array($query))	{
			$datalist[$data['tid']]['fid']			=	$data['fid'];
			$datalist[$data['tid']]['fname']		=	isset($_DCACHE['forums'][$data['fid']]['name']) ? str_replace('\'', '&nbsp;',addslashes($_DCACHE['forums'][$data['fid']]['name'])) : NULL;
			$datalist[$data['tid']]['fnamelength']	=	strlen($datalist[$data['tid']]['fname']);
			$datalist[$data['tid']]['subject']		=	isset($data['subject']) ? str_replace('\'', '&nbsp;',addslashes($data['subject'])) : NULL;
			$datalist[$data['tid']]['dateline']		=	date("$dateformat $timeformat",$data['dateline']);
			$datalist[$data['tid']]['lastpost']		=	date("$dateformat $timeformat",$data['lastpost']);
			$datalist[$data['tid']]['views']		=	$data['views'];
			$datalist[$data['tid']]['replies']		=	$data['replies'];
			$datalist[$data['tid']]['time']			=	$orderby != 'dateline' ? $datalist[$data['tid']]['lastpost'] : $datalist[$data['tid']]['dateline'];
			$datalist[$data['tid']]['highlight']	=	$data['highlight'];
			if($data['author']) {
				$datalist[$data['tid']]['author'] = $data['author'];
				$datalist[$data['tid']]['authorid'] = $data['authorid'];
			} else {
				$datalist[$data['tid']]['author'] = 'Anonymous';
				$datalist[$data['tid']]['authorid'] = 0; 
			}
		}
		$writedata = "\$datalist = unserialize('".addcslashes(serialize($datalist), '\\\'')."');";
		UpdateCache($cachefile,$writedata);
	}
	if (is_array($datalist)) {
		$colorarray	=	array('', 'red', 'orange', 'yellow', 'green', 'cyan', 'blue', 'purple', 'gray');
		$prefix		=	$picpre ? "<img src='$picpre' border='0' align='absmiddle'>" : NULL;
		foreach ($datalist AS $tid=>$value) {
			$SubjectStyles	=	'';
			if ($highlight && $value['highlight']) {
				$string			= sprintf('%02d', $value['highlight']);
				$stylestr		= sprintf('%03b', $string[0]);
				$SubjectStyles	.= " style='";
				$SubjectStyles	.= $stylestr[0] ? 'font-weight: bold;' : NULL;
				$SubjectStyles	.= $stylestr[1] ? 'font-style: italic;' : NULL;
				$SubjectStyles	.= $stylestr[2] ? 'text-decoration: underline;' : NULL;
				$SubjectStyles	.= $string[1] ? 'color: '.$colorarray[$string[1]] : NULL;
				$SubjectStyles	.= "'";
			}
			echo "document.writeln(\"$prefix&nbsp;"
				.($forum ? "<a href='".$boardurl."forumdisplay.php?fid=$value[fid]'$LinkTarget>$value[fname]</a>&nbsp;" : NULL)
				.($dateline ? "$value[time]&nbsp;" : NULL)
				."<a href='".$boardurl."viewthread.php?tid=$tid' title='$value[subject]'$SubjectStyles$LinkTarget>"
				.(cutstr($value['subject'],($forum ? ($maxlength - $value['fnamelength']) : $maxlength)))
				."</a>"
				.($author ? "&nbsp;(<a href='".$boardurl."viewpro.php?uid=$value[authorid]'$LinkTarget>$value[author]</a>)" : NULL)
				."<br />\");\r\n";
		}
	}

} elseif ($function == 'forums') {
	//论坛列表
	$fups		=	isset($_GET['fups']) ? $_GET['fups'] : NULL;
	$orderby	=	isset($_GET['orderby']) ? (in_array($_GET['orderby'],array('displayorder','threads','posts')) ? $_GET['orderby'] : 'displayorder') : 'displayorder';
	$cachefile	=	DISCUZ_ROOT.'./forumdata/cache/javascript_'.md5("forums|$fups|$startrow|$items|$orderby").'.php';
	if((@!include($cachefile)) || $expiration < $timestamp) {
		$datalist = array();
		$query	=	$db->query("SELECT `fid`,`fup`,`name`,`status`,`threads`,`posts`,`displayorder`,`type` 
					FROM `{$tablepre}forums`
					WHERE `type`!='group'
					".($fups ? "AND `fup` IN ('".str_replace('_', '\',\'', $fups)."') " : "")."
					AND `status`='1' 
					ORDER BY ".($orderby == 'displayorder' ? " `displayorder` ASC " : " `$orderby` DESC")." 
					LIMIT $startrow,".($items > 0 ? $items : 65535).";"
					);
		while ($data = $db->fetch_array($query)) {
			$datalist[$data['fid']] = str_replace('\'', '&nbsp;',addslashes($data['name']));
		}
		$writedata = "\$datalist = unserialize('".addcslashes(serialize($datalist), '\\\'')."');";
		UpdateCache($cachefile,$writedata);
	}
	//读出并显示
	if (is_array($datalist)) {
		foreach ($datalist AS $fid=>$name) {
			echo "document.writeln(\"<a href='".$boardurl."forumdisplay.php?fid=$fid'$LinkTarget>$name</a><br />\");\r\n";
		}
	}
} elseif ($function == 'memberrank') {
	//会员排行
	$orderby	=	isset($_GET['orderby']) ? $_GET['orderby'] : 'credits';
	$cachefile	=	DISCUZ_ROOT.'./forumdata/cache/javascript_'.md5("memberrank|$startrow|$items|$orderby").'.php';
	if((@!include($cachefile)) || $expiration < $timestamp) {
		$datalist = array();
		switch ($orderby) {
			case 'credits':
				$sql = "SELECT `username`,`uid`,`credits` FROM `{$tablepre}members` ORDER BY `credits` DESC";
			break;
			case 'posts':
				$sql = "SELECT `username`,`uid`,`posts` FROM `{$tablepre}members` ORDER BY `posts` DESC";
			break;
			case 'digestposts':
				$sql = "SELECT `username`,`uid`,`digestposts` FROM `{$tablepre}members` ORDER BY `digestposts` DESC";
			break;
			case 'regdate':
				$sql = "SELECT `username`,`uid`,`regdate` FROM `{$tablepre}members` ORDER BY `regdate` DESC";
			break;
			case 'todayposts':
				$sql = "SELECT DISTINCT(author) AS username,authorid AS uid,COUNT(pid) AS postnum FROM `{$tablepre}posts` WHERE `dateline` >= ".($timestamp - 86400)." AND `authorid`!='0' GROUP BY `author` ORDER BY `postnum` DESC";
			break;
		}
		$query = $db->query($sql." LIMIT $startrow,$items;");
		while ($data = $db->fetch_array($query,MYSQL_NUM)) {
			$data[2] = $orderby == 'regdate' ? date($timeformat,$data[2]) : $data[2];
			$datalist[] = $data;
		}
		$writedata = "\$datalist = unserialize('".addcslashes(serialize($datalist), '\\\'')."');";
		UpdateCache($cachefile,$writedata);
	}
	//读出并显示
	if (is_array($datalist)) {
		if ($orderby == 'regdate') {
			foreach ($datalist AS $value) {
				echo "document.writeln(\"($value[2])&nbsp;<a href='".$boardurl."viewpro.php?uid=$value[1]'$LinkTarget>$value[0]</a><br />\");\r\n";
			}
		} else {
			foreach ($datalist AS $value) {
				echo "document.writeln(\"<a href='".$boardurl."viewpro.php?uid=$value[1]'$LinkTarget>$value[0]</a>&nbsp;($value[2])<br />\");\r\n";
			}
		}
	}
} elseif ($function == 'stats') {
	//论坛统计
	$info = isset($_GET['info']) ? $_GET['info'] : NULL;
	$language = $info;
	if (is_array($info)) {
		$info_index = '';
		$cachefile = DISCUZ_ROOT.'./forumdata/cache/javascript_'.md5("stats|forums|threads|posts|members|online|onlinemembers").'.php';
		if((@!include($cachefile)) || $expiration < $timestamp) {
			$statsinfo = array();
			$statsinfo['forums'] = $statsinfo['threads'] = $statsinfo['posts'] = 0;
			$query = $db->query("SELECT `status`,`threads`,`posts` 
					FROM `{$tablepre}forums` WHERE 
					`status`='1';
					");
			while($forumlist = $db->fetch_array($query)) {
				//forums论坛数、threads主题数、posts帖子数
				$statsinfo['forums']++;
				$statsinfo['threads'] += $forumlist['threads'];
				$statsinfo['posts'] += $forumlist['posts'];
			}
			unset($info['forums'],$info['threads'],$info['posts']);
			foreach ($info AS $index=>$value) {
				//members会员数、online在线人数、onlinemembers在线会员数
				if ($index == 'members') {
					$sql = "SELECT COUNT(*) FROM `{$tablepre}members`;";
				} elseif ($index == 'online') {
					$sql = "SELECT COUNT(*) FROM `{$tablepre}sessions`;";
				} elseif ($index == 'onlinemembers') {
					$sql = "SELECT COUNT(*) FROM `{$tablepre}sessions` WHERE `uid`>'0';";
				}
				if ($index == 'members' || $index == 'online' || $index == 'onlinemembers') {
					$query = $db->query($sql);
					$statsinfo[$index] = $db->result($query, 0);
				}
			}
			unset($index,$value);
			$writedata = "\$statsinfo = unserialize('".addcslashes(serialize($statsinfo), '\\\'')."');";
			UpdateCache($cachefile,$writedata);
		}
		//读出数据并显示
		foreach ($language AS $index=>$value) {
			echo "document.write(\"$value$statsinfo[$index]<br />\");\r\n";
		}
	}
} elseif ($function == 'images') {
	//附件图片调用
	$maxwidth	=	isset($_GET['maxwidth']) ? $_GET['maxwidth'] : 0;
	$maxheight	=	isset($_GET['maxheight']) ? $_GET['maxheight'] : 0;
	$cachefile	=	DISCUZ_ROOT.'./forumdata/cache/javascript_'.md5("images|$fids|$startrow|$items|$digest").'.php';
	if((@!include($cachefile)) || $expiration < $timestamp) {
		require_once DISCUZ_ROOT.'./forumdata/cache/cache_forums.php';
		$datalist	=	array();
		$sql		=	($fids ? ' AND `fid` IN (\''.str_replace('_', '\',\'', $fids).'\')' : '')
					.(($digest > 0 && $digest < 15) ? DigestLevel($digest) : '');

		$query		=	$db->query("SELECT attach.*,t.tid,t.fid,t.digest,t.author,t.dateline,t.subject,t.displayorder
						FROM `{$tablepre}attachments` attach
						LEFT JOIN `{$tablepre}threads` t 
						ON `t`.`tid`=`attach`.`tid`
						WHERE `attach`.`readperm`='0' 
						AND `displayorder`>='0'
						AND `filetype` LIKE '%image/%'
						$sql
						GROUP BY `attach`.`tid`
						ORDER BY `attach`.`dateline` DESC,`attach`.`tid` DESC
						LIMIT $startrow,$items;"
						);
		while ($data = $db->fetch_array($query)) {
			$datalist[$data['tid']]['threadlink']	=	$boardurl."viewthread.php?tid=$data[tid]";
			$datalist[$data['tid']]['imgfile']		=	$boardurl."$attachdir/$data[attachment]";
			$datalist[$data['tid']]['subject']		=	str_replace('\'', '&nbsp;',$data['subject']);
			$datalist[$data['tid']]['author']		=	addslashes($data['author']);
			$datalist[$data['tid']]['dateline']		=	date("$dateformat $timeformat",$data['dateline']);
			$datalist[$data['tid']]['fname']		=	isset($_DCACHE['forums'][$data['fid']]['name']) ? str_replace('\'', '&nbsp;',addslashes($_DCACHE['forums'][$data['fid']]['name'])) : NULL;
			$datalist[$data['tid']]['description']	=	$data['description'] ? str_replace('\'', '&nbsp;',addslashes($data['description'])) : NULL;
		}
		$writedata = "\$datalist = unserialize('".addcslashes(serialize($datalist), '\\\'')."');";
		UpdateCache($cachefile,$writedata);
	}
	//读出数据并显示
	if (is_array($datalist)) {
		$imgsize	=	($maxwidth ? " width='$maxwidth'" : NULL)
					.($maxheight ? " height='$maxheight'" : NULL);
		foreach ($datalist AS $value) {
			echo "document.write(\"<a href='$value[threadlink]'$LinkTarget><img$imgsize src='$value[imgfile]' border='0' alt='"
				.($value['description'] ? "$value[description]&#13&#10" : NULL)
				."$value[subject]&#13&#10$value[author]($value[dateline])&#13&#10$value[fname]' /></a>\");\r\n";
		}
	}
} else {
	exit("document.write(\"<font color=red>Undefined action.</font>\");");
}

function UpdateCache($cachfile,$data='') {
	//写入缓存
	global $timestamp,$jscachelife;
	if(!$fp = @fopen($cachfile, 'wb')) {
		exit("document.write(\"Unable to write to cache file!<br>Please chmod ./forumdata/cache to 777 and try again.\");");
	}
	$fp = @fopen($cachfile, 'wb');
	@fwrite($fp, "<?php\r\nif(!defined('IN_DISCUZ')) exit('Access Denied');\r\n\$expiration='".($timestamp + $jscachelife)."';\r\n".$data."\r\n?>");
	@fclose($fp);
}

function DigestLevel($digest) {
	$digest	=	intval($digest);
	$digest	=	sprintf("%04d", decbin($digest));
	$digest	=	"$digest";
	$digest_filed	=	'';
	$digest_filed	.=	$digest[0] == 1 ? 1 : '';
	$digest_filed	.=	$digest[1] == 1 ? 2 : '';
	$digest_filed	.=	$digest[2] == 1 ? 3 : '';
	$digest_filed	.=	$digest[3] == 1 ? 0 : '';
	return ' AND `digest` IN (\''.str_replace('_', '\',\'', substr(chunk_split($digest_filed,1,"_"),0,-1)).'\')';
}
?>