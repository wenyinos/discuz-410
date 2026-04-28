<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: header.inc.php,v $
	$Revision: 1.4 $
	$Date: 2006/02/23 13:44:02 $
*/


if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

?>
<html>
<head>
<base href="<?=$boardurl?>">
<title><?=$_DCACHE['settings']['bbname']?> <?=$_DCACHE['settings']['seotitle']?> <?=$navtitle?> - powered by Discuz! Archiver</title>
<?=$_DCACHE['settings']['seohead']?>

<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<meta name="keywords" content="Discuz!,Board,Comsenz,forums,bulletin board,<?=$_DCACHE['settings']['seokeywords']?>">
<meta name="description" content="<?=$_DCACHE['settings']['bbname']?> <?=$_DCACHE['settings']['seodescription']?> - Discuz! Archiver">
<meta name="generator" content="Discuz! Archiver <?=$_DCACHE['settings']['version']?>">

<style type="text/css">
a				{ text-decoration: none; color: <?=LINK?> }
a:hover			{ text-decoration: underline }
body			{ scrollbar-base-color: <?=ALTBG1?>; scrollbar-arrow-color: <?=BORDERCOLOR?>; font-size: <?=FONTSIZE?>; <?=BGCODE?> }
table			{ font-family: <?=FONT?>; font-size: <?=FONTSIZE?>; color: <?=TABLETEXT?> }
li				{ padding: 2px }
.tableborder	{ background: <?=INNERBORDERCOLOR?>; border: <?=BORDERWIDTH?>px solid <?=BORDERCOLOR?> } 
.smalltxt		{ font-family: <?=SMFONT?>; font-size: <?=SMFONTSIZE?> }
.bold			{ font-weight: <?=BOLD?> }
</style>
</head>

<body leftmargin="10" rightmargin="10" topmargin="10">
<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="tableborder">
<tr><td <?=MAINTABLEBGCODE?>><br>