<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$RCSfile: database.inc.php,v $
	$Revision: 1.16 $
	$Date: 2006/02/23 13:44:02 $
*/

if(!defined('IN_DISCUZ') || !isset($PHP_SELF) || !preg_match("/[\/\\\\]admincp\.php$/", $PHP_SELF)) {
        exit('Access Denied');
}

require_once DISCUZ_ROOT.'./include/attachment.func.php';

cpheader();

if($action == 'export') {

	if(!submitcheck('exportsubmit', 1)) {

		$shelldisabled = function_exists('shell_exec') ? '' : 'disabled';
		$sqlcharsets = "<input type=\"radio\" name=\"sqlcharset\" value=\"\" checked> $lang[default]".
			($dbcharset ? " &nbsp; <input type=\"radio\" name=\"sqlcharset\" value=\"$dbcharset\"> ".strtoupper($dbcharset) : '').
			($db->version() > '4.1' && $dbcharset != 'utf8' ? " &nbsp; <input type=\"radio\" name=\"sqlcharset\" value='utf8'> UTF-8</option>" : '');

		$tables = $tablelist = '';
		$query = $db->query("SELECT value FROM {$tablepre}settings WHERE variable='custombackup'");
		if($tables = $db->fetch_array($query)) {
			$tables = unserialize($tables['value']);
			$tables = is_array($tables) ? $tables : '';
		}
		$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'");

		$rowcount = 0;
		while($table = $db->fetch_array($query)) {
			$checked = $tables && in_array($table['Name'], $tables) ? 'checked' : '';
			$tablelist .= ($rowcount % 4 ? '' : '</tr><tr>')."<td><input type=\"checkbox\" name=\"customtables[]\" value=\"$table[Name]\" $checked> $table[Name]</td>\n";
			$rowcount++;
		}

?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="85%" align="center" class="tableborder">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['database_export_tips']?>
</td></tr></table>

<br><br><form name="backup" method="post" action="admincp.php?action=export">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="setup" value="1">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="85%" align="center" class="tableborder">
<tr class="header"><td colspan="2"><?=$lang['database_export_type']?></td></tr>
<tr>
<td bgcolor="<?=ALTBG1?>" width="40%"><input type="radio" value="full" name="type" onclick="findobj('showtables').style.display='none'"> <?=$lang['database_export_full']?></td>
<td bgcolor="<?=ALTBG2?>" width="60%"><?=$lang['database_export_full_comment']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" value="standard" checked name="type" onclick="findobj('showtables').style.display='none'"> <?=$lang['database_export_standard']?></td>
<td bgcolor="<?=ALTBG2?>"><?=$lang['database_export_standard_comment']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" value="mini" name="type" onclick="findobj('showtables').style.display='none'"> <?=$lang['database_export_mini']?></td>
<td bgcolor="<?=ALTBG2?>"><?=$lang['database_export_mini_comment']?></td></tr>

<tr>
<td class="altbg1"><input type="radio" value="custom" name="type" onclick="findobj('showtables').style.display=''"> <?=$lang['database_export_custom']?></td>
<td class="altbg2"><?=$lang['database_export_custom_comment']?></td></tr>

<tbody id="showtables" style="display:none">
<tr>
<td class="altbg2" colspan="2">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr><td colspan="4"><input type="checkbox" name="chkall" onclick="checkall(this.form, 'customtables')"> <b><?=$lang['database_export_custom_select_all']?></b></td>
<?=$tablelist?>
</table>
</td>
</tr>
</tbody>

<tr class="header"><td colspan="2"><?=$lang['database_export_method']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" name="method" value="shell" <?=$shelldisabled?> onclick="if(<?=intval($db->version() < '4.1')?>) {if(this.form.sqlcompat[2].checked==true) this.form.sqlcompat[0].checked=true; this.form.sqlcompat[2].disabled=true; this.form.sizelimit.disabled=true;} else {this.form.sqlcharset[0].checked=true; for(var i=1; i<=5; i++) {if(this.form.sqlcharset[i]) this.form.sqlcharset[i].disabled=true;}}"> <?=$lang['database_export_shell']?></td>
<td bgcolor="<?=ALTBG2?>">&nbsp;</td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>"><input type="radio" name="method" value="multivol" checked onclick="this.form.sqlcompat[2].disabled=false; this.form.sizelimit.disabled=false; for(var i=1; i<=5; i++) {if(this.form.sqlcharset[i]) this.form.sqlcharset[i].disabled=false;}"> <?=$lang['database_export_multivol']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="text" size="40" name="sizelimit" value="2048"></td>
</tr>

<tr class="header"><td colspan="2"><?=$lang['database_export_options']?></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>">&nbsp;<?=$lang['database_export_options_extended_insert']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="radio" name="extendins" value="1"> <?=$lang['yes']?> &nbsp; <input type="radio" name="extendins" value="0" checked> <?=$lang['no']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>">&nbsp;<?=$lang['database_export_options_add_set_names']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="radio" name="addsetnames" value="1"> <?=$lang['yes']?> &nbsp; <input type="radio" name="addsetnames" value="0" checked> <?=$lang['no']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>">&nbsp;<?=$lang['database_export_options_sql_compatible']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="radio" name="sqlcompat" value="" checked> <?=$lang['default']?> &nbsp; <input type="radio" name="sqlcompat" value="MYSQL40"> MySQL 3.23/4.0.x &nbsp; <input type="radio" name="sqlcompat" value="MYSQL41"> MySQL 4.1.x/5.x &nbsp;
</td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>">&nbsp;<?=$lang['database_export_options_charset']?></td>
<td bgcolor="<?=ALTBG2?>"><?=$sqlcharsets?>
</td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>">&nbsp;<?=$lang['database_export_filename']?></td>
<td bgcolor="<?=ALTBG2?>"><input type="text" size="40" name="filename" value="./forumdata/<?=date('md').'_'.random(8)?>.sql" onclick="alert('<?=$lang['database_export_filename_confirm']?>');"></td>
</tr>

</table><br><center>
<input type="submit" name="exportsubmit" value="<?=$lang['submit']?>"></center></form>
<?

	} else {

		if(!$filename || preg_match("/(\.)(exe|jsp|asp|aspx|cgi|fcgi|pl)(\.|$)/i", $filename)) {
			cpmsg('database_export_filename_invalid');
		}

		$time = gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600);
		if($type == 'full') {
			$tables = array('access', 'adminactions', 'admingroups', 'adminnotes', 'adminsessions', 'advertisements', 'announcements',
				'attachments', 'attachtypes', 'banned', 'bbcodes', 'blogcaches', 'buddys', 'creditslog', 'crons', 'failedlogins',
				'favorites', 'forumfields', 'forumlinks', 'forums', 'medals', 'memberfields', 'members', 'moderators', 'modworks',
				'onlinelist', 'onlinetime', 'orders', 'paymentlog', 'pluginhooks', 'plugins', 'pluginvars', 'pms', 'pmsearchindex', 'polls', 'posts', 'profilefields',
				'promotions', 'ranks', 'ratelog', 'regips', 'relatedthreads', 'rsscaches', 'searchindex', 'sessions', 'settings', 'smilies', 'stats',
				'statvars', 'styles', 'stylevars', 'subscriptions', 'templates', 'threads', 'threadsmod', 'threadtypes',
				'usergroups', 'validating', 'words');
		} elseif($type == 'standard') {
			$tables = array('access', 'adminactions', 'admingroups', 'adminnotes', 'adminsessions', 'advertisements', 'announcements', 'attachments',
				'attachtypes', 'banned', 'bbcodes', 'buddys', 'creditslog', 'crons', 'favorites', 'forumfields', 'forumlinks', 'forums',
				'medals', 'memberfields', 'members', 'moderators', 'onlinelist', 'onlinetime', 'orders', 'paymentlog', 'pluginhooks', 'plugins',
				'pluginvars', 'polls', 'posts', 'profilefields', 'ranks', 'ratelog', 'settings', 'smilies', 'stats', 'styles',
				'stylevars', 'templates', 'threads', 'threadsmod', 'threadtypes', 'usergroups', 'validating', 'words');
		} elseif($type == 'mini') {
			$tables = array('access', 'adminactions', 'admingroups', 'adminsessions', 'advertisements', 'announcements', 'attachtypes', 'bbcodes',
				'buddys', 'crons', 'forumfields', 'forumlinks', 'forums', 'medals', 'memberfields', 'members', 'moderators', 'onlinelist',
				'onlinetime', 'pluginhooks', 'plugins', 'pluginvars', 'profilefields', 'ranks', 'settings', 'smilies', 'stats', 'styles',
				'stylevars', 'templates', 'threadtypes', 'usergroups', 'words');
		} elseif($type == 'custom') {
			$tables = array();
			if(empty($setup)) {
				$query = $db->query("SELECT value FROM {$tablepre}settings WHERE variable='custombackup'");
				if($tables = $db->fetch_array($query)) {
					$tables = unserialize($tables['value']);
				}
			} else {
				$customtablesnew = empty($customtables)? '' : addslashes(serialize($customtables));
				$db->query("REPLACE INTO {$tablepre}settings (variable, value) VALUES ('custombackup', '$customtablesnew')");
				$tables = $customtables;
			}
			if( !is_array($tables) || empty($tables)) {
				cpmsg('database_export_custom_invalid');
			}
		}

		if($type == 'full' || $type == 'standard') {
			$query = $db->query("SELECT datatables FROM {$tablepre}plugins WHERE datatables<>''");
			while($plugin = $db->fetch_array($query)) {
				foreach(explode(',', $plugin['datatables']) as $table) {
					if($table = trim($table)) {
						$tables[] = $table;
					}
				}
			}
		}

		$volume = intval($volume) + 1;
		$idstring = '# Identify: '.base64_encode("$timestamp,$version,$type,$method,$volume")."\n";

		if($volume == 1) {
			$db->query("TRUNCATE TABLE {$tablepre}relatedthreads", 'UNBUFFERED');
			$db->query("TRUNCATE TABLE {$tablepre}pmsearchindex", 'UNBUFFERED');
			$db->query("TRUNCATE TABLE {$tablepre}searchindex", 'UNBUFFERED');
		}

		$dumpcharset = $sqlcharset ? $sqlcharset : str_replace('-', '', $GLOBALS['charset']);
		$setnames = $addsetnames || ($db->version() > '4.1' && (!$sqlcompat || $sqlcompat == 'MYSQL41')) ? "SET NAMES '$dumpcharset';\n\n" : '';

		if($db->version() > '4.1') {
			if($sqlcharset) {
				$db->query("SET NAMES '".$sqlcharset."';\n\n");
			}
			if($sqlcompat == 'MYSQL40') {
				$db->query("SET SQL_MODE='MYSQL40'");
			}
		}

		if($method == 'multivol') {

			$sqldump = '';
			$tableid = $tableid ? $tableid - 1 : 0;
			$startfrom = intval($startfrom);
			for($i = $tableid; $i < count($tables) && strlen($sqldump) < $sizelimit * 1000; $i++) {
				$sqldump .= sqldumptable(($type != 'custom' ? $tablepre : '').$tables[$i], $startfrom, strlen($sqldump));
				$startfrom = 0;
			}
			$tableid = $i;

			$dumpfile = substr($filename, 0, strrpos($filename, '.'))."-%s".strrchr($filename, '.');

			if(trim($sqldump)) {

				$sqldump = "$idstring".
					"# <?exit();?>\n".
					"# Discuz! Multi-Volume Data Dump Vol.$volume\n".
					"# Version: Discuz! $version\n".
					"# Time: $time\n".
					"# Type: $type\n".
					"# Table Prefix: $tablepre\n".
					"#\n".
					"# Discuz! Home: http://www.discuz.com\n".
					"# Please visit our website for newest infomation about Discuz!\n".
					"# --------------------------------------------------------\n\n\n".
					"$setnames".
					$sqldump;

				@$fp = fopen(($method == 'multivol' ? sprintf($dumpfile, $volume) : $filename), 'wb');
				@flock($fp, 2);
				if(@!fwrite($fp, $sqldump)) {
					@fclose($fp);
					cpmsg('database_export_file_invalid');
				} else {
					cpmsg('database_export_multivol_redirect', "admincp.php?action=export&type=".rawurlencode($type)."&saveto=".rawurlencode(server)."&filename=".rawurlencode($filename)."&method=".rawurlencode(multivol)."&sizelimit=".rawurlencode($sizelimit)."&volume=".rawurlencode($volume)."&tableid=".rawurlencode($tableid)."&startfrom=".rawurlencode($startrow)."&extendins=".rawurlencode($extendins)."&sqlcharset=".rawurlencode($sqlcharset)."&sqlcompat=".rawurlencode($sqlcompat)."&exportsubmit=yes");
				}
			} else {
				$volume--;
				$filelist = '<ul>';
				for($i = 1; $i <= $volume; $i++) {
					$filename = sprintf($dumpfile, $i);
					$filelist .= "<li><a href=\"$filename\">$filename\n";
				}
				cpheader();
				cpmsg('database_export_multivol_succeed');
			}

		} else {

			$tablesstr = '';
			foreach($tables as $table) {
				$tablesstr .= '"'.($type != 'custom' ? $tablepre : '').$table.'" ';
			}

			require './config.inc.php';
			list($dbhost, $dbport) = explode(':', $dbhost);

			$query = $db->query("SHOW VARIABLES LIKE 'basedir'");
			list(, $mysql_base) = $db->fetch_array($query, MYSQL_NUM);

			$dumpfile = addslashes(dirname(dirname(__FILE__))).'/'.$filename;
			@unlink($dumpfile);

			$mysqlbin = $mysql_base == '/' ? '' : addslashes($mysql_base).'bin/';
			@shell_exec($mysqlbin.'mysqldump --force --quick '.($db->version() > '4.1' ? '--skip-opt --create-options' : '-all').' --add-drop-table'.($extendins == 1 ? ' --extended-insert' : '').''.($db->version() > '4.1' && $sqlcompat == 'MYSQL40' ? ' --compatible=mysql40' : '').' --host="'.$dbhost.($dbport ? (is_numeric($dbport) ? ' --port='.$dbport : ' --socket="'.$dbport.'"') : '').'" --user="'.$dbuser.'" --password="'.$dbpw.'" "'.$dbname.'" '.$tablesstr.' > '.$dumpfile);

			if(@file_exists($dumpfile)) {

				if(@is_writeable($dumpfile)) {
					$fp = fopen($dumpfile, 'rb+');
					fwrite($fp, $idstring."# <?exit();?>\n ".$setnames."\n #");
					fclose($fp);
				}
				cpmsg('database_export_succeed');

			} else {

				cpmsg('database_shell_fail');

			}

		}
	}

} elseif($action == 'import') {

	 if(!submitcheck('importsubmit', 1) && !submitcheck('deletesubmit')) {
	 	$exportlog = array();
	 	if(is_dir(DISCUZ_ROOT.'./forumdata')) {
	 		$dir = dir(DISCUZ_ROOT.'./forumdata');
			while($entry = $dir->read()) {
				$entry = "./forumdata/$entry";
				if(is_file($entry) && preg_match("/\.sql/i", $entry)) {
					$filesize = filesize($entry);
					$fp = fopen($entry, 'rb');
					$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", fgets($fp, 256))));
					fclose ($fp);

					$exportlog[$identify[0]] = array(	'version' => $identify[1],
										'type' => $identify[2],
										'method' => $identify[3],
										'volume' => $identify[4],
										'filename' => $entry,
										'size' => $filesize);
				}
			}
			$dir->close();
		} else {
			cpmsg('database_export_dest_invalid');
		}
		krsort($exportlog);
		reset($exportlog);

		$exportinfo = '';
		foreach($exportlog as $dateline => $info) {
			$info['dateline'] = is_int($dateline) ? gmdate("$dateformat $timeformat", $dateline + $timeoffset * 3600) : $lang['unknown'];
			switch($info['type']) {
				case 'full':
					$info['type'] = $lang['database_export_full'];
					break;
				case 'standard':
					$info['type'] = $lang['database_export_standard'];
					break;
				case 'mini':
					$info['type'] = $lang['database_export_mini'];
					break;
				case 'custom':
					$info['type'] = $lang['database_export_custom'];
					break;
			}
			$info['size'] = sizecount($info['size']);
			$info['volume'] = $info['method'] == 'multivol' ? $info['volume'] : '';
			$info['method'] = $info['method'] == 'multivol' ? $lang['database_multivol'] : $lang['database_shell'];
			$exportinfo .= "<tr align=\"center\"><td bgcolor=\"".ALTBG1."\"><input type=\"checkbox\" name=\"delete[]\" value=\"$info[filename]\"></td>\n".
				"<td bgcolor=\"".ALTBG2."\"><a href=\"$info[filename]\">".substr(strrchr($info['filename'], "/"), 1)."</a></td>\n".
				"<td bgcolor=\"".ALTBG1."\">$info[version]</td>\n".
				"<td bgcolor=\"".ALTBG2."\">$info[dateline]</td>\n".
				"<td bgcolor=\"".ALTBG1."\">$info[type]</td>\n".
				"<td bgcolor=\"".ALTBG2."\">$info[size]</td>\n".
				"<td bgcolor=\"".ALTBG1."\">$info[method]</td>\n".
				"<td bgcolor=\"".ALTBG2."\">$info[volume]</td>\n".
				"<td bgcolor=\"".ALTBG1."\"><a href=\"admincp.php?action=import&from=server&datafile_server=$info[filename]&importsubmit=yes\"".
				($info['version'] != $version ? " onclick=\"return confirm('$lang[database_import_confirm]');\"" : '').">[$lang[import]]</a></td>\n";
		}

?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['database_import_tips']?>
</td></tr></table>

<br><form name="restore" method="post" action="admincp.php?action=import" enctype="multipart/form-data">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr class="header">
<td colspan="2"><?=$lang['database_import']?></td>
</tr>

<tr>
<td bgcolor="<?=ALTBG1?>" width="40%"><input type="radio" name="from" value="server" checked onclick="this.form.datafile_server.disabled=!this.checked;this.form.datafile.disabled=this.checked"><?=$lang['database_import_from_server']?></td>
<td bgcolor="<?=ALTBG2?>" width="60%"><input type="text" size="40" name="datafile_server" value="./forumdata/"></td></tr>

<tr>
<td bgcolor="<?=ALTBG1?>" width="40%"><input type="radio" name="from" value="local" onclick="this.form.datafile_server.disabled=this.checked;this.form.datafile.disabled=!this.checked"><?=$lang['database_import_from_local']?></td>
<td bgcolor="<?=ALTBG2?>" width="60%"><input type="file" size="29" name="datafile" disabled></td></tr>

</table><br><center>
<input type="submit" name="importsubmit" value="<?=$lang['submit']?>"></center>
</form>

<br><form method="post" action="admincp.php?action=import">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr class="header"><td colspan="9"><?=$lang['database_export_file']?></td></tr>
<tr align="center" class="category"><td width="48"><input type="checkbox" name="chkall" class="category" onclick="checkall(this.form)"><?=$lang['del']?></td>
<td><?=$lang['filename']?></td><td><?=$lang['version']?></td>
<td><?=$lang['time']?></td><td><?=$lang['type']?></td>
<td><?=$lang['size']?></td><td><?=$lang['database_method']?></td>
<td><?=$lang['database_volume']?></td><td><?=$lang['operation']?></td></tr>
<?=$exportinfo?>
</table><br><center>
<input type="submit" name="deletesubmit" value="<?=$lang['submit']?>"></center></form>
<?

	 } elseif(submitcheck('importsubmit', 1)) {

		$readerror = 0;
		if($from == 'server') {
			$datafile = addslashes(dirname(dirname(__FILE__))).'/'.$datafile_server;
		}

		if(@$fp = fopen($datafile, 'rb')) {
			$sqldump = fgets($fp, 256);
			$identify = explode(',', base64_decode(preg_replace("/^# Identify:\s*(\w+).*/s", "\\1", $sqldump)));
			$dumpinfo = array('method' => $identify[3], 'volume' => intval($identify[4]));
			if($dumpinfo['method'] == 'multivol') {
				$sqldump .= fread($fp, 8388607);
			}
			fclose($fp);
		} else {
			if($autoimport) {
				updatecache();
				cpmsg('database_import_multivol_succeed');
			} else {
				cpmsg('database_import_file_illegal');
			}
		}

		if($dumpinfo['method'] == 'multivol') {
			$sqlquery = splitsql($sqldump);
			unset($sqldump);
			foreach($sqlquery as $sql) {
				if(trim($sql) != '') {
					$db->query($sql, 'SILENT');
					if(($sqlerror = $db->error()) && $db->errno() != 1062) {
						$db->halt('MySQL Query Error', $sql);
					}
				}
			}

			$datafile_next = preg_replace("/-($dumpinfo[volume])(\..+)$/", "-".($dumpinfo['volume'] + 1)."\\2", $datafile_server);
			//$datafile_next = str_replace("-$dumpinfo[volume].sql", '-'.($dumpinfo['volume'] + 1).'.sql', $datafile_server);

			if($dumpinfo['volume'] == 1) {
				cpmsg('database_import_multivol_prompt',
					"admincp.php?action=import&from=server&datafile_server=$datafile_next&autoimport=yes&importsubmit=yes",
					'form');
			} elseif($autoimport) {
				cpmsg('database_import_multivol_redirect', "admincp.php?action=import&from=server&datafile_server=$datafile_next&autoimport=yes&importsubmit=yes");
			} else {
				updatecache();
				cpmsg('database_import_succeed');
			}
		} elseif($dumpinfo['method'] == 'shell') {
			require './config.inc.php';
			list($dbhost, $dbport) = explode(':', $dbhost);

			$query = $db->query("SHOW VARIABLES LIKE 'basedir'");
			list(, $mysql_base) = $db->fetch_array($query, MYSQL_NUM);

			$mysqlbin = $mysql_base == '/' ? '' : addslashes($mysql_base).'bin/';
			shell_exec($mysqlbin.'mysql -h"'.$dbhost.($dbport ? (is_numeric($dbport) ? ' -P'.$dbport : ' -S"'.$dbport.'"') : '').
				'" -u"'.$dbuser.'" -p"'.$dbpw.'" "'.$dbname.'" < '.$datafile);

			updatecache();
			cpmsg('database_import_succeed');
		} else {
			cpmsg('database_import_format_illegal');
		}

	} elseif(submitcheck('deletesubmit')) {
		if(is_array($delete)) {
			foreach($delete as $filename) {
				@unlink($filename);
			}
			cpmsg('database_file_delete_succeed');
		} else {
			cpmsg('database_file_delete_invalid');
		}
	}

} elseif($action == 'runquery') {

	if(!submitcheck('sqlsubmit')) {

?>
<br><br><form method="post" action="admincp.php?action=runquery">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="60%" align="center" class="tableborder">
<tr class="header"><td colspan=2><?=$lang['database_run_query']?></td></tr>
<tr bgcolor="<?=ALTBG1?>" align="center">
<td valign="top"><textarea cols="85" rows="10" name="queries"></textarea><br>
<br><?=$lang['database_run_query_comment']?>
</td></tr></table>
<br><br>
<center><input type="submit" name="sqlsubmit" value="<?=$lang['submit']?>"></center>
</form></td></tr>
<?

	} else {

		$sqlquery = splitsql(str_replace(' cdb_', ' '.$tablepre, $queries));
		foreach($sqlquery as $sql) {
			if(trim($sql) != '') {
				$db->query(stripslashes($sql), 'SILENT');
				if($sqlerror = $db->error()) {
					break;
				}
			}
		}

		cpmsg($sqlerror ? 'database_run_query_invalid' : 'database_run_query_succeed');
	}

} elseif($action == 'optimize') {

?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr class="header"><td><?=$lang['tips']?></td></tr>
<tr bgcolor="<?=ALTBG1?>"><td>
<br><?=$lang['database_optimize_tips']?>
</td></tr></table>

<br><br><form name="optimize" method="post" action="admincp.php?action=optimize">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="90%" align="center" class="tableborder">
<tr align="center" class="header">
<td><input type="checkbox" name="chkall" class="header" onclick="checkall(this.form)" checked><?=$lang['database_optimize_opt']?></td><td><?=$lang['database_optimize_table_name']?></td><td><?=$lang['type']?></td><td><?=$lang['database_optimize_rows']?></td>
<td><?=$lang['database_optimize_data']?></td><td><?=$lang['database_optimize_index']?></td><td><?=$lang['database_optimize_frag']?></td></tr>
<?

	$optimizetable = '';
	$totalsize = 0;
	if(!submitcheck('optimizesubmit')) {
		$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'");
		while($table = $db->fetch_array($query)) {
			$checked = $table['Type'] == 'MyISAM' || $table['Engine'] == 'MyISAM' ? 'checked' : 'disabled';
			echo "<tr><td bgcolor=\"".ALTBG1."\" align=\"center\"><input type=\"checkbox\" name=\"optimizetables[]\" value=\"$table[Name]\" $checked></td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Name]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Type]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Rows]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Data_length]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Index_length]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Data_free]</td></tr>\n";
			$totalsize += $table['Data_length'] + $table['Index_length'];
		}
		echo "<tr class=\"header\"><td colspan=\"7\" align=\"right\">$lang[database_optimize_used] ".sizecount($totalsize)."</td></tr></table><br><br><center><input type=\"submit\" name=\"optimizesubmit\" value=\"$lang[submit]\"></center>\n";
	} else {
		$db->query("DELETE FROM {$tablepre}subscriptions", 'UNBUFFERED');
		$db->query("UPDATE {$tablepre}memberfields SET authstr=''", 'UNBUFFERED');

		$query = $db->query("SHOW TABLE STATUS LIKE '$tablepre%'");
		while($table = $db->fetch_array($query)) {
			if(is_array($optimizetables) && in_array($table['Name'], $optimizetables)) {
				$optimized = $lang['yes'];
				$db->query("OPTIMIZE TABLE $table[Name]");
			} else {
				$optimized = '<b>'.$lang['no'].'</b>';
			}

			echo "<tr>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$optimized</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Name]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Type]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Rows]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">$table[Data_length]</td>\n".
				"<td bgcolor=\"".ALTBG2."\" align=\"center\">$table[Index_length]</td>\n".
				"<td bgcolor=\"".ALTBG1."\" align=\"center\">0</td>\n".
				"</tr>\n";
			$totalsize += $table['Data_length'] + $table['Index_length'];
		}
		echo "<tr class=\"header\"><td colspan=\"7\" align=\"right\">$lang[database_optimize_used] ".sizecount($totalsize)."</td></tr></table>";
	}

	echo '</table></form>';
}

?>