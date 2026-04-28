<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<script language="JavaScript">
var attachnum = 1;
function addattachment() {
attachnum < 5 ? attachnum++ : findobj("addattachlink").disabled=true;
findobj("attach_"+attachnum).style.display = "";
}
</script>

<br>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td colspan="2" class="header"><a href="member.php?action=credits&view=postattach" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>上传新附件 <a id="addattachlink" href="###" onclick="addattachment(this.form)">[+]</a></td></tr>
<tr class="category"><td colspan="2">上传新附件 (小于 <?=$maxattachsize_kb?> kb 
<? if($attachextensions) { ?>
, 可用扩展名: <?=$attachextensions?>
<? } ?>
)</td></tr>
<tbody id="attach_1" style=""><tr class="altbg2"><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td></tr></tbody>
<tbody id="attach_2" style="display: none"><tr class="altbg2"><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td></tr></tbody>
<tbody id="attach_3" style="display: none"><tr class="altbg2"><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td></tr></tbody>
<tbody id="attach_4" style="display: none"><tr class="altbg2"><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td></tr></tbody>
<tbody id="attach_5" style="display: none"><tr class="altbg2"><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td><td width="50%">
<? if($allowsetattachperm) { ?>
阅读权限: <input type="text" name="attachperm[]" value="0" size="1">&nbsp; &nbsp;
<? } ?>
描述: <input type="text" name="attachdesc[]" size="15">&nbsp; &nbsp;附件: <input type="file" name="attach[]" size="15"></td></tr></tbody>
</table>