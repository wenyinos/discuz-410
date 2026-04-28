<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<tr>
<td class="altbg1" valign="top">操作原因:
<? if($reasonpm == 1 || $reasonpm == 3) { ?>
<br><span class="smalltxt">您必须输入理由才能进行操作</span>
<? } ?>
</td>
<td class="altbg2">
<select name="selectreason" size="6" style="height: 8em; width: 8em" onchange="this.form.reason.value=this.value">
<option value="">自定义</option>
<option value="">--------</option>
<? if(is_array($modreasons)) { foreach($modreasons as $reasonarray) { ?>
<option value="<?=$reasonarray['0']?>"><?=$reasonarray['1']?></option>
<? } } ?>
</select>
<textarea name="reason" style="height: 8em; width: 18em"></textarea><br>
<input type="checkbox" name="sendreasonpm" value="1" <?=$reasonpmcheck?>> 发短消息通知作者
</td>
</tr>