<?

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: plugin.php,v $
	$Revision: 1.7.2.2 $
	$Date: 2006/03/08 07:35:39 $
*/

require_once './include/common.inc.php';

$pluginmodule = isset($plugins['links'][$identifier][$module]) ? $plugins['links'][$identifier][$module] : '';

if(empty($identifier) || empty($module) || !preg_match("/^[a-z0-9_\-]+$/i", $module) || !$pluginmodule) {
	showmessage('undefined_action');
} elseif($pluginmodule['adminid'] && ($adminid < 1 || ($adminid > 0 && $pluginmodule['adminid'] < $adminid))) {
	showmessage('plugin_nopermission');
} elseif(@!file_exists(DISCUZ_ROOT.($modfile = './plugins/'.$pluginmodule['directory'].((!empty($pluginmodule['directory']) && substr($pluginmodule['directory'], -1) != '/') ? '/' : '') .$module.'.inc.php'))) {
	showmessage('plugin_module_nonexistence');
}

include DISCUZ_ROOT.$modfile;

?>