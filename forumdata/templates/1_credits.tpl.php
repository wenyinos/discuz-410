<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; 积分策略说明</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header">
<td colspan="13">积分策略说明</td>
</tr>

<tr align="center" class="category">
<td width="9%">&nbsp;</td>
<? if(is_array($policyarray)) { foreach($policyarray as $operation => $policy) { ?>
<td width="7%" nowrap>
<? if($operation == 'post') { ?>
发新主题
<? } elseif($operation == 'forum_post') { ?>
本版发新主题
<? } elseif($operation == 'reply') { ?>
发表回复
<? } elseif($operation == 'forum_reply') { ?>
本版发表回复
<? } elseif($operation == 'digest') { ?>
加入精华
<? } elseif($operation == 'postattach') { ?>
发表附件
<? } elseif($operation == 'getattach') { ?>
下载附件
<? } elseif($operation == 'pm') { ?>
发短消息
<? } elseif($operation == 'search') { ?>
搜索
<? } elseif($operation == 'promotion_visit') { ?>
访问推广
<? } elseif($operation == 'promotion_register') { ?>
注册推广
<? } ?>
</td>
<? } } ?>
<td width="7%">积分下限</td>
</tr>
<? if(is_array($extcredits)) { foreach($extcredits as $id => $credit) { ?>
<tr align="center" class="altbg2">
<td class="altbg1"><?=$credit['title']?></td>
<? if(is_array($creditsarray[$id])) { foreach($creditsarray[$id] as $operation => $policy) { if(isset($view) && $operation == $view) { ?>
<td class="altbg1"><span class="bold"><?=$policy?></span></td>
<? } else { ?>
<td class="smalltxt"><?=$policy?></td>
<? } } } ?>
<td class="altbg1"><?=$extcredits[$id]['lowerlimit']?></td>
</tr>
<? } } ?>
<tr><td colspan="13" class="altbg1"><span class="bold">积分下限:</span> 当您该项积分低于此下限设置的数值时，将无法执行积分策略中涉及扣减此项积分的操作</td></tr>
<tr><td colspan="13" class="altbg1"><span class="bold">总积分计算公式:</span> <?=$creditsformulaexp?></td></tr>
</table>
<? include template('footer'); ?>
