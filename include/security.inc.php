<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: security.inc.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if($attackevasive == 1 || $attackevasive == 3) {
	dsetcookie('lastrequest', time(), 0, 0);
	if(time() - $_COOKIE['lastrequest'] < 2) {
?>
<html>
<head>
<title>Refresh Limitation Enabled</title>
</head>
<body bgcolor="#FFFFFF">
<script language="JavaScript">
function reload() {
	document.location.reload();
}
setTimeout("reload()", 2001);
</script>
<table cellpadding="0" cellspacing="0" border="0" width="700" align="center" height="85%">
  <tr align="center" valign="middle">
    <td>
    <table cellpadding="10" cellspacing="0" border="0" width="80%" align="center" style="font-family: Verdana, Tahoma; color: #666666; font-size: 11px">
    <tr>
      <td valign="middle" align="center" bgcolor="#EBEBEB">
        <br><b style="font-size: 16px">Refresh Limitation Enabled</b>
        <br><br>The time between your two requests is smaller than 2 seconds, please do NOT refresh and wait for automatical forwarding ...
        <br><br>
      </td>
    </tr>
    </table>
    </td>
  </tr>
</table>
</body>
</html>
<?
		exit();
	}
}

if(($attackevasive == 2 || $attackevasive == 3) && ($_SERVER['HTTP_X_FORWARDED_FOR'] ||
	$_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] ||
	$_SERVER['HTTP_USER_AGENT_VIA'] || $_SERVER['HTTP_CACHE_CONTROL'] ||
	$_SERVER['HTTP_CACHE_INFO'] || $_SERVER['HTTP_PROXY_CONNECTION'])) {
?>
<html>
<head>
<title>Proxy Connection Denied</title>
</head>
<body bgcolor="#FFFFFF">
<table cellpadding="0" cellspacing="0" border="0" width="700" align="center" height="85%">
  <tr align="center" valign="middle">
    <td>
    <table cellpadding="10" cellspacing="0" border="0" width="80%" align="center" style="font-family: Verdana, Tahoma; color: #666666; font-size: 11px">
    <tr>
      <td valign="middle" align="center" bgcolor="#EBEBEB">
        <br><b style="font-size: 16px">Proxy Connection Denied</b>
        <br><br>Your request was forbidden due to the administrator has set to deny all proxy connection.
        <br><br>
      </td>
    </tr>
    </table>
    </td>
  </tr>
</table>
</body>
</html>
<?
	exit();
}

?>