<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: post.func.php,v $
	$Revision: 1.9 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function attach_upload() {
	global $db, $tablepre, $extension, $typemaxsize, $allowsetattachperm, $attachperm, $attachdesc, $attachsave, $attachdir,
		$maxattachsize, $maxsizeperday, $attachextensions, $watermarkstatus, $watermarktrans, $watermarkquality, $_FILES;

	// watermark filename
	$watermark_file = './images/common/watermark.gif';
	$watermarkstatus = $GLOBALS['forum']['disablewatermark'] ? 0 : $watermarkstatus;

	$attachments = $attacharray = array();

	if(isset($_FILES['attach']) && is_array($_FILES['attach'])) {
		foreach($_FILES['attach'] as $key => $var) {
			foreach($var as $id => $val) {
				$attachments[$id][$key] = $val;
			}
		}
	}

	foreach($attachments as $key => $attach) {

		$attach_saved = false;

		if(!disuploadedfile($attach['tmp_name']) || !($attach['tmp_name'] != 'none' && $attach['tmp_name'] && $attach['name'])) {
			continue;
		}

		$filename = daddslashes($attach['name']);
		$attach['ext'] = $extension = strtolower(fileext($attach['name']));
		if(strlen($attach['name']) > 90) {
			$attach['name'] = 'abbr_'.substr($attach['name'], -80);
		}

		if($attachextensions && (!preg_match("/(^|\s|,)".preg_quote($attach['ext'], '/')."($|\s|,)/i", $attachextensions) || !$attach['ext'])) {
			upload_error('post_attachment_ext_notallowed', $attacharray);
		}

		if(empty($attach['size'])) {
			upload_error('post_attachment_size_invalid', $attacharray);
		}

		if($maxattachsize && $attach['size'] > $maxattachsize) {
			upload_error('post_attachment_toobig', $attacharray);
		}

		$query = $db->query("SELECT maxsize FROM {$tablepre}attachtypes WHERE extension='".addslashes($attach['ext'])."'");
		if($type = $db->fetch_array($query)) {
			if($type['maxsize'] == 0) {
				upload_error('post_attachment_ext_notallowed', $attacharray);
			} elseif($attach['size'] > $type['maxsize']) {
				require_once DISCUZ_ROOT.'./include/attachment.func.php';
				$typemaxsize = sizecount($type['maxsize']);
				upload_error('post_attachment_type_toobig', $attacharray);
			}
		}

		if($attach['size'] && $maxsizeperday) {
			if(!isset($todaysize)) {
				$query = $db->query("SELECT SUM(a.filesize) FROM {$tablepre}posts p
					LEFT JOIN {$tablepre}attachments a USING (pid)
					WHERE p.authorid='{$GLOBALS['discuz_uid']}' AND p.dateline>'{$GLOBALS['timestamp']}'-86400 AND p.attachment>'0'");
				$todaysize = intval($db->result($query, 0));
			}
			$todaysize += $attach['size'];
			if($todaysize >= $maxsizeperday) {
				upload_error('post_attachment_quota_exceed', $attacharray);
			}
		}

		if($attachsave) {
			switch($attachsave) {
				case 1: $attach_subdir = 'forumid_'.$GLOBALS['fid']; break;
				case 2: $attach_subdir = 'ext_'.$extension; break;
				case 3: $attach_subdir = 'month_'.date('ym'); break;
				case 4: $attach_subdir = 'day_'.date('ymd'); break;
			}
			$attach_dir = $attachdir.'/'.$attach_subdir;
			if(!is_dir($attach_dir)) {
				mkdir($attach_dir, 0777);
				fclose(fopen($attach_dir.'/index.htm', 'w'));
			}
			$attach['attachment'] = $attach_subdir.'/';
		} else {
			$attach['attachment'] = '';
		}

		$filename = substr($filename, 0, strlen($filename) - strlen($extension) - 1);
		if(preg_match("/[\x7f-\xff\%\'\"\(\)\#]+/", $filename) || strlen($filename) > 60) {
			$filename = str_replace('/', '', base64_encode(substr($filename, 0, 10)));
		}

		$attach['attachment'] .= preg_replace("/(php|phtml|php3|jsp|exe|dll|asp|aspx|cgi|fcgi|pl)(\.|$)/i", "_\\1\\2",
			substr($filename, 0, 64).'_'.random(12).'.'.$extension);

		$target = $attachdir.'/'.stripslashes($attach['attachment']);

		if(@copy($attach['tmp_name'], $target) || (function_exists('move_uploaded_file') && @move_uploaded_file($attach['tmp_name'], $target))) {
			@unlink($attach['tmp_name']);
			$attach_saved = true;
		}

		if(!$attach_saved && @is_readable($attach['tmp_name'])) {
			@$fp = fopen($attach['tmp_name'], 'rb');
			@flock($fp, 2);
			@$attachedfile = fread($fp, $attach['size']);
			@fclose($fp);

			@$fp = fopen($target, 'wb');
			@flock($fp, 2);
			if(@fwrite($fp, $attachedfile)) {
				@unlink($attach['tmp_name']);
				$attach_saved = true;
			}
			@fclose($fp);
		}

		if($attach_saved) {
			if(in_array($attach['ext'], array('jpg', 'jpeg', 'gif', 'png', 'swf', 'bmp')) && function_exists('getimagesize') && !@getimagesize($target)) {
				@unlink($target);
				upload_error('post_attachment_ext_notallowed', $attacharray);
			} else {
				if($watermarkstatus && in_array($attach['ext'], array('jpg', 'jpeg', 'gif', 'png')) && function_exists('getimagesize') && function_exists('imageCreateFromJPEG') && function_exists('imageCreateFromPNG') && function_exists('imageCopyMerge')) {
					$attachinfo	= getimagesize($target);
					$watermark_logo = imageCreateFromGIF($watermark_file);
					$logo_w		= imageSX($watermark_logo);
					$logo_h		= imageSY($watermark_logo);
					$img_w		= $attachinfo[0];
					$img_h		= $attachinfo[1];
					$wmwidth	= $img_w - $logo_w;
					$wmheight	= $img_h - $logo_h;

					$animatedgif = 0;
					if($attachinfo['mime'] == 'image/gif') {
						if(empty($attachedfile)) {
							$fp = fopen($target, 'rb');
							$attachedfile = fread($fp, $attach['size']);
							fclose($fp);
						}
						$animatedgif = strpos($attachedfile, 'NETSCAPE2.0') === FALSE ? 0 : 1;
					}
						
					if(is_readable($watermark_file) && $wmwidth > 10 && $wmheight > 10 && !$animatedgif) {
						switch ($attachinfo['mime']) {
							case 'image/jpeg':
								$dst_photo = imageCreateFromJPEG($target);
								break;
							case 'image/gif':
								$dst_photo = imageCreateFromGIF($target);
								break;
							case 'image/png':
								$dst_photo = imageCreateFromPNG($target);
								break;
						}

						switch($watermarkstatus) {
							case 1:
								$x = +5;
								$y = +5;
								break;
							case 2:
								$x = ($logo_w +	$img_w)	/ 2;
								$y = +5;
								break;
							case 3:
								$x = $img_w - $logo_w-5;
								$y = +5;
								break;
							case 4:
								$x = +5;
								$y = ($logo_h +	$img_h)	/ 2;
								break;
							case 5:
								$x = ($logo_w +	$img_w)	/ 2;
								$y = ($logo_h +	$img_h)	/ 2;
								break;
							case 6:
								$x = $img_w - $logo_w;
								$y = ($logo_h +	$img_h)	/ 2;
								break;
							case 7:
								$x = +5;
								$y = $img_h - $logo_h-5;
								break;
							case 8:
								$x = ($logo_w +	$img_w)	/ 2;
								$y = $img_h - $logo_h;
								break;
							case 9:
								$x = $img_w - $logo_w-5;
								$y = $img_h - $logo_h-5;
								break;
						}

						imageAlphaBlending($watermark_logo, true);
						imageCopyMerge($dst_photo, $watermark_logo, $x,	$y, 0, 0, $logo_w, $logo_h, $watermarktrans);

						switch($attachinfo['mime']) {
							case 'image/jpeg':
								imageJPEG($dst_photo, $target, $watermarkquality);
								break;
							case 'image/gif':
								imageGIF($dst_photo, $target);
								break;
							case 'image/png':
								imagePNG($dst_photo, $target);
								break;	      
						}

					}

					$attach['size'] = filesize($target);
				}

				$attach['perm'] = $allowsetattachperm ? $attachperm[$key] : 0;
				$attach['description'] = cutstr(dhtmlspecialchars($attachdesc[$key]), 100);
				$attacharray[$key] = $attach;
			}
		} else {

			upload_error('post_attachment_save_error', $attacharray);
		}
	}

	return !empty($attacharray) ? $attacharray : false;
}

function upload_error($message, $attacharray = array()) {
	if(!empty($attacharray)) {
		foreach($attacharray as $attach) {
			@unlink($GLOBALS['attachdir'].'/'.$attach['attachment']);
		}
	}
	showmessage($message);
}

function checkflood() {
	global $disablepostctrl, $floodctrl, $discuz_uid, $timestamp, $lastpost, $forum;
	if(!$disablepostctrl && $floodctrl) {
		if($discuz_uid) {
			if($timestamp - $floodctrl <= $lastpost) {
				return TRUE;
			}
		} else {
			$lastpost = explode("\t", $forum['lastpost']);
			if(($timestamp - $floodctrl) <= $lastpost[1] && $discuz_user == $lastpost[2]) {
				return TRUE;
			}
		}
	}
	return FALSE;
}

function checkpost() {
	global $subject, $message, $disablepostctrl, $minpostsize, $maxpostsize;
	if(strlen($subject) > 80) {
		return 'post_subject_toolang';
	}
	if(!$disablepostctrl) {
		if($maxpostsize && strlen($message) > $maxpostsize) {
			return 'post_message_toolang';
		} elseif($minpostsize && strlen(preg_replace("/\[quote\].+?\[\/quote\]/is", '', $message)) < $minpostsize) {
			return 'post_message_tooshort';
		}
	}
	return FALSE;
}

function checkbbcodes($message, $bbcodeoff) {
	return !$bbcodeoff && !preg_match("/\[.+\]/s", $message) ? -1 : $bbcodeoff;
}

function checksmilies($message, $smileyoff) {
	$smilies = array();
	if(!empty($GLOBALS['_DCACHE']['smilies']) && is_array($GLOBALS['_DCACHE']['smilies'])) {
		foreach($GLOBALS['_DCACHE']['smilies']['searcharray'] as $smiley) {
			$smilies[] = substr($smiley, 1, -1);
		}
	}
	return !$smileyoff && !preg_match('/'.implode('|', $smilies).'/', stripslashes($message)) ? -1 : $smileyoff;
}

function todayposts() {
	global $forum, $timestamp, $_DCACHE;
	$forum['lastpost'] = explode("\t", $forum['lastpost']);
	if($forum['lastpost'][2]) {
		if($forum['lastpost'][2] > $timestamp - gmdate('G', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600) * 3600 - gmdate('i', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600) * 60 - gmdate('s', $timestamp + $_DCACHE['settings']['timeoffset'] * 3600) * 1) {
			return 'todayposts+1';
		} else {
			return '1';
		}
	} else {
		return 'todayposts';
	}
}

function updatepostcredits($operator, $uidarray, $creditsarray) {
	global $db, $tablepre, $discuz_uid, $timestamp;

	$membersarray = $postsarray = array();
	foreach((is_array($uidarray) ? $uidarray : array($uidarray)) as $id) {
		$membersarray[intval(trim($id))]++;
	}
	foreach($membersarray as $uid => $posts) {
		$postsarray[$posts][] = $uid;
	}

	$lastpostadd = $uidarray == $discuz_uid ? ", lastpost='$timestamp'" : '';

	$creditsadd1 = '';
	if(is_array($creditsarray)) {
		foreach($creditsarray as $id => $addcredits) {
			$creditsadd1 .= ", extcredits$id=extcredits$id$operator$addcredits*\$posts";
		}
	}

	foreach($postsarray as $posts => $uidarray) {
		$uids = implode(',', $uidarray);
		$creditsadd2 = str_replace('$posts', $posts, $creditsadd1);
		$db->query("UPDATE {$tablepre}members SET posts=posts$operator$posts $lastpostadd $creditsadd2 WHERE uid IN ($uids)", 'UNBUFFERED');
	}
}

function updateforumcount($fid) {
	global $db, $tablepre;

	$query = $db->query("SELECT COUNT(*) AS threadcount, SUM(t.replies)+COUNT(*) AS replycount
		FROM {$tablepre}threads t, {$tablepre}forums f
		WHERE f.fid='$fid' AND t.fid=f.fid AND t.displayorder>='0'");

	extract($db->fetch_array($query), EXTR_SKIP);

	$query = $db->query("SELECT tid, subject, lastpost, lastposter FROM {$tablepre}threads
		WHERE fid='$fid' AND displayorder>='0' ORDER BY lastpost DESC LIMIT 1");

	$thread = $db->fetch_array($query);

	$thread['subject'] = addslashes($thread['subject']);
	$thread['lastposter'] = addslashes($thread['lastposter']);

	$db->query("UPDATE {$tablepre}forums SET posts='$replycount', threads='$threadcount', lastpost='{$thread['tid']}\t{$thread['subject']}\t{$thread['lastpost']}\t{$thread['lastposter']}' WHERE fid='$fid'", 'UNBUFFERED');
}

function updatethreadcount($tid, $updateattach = 0) {
	global $db, $tablepre;

	$query = $db->query("SELECT COUNT(*) FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0'");
	$replycount = $db->result($query, 0) - 1;

	$query = $db->query("SELECT author, dateline FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' ORDER BY dateline DESC LIMIT 1");
	$lastpost = $db->fetch_array($query);
	$lastpost['author'] = addslashes($lastpost['author']);

	if($updateattach) {
		$query = $db->query("SELECT attachment FROM {$tablepre}posts WHERE tid='$tid' AND invisible='0' AND attachment>0 LIMIT 1");
		$attachadd = ', attachment=\''.($db->num_rows($query)).'\'';
	} else {
		$attachadd = '';
	}

	$db->query("UPDATE {$tablepre}threads SET replies='$replycount', lastposter='{$lastpost['author']}', lastpost='{$lastpost['dateline']}' $attachadd WHERE tid='$tid'", 'UNBUFFERED');
}

function updatemodlog($tids, $action, $expiration = 0, $iscron = 0) {
	global $db, $tablepre, $timestamp;
	
	$uid = empty($iscron) ? $GLOBALS['discuz_uid'] : 0;
	$username = empty($iscron) ? $GLOBALS['discuz_user'] : 0;
	$expiration = empty($expiration) ? 0 : intval($expiration);
	
	$data = $comma = '';
	foreach(explode(',', str_replace(array('\'', ' '), array('', ''), $tids)) as $tid) {
		if($tid) {
			$data .= "{$comma} ('$tid', '$uid', '$username', '$timestamp', '$action', '$expiration', '1')";
			$comma = ',';
		}
	}

	!empty($data) && $db->query("INSERT INTO {$tablepre}threadsmod (tid, uid, username, dateline, action, expiration, status) VALUES $data", 'UNBUFFERED');

}

?>