<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: template.func.php,v $
	$Revision: 1.3 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function parse_template($file, $templateid, $tpldir) {
	global $language;

	$nest = 5;
	$tplfile = DISCUZ_ROOT."./$tpldir/$file.htm";
	$objfile = DISCUZ_ROOT."./forumdata/templates/{$templateid}_$file.tpl.php";

	if(!@$fp = fopen($tplfile, 'r')) {
		dexit("Current template file './$tpldir/$file.htm' not found or have no access!");
	} elseif(!include_once language('templates', $templateid, $tpldir)) {
		dexit("<br>Current template pack do not have a necessary language file 'templates.lang.php' or have syntax error!");
	}

	$template = fread($fp, filesize($tplfile));
	fclose($fp);

	$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
	$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

	$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
	$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
	$template = preg_replace_callback("/\{lang\s+(.+?)\}/is", function($m) { return languagevar($m[1]); }, $template);
	$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);

	$template = varreplace($template);

	$template = "<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>\n\n$template";
	$template = preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_]+)\}[\n\r\t]*/is", "\n<?php include template('\\1'); ?>\n", $template);
	$template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", "\n<?php include template(\\1); ?>\n", $template);
	$template = preg_replace_callback("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/is", function($m) { return stripvtags("\n<?php $m[1] ?>\n", ''); }, $template);
	$template = preg_replace_callback("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is", function($m) { return stripvtags("\n<?php echo $m[1]; ?>\n", ''); }, $template);
	$template = preg_replace_callback("/[\n\r\t]*\{elseif\s+(.+?)\}[\n\r\t]*/is", function($m) { return stripvtags("\n<?php } elseif($m[1]) { ?>\n", ''); }, $template);
	$template = preg_replace("/[\n\r\t]*\{else\}[\n\r\t]*/is", "\n<?php } else { ?>\n", $template);

	for($i = 0; $i < $nest; $i++) {
		$template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/is", function($m) { return stripvtags("\n<?php if(is_array($m[1])) { foreach($m[1] as $m[2]) { ?>", "\n$m[3]\n<?php } } ?>\n"); }, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/is", function($m) { return stripvtags("\n<?php if(is_array($m[1])) { foreach($m[1] as $m[2] => $m[3]) { ?>", "\n$m[4]\n<?php } } ?>\n"); }, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{if\s+(.+?)\}[\n\r]*(.+?)[\n\r]*\{\/if\}[\n\r\t]*/is", function($m) { return stripvtags("\n<?php if($m[1]) { ?>", "\n$m[2]\n<?php } ?>\n"); }, $template);
	}

	$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
	$template = preg_replace("/ \?\>[\n\r]*\<\?php? /s", " ", $template);

	if(!@$fp = fopen($objfile, 'w')) {
		dexit("Directory './forumdata/templates/' not found or have no access!");
	}

	flock($fp, 2);
	fwrite($fp, $template);
	fclose($fp);
}

function addquote($var) {
	return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}

function varreplace($template) {
	$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
	$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
	$template = preg_replace_callback("/$var_regexp/s", function($m) { return addquote('<?='.$m[1].'?>'); }, $template);
	$template = preg_replace_callback("/\<\?\=\<\?\=$var_regexp\?\>\?\>/s", function($m) { return addquote('<?='.$m[1].'?>'); }, $template);
	return $template;
}

function languagevar($var) {
	if(isset($GLOBALS['language'][$var])) {
		return $GLOBALS['language'][$var];
	} else {
		return "!$var!";
	}
}

function stripvtags($expr, $statement) {
	$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
	$statement = str_replace("\\\"", "\"", $statement);
	return $expr.$statement;
}

?>
