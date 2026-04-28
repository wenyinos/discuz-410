<? if(!defined('IN_DISCUZ')) exit('Access Denied'); if(isset($trade) && $allowposttrade) { ?>
商品描述
<? } else { ?>
内容
<? } ?>
 <a href="###" onclick="checklength(document.input)" class="smalltxt">[字数检查]</a>:<br><br>
<span class="smalltxt">
Html 代码 <span class="bold">
<? if($forum['allowhtml'] || $allowhtml) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span><br>
<a href="faq.php?page=messages#6" target="_blank">Smilies</a> <span class="bold">
<? if($forum['allowsmilies']) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span><br>
<a href="faq.php?page=misc#1" target="_blank">Discuz! 代码</a> <span class="bold">
<? if($forum['allowbbcode']) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span><br>
[img] 代码 <span class="bold">
<? if($forum['allowimgcode']) { ?>
可用
<? } else { ?>
禁用
<? } ?>
</span>
</span><br><br><br>
<? if($smileyinsert) { ?>
<table cellpadding="3" cellspacing="0" width="50%" border="0" class="altbg1" style="border-width: 2px; border-style: outset" align="center">
<tr><td colspan="<?=$smcols?>" align="center" class="altbg1" style="border-width:1px; border-style:inset">Smilies</td></tr>
<?=$smilies?>
<? if($moresmilies) { ?>
<tr>
<td colspan="<?=$smcols?>" align="center"><br>
<a href="###" class="smalltxt"
onclick="window.open('post.php?action=smilies', '', 'width=200,height=500,resizable=yes,scrollbars=yes');">
<span class="bold">更多 Smilies</span></a></td>
</tr>
<? } ?>
</table>
<? } ?>
