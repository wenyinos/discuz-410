<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: seccode.php,v $
	$Revision: 1.7 $
	$Date: 2006/04/04 02:54:15 $
*/

define('CURSCRIPT', 'seccode');

require_once './include/common.inc.php';

//debug 如果不存在有效链接 就不显示图片 防止别人作弊
if($boardurl != dirname($_SERVER['HTTP_REFERER']).'/') {
	exit('Access Denied');
}

//debug 重新更新验证码数字
if($update) {
	$seccode = random(4, 1);
	updatesession();
}

//debug 校验认证码范围
if($seccode < 1 || $seccode > 9999) {
	exit('Access Denied');
}

//debug 格式化认证吗
$seccode = sprintf('%04d', $seccode);

//debug 让浏览器无法缓存数据
if(!$nocacheheaders) {
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
}

//debug 支持gd库的情况
if(function_exists('imagecreate') && function_exists('imagecolorset') && function_exists('imagecopyresized') && function_exists('imagecolorallocate') && function_exists('imagesetpixel') && function_exists('imagechar') && function_exists('imagepng')) {

	$im = imagecreate(62, 25);
	$backgroundcolor = imagecolorallocate ($im, 255, 255, 255);
	
	//debug 重新计算显示顺序
	$numorder = array(1, 2, 3, 4);
	shuffle($numorder);
	$numorder = array_flip($numorder);

	//debug 存在加载图片，否则加载字体
	for($i = 1; $i <= 4; $i++) {
		$imcodefile = 'images/common/number'.$seccode[$numorder[$i]].'.gif';
		$x = $numorder[$i] * 13 + mt_rand(0, 4) - 2;
		$y = mt_rand(0, 3);
		if(file_exists($imcodefile)) {
			$imcode = imagecreatefromgif($imcodefile);
			$data = getimagesize($imcodefile);
			imagecolorset($imcode, 0 ,mt_rand(50, 255), mt_rand(50, 128), mt_rand(50, 255));
			imagecopyresized($im, $imcode, $x, $y, 0, 0, $data[0] + mt_rand(0, 6) - 3, $data[1] + mt_rand(0, 6) - 3, $data[0], $data[1]);
		} else {
			$text_color = imagecolorallocate($im, mt_rand(50, 255), mt_rand(50, 128), mt_rand(50, 255));
			imagechar($im, 5, $x + 5, $y + 3, $seccode[$numorder[$i]], $text_color);
		}
	}

	//debug 增加图片的掺杂特性
	$linenums = mt_rand(10, 32);
	for($i=0; $i <= $linenums; $i++) {
		$linecolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
		$linex = mt_rand(0, 62);
		$liney = mt_rand(0, 25);
		imageline($im, $linex, $liney, $linex + mt_rand(0, 4) - 2, $liney + mt_rand(0, 4) - 2, $linecolor);
	}	
	
	for($i=0; $i <= 64; $i++) {
		$pointcolor = imagecolorallocate($im, mt_rand(50, 255), mt_rand(50, 255), mt_rand(50, 255));
		imagesetpixel($im, mt_rand(0, 62), mt_rand(0, 25), $pointcolor);
	}

	$bordercolor = imagecolorallocate($im , 150, 150, 150);
	imagerectangle($im, 0, 0, 61, 24, $bordercolor);
		
	//debug 合成图片
	header('Content-type: image/png');
	imagepng($im);
	imagedestroy($im);

//debug 不支持gd的情况 输出bmp图片
} else {

	$numbers = array
		(
		0 => array('3c','66','66','66','66','66','66','66','66','3c'),
		1 => array('1c','0c','0c','0c','0c','0c','0c','0c','1c','0c'),
		2 => array('7e','60','60','30','18','0c','06','06','66','3c'),
		3 => array('3c','66','06','06','06','1c','06','06','66','3c'),
		4 => array('1e','0c','7e','4c','2c','2c','1c','1c','0c','0c'),
		5 => array('3c','66','06','06','06','7c','60','60','60','7e'),
		6 => array('3c','66','66','66','66','7c','60','60','30','1c'),
		7 => array('30','30','18','18','0c','0c','06','06','66','7e'),
		8 => array('3c','66','66','66','66','3c','66','66','66','3c'),
		9 => array('38','0c','06','06','3e','66','66','66','66','3c')
		);

	for($i = 0; $i < 10; $i++) {
		for($j = 0; $j < 6; $j++) {
			$a1 = substr('012', mt_rand(0, 2), 1).substr('012345', mt_rand(0, 5), 1);
			$a2 = substr('012345', mt_rand(0, 5), 1).substr('0123', mt_rand(0, 3), 1);
			mt_rand(0, 1) == 1 ? array_push($numbers[$i], $a1) : array_unshift($numbers[$i], $a1);
			mt_rand(0, 1) == 0 ? array_push($numbers[$i], $a1) : array_unshift($numbers[$i], $a2);
		}
	}

	$bitmap = array();
	for($i = 0; $i < 20; $i++) {
		for ($j = 0; $j < 4; $j++) {
			$n = substr($seccode, $j, 1);
			$bytes = $numbers[$n][$i];
			$a = mt_rand(0, 14);
			switch($a) {
				case 1: str_replace('9', '8', $bytes); break;
				case 3: str_replace('c', 'e', $bytes); break;
				case 6: str_replace('3', 'b', $bytes); break;
				case 8: str_replace('8', '9', $bytes); break;
				case 0: str_replace('e', 'f', $bytes); break;
			}
			array_push($bitmap, $bytes);
		}
	}

	for ($i = 0; $i < 8; $i++) {
		$a = substr('012', mt_rand(0, 2), 1) . substr('012345', mt_rand(0, 5), 1);
		array_unshift($bitmap, $a);
		array_push($bitmap, $a);
	}

	$image = pack('H*', '424d9e000000000000003e000000280000002000000018000000010001000000'.
			'0000600000000000000000000000000000000000000000000000FFFFFF00'.implode('', $bitmap));

	header('Content-Type: image/bmp');
	echo $image;

}

?>