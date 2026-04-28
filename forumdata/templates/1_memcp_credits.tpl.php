<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); include template('memcp_navbar'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center">
<tr><td width="200" valign="top">
<? if($exchangestatus || $transferstatus || $ec_ratio) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="98%" class="tableborder">
<tr class="header"><td colspan="2" align="center">积分交易</td></tr>
<? if($exchangestatus) { ?>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="memcp.php?action=credits&operation=exchange">
<? if($operation == 'exchange') { ?>
<b>积分兑换</b>
<? } else { ?>
积分兑换
<? } ?>
</a></td></tr>
<? } if($transferstatus) { ?>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="memcp.php?action=credits&operation=transfer">
<? if($operation == 'transfer') { ?>
<b>积分转账</b>
<? } else { ?>
积分转账
<? } ?>
</a></td></tr>
<? } if($ec_ratio) { ?>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="memcp.php?action=credits&operation=addfunds">
<? if($operation == 'addfunds') { ?>
<b>积分充值</b>
<? } else { ?>
积分充值
<? } ?>
</a></td></tr>
<? } ?>
</table><br>
<? } ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="98%" class="tableborder">
<tr class="header"><td colspan="2" align="center">积分记录</td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="memcp.php?action=credits&operation=creditslog">
<? if($operation == 'creditslog') { ?>
<b>转账与兑换记录</b>
<? } else { ?>
转账与兑换记录
<? } ?>
</a></td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="memcp.php?action=credits&operation=paymentlog">
<? if($operation == 'paymentlog') { ?>
<b>主题付费记录</b>
<? } else { ?>
主题付费记录
<? } ?>
</a></td></tr>
<tr><td class="altbg1" width="20" align="center"><img src="<?=IMGDIR?>/foldersmall.gif"></td><td class="altbg2">
<a href="memcp.php?action=credits&operation=incomelog">
<? if($operation == 'incomelog') { ?>
<b>主题收益记录</b>
<? } else { ?>
主题收益记录
<? } ?>
</a></td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="98%" class="tableborder">
<tr class="header"><td colspan="3" align="center">积分概况</td></tr>
<tr class="altbg2"><td colspan="3" class="smalltxt">
<b>&raquo;</b> 积分: <span class="bold"><?=$credits?></span><br>
<? if(is_array($extcredits)) { foreach($extcredits as $id => $credit) { ?>
<b>&raquo;</b> <?=$credit['title']?>: <span class="bold"><?=$GLOBALS[extcredits.$id]?></span> <?=$credit['unit']?><br>
<? } } ?>
</td></tr></table><br>

</td><td align="right" valign="top">
<? if($operation == 'transfer') { ?>
<form method="post" action="memcp.php?action=credits">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="operation" value="transfer">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="99%" class="tableborder">
<tr>
<td width="100%" colspan="2" class="header">积分转账</td>
</tr>

<tr>
<td class="altbg1" width="15%">密码:</td>
<td class="altbg2" width="85%"><input type="password" size="15" name="password"></td>
</tr>

<tr>
<td class="altbg1" width="15%">到:</td>
<td class="altbg2" width="85%"><input type="text" size="15" name="to"></td>
</tr>

<tr>
<td class="altbg1" width="15%"><?=$extcredits[$creditstrans]['title']?>:</td>
<td class="altbg2" width="85%"><input type="text" size="15" name="amount" value="0"> <?=$extcredits[$creditstrans]['unit']?></td>
</tr>

<tr>
<td class="altbg1" width="15%">交易后最低余额:</td>
<td class="altbg2" width="85%"><?=$transfermincredits?> <?=$extcredits[$creditstrans]['unit']?></td>
</tr>

<tr>
<td class="altbg1" width="15%">积分交易税:</td>
<td class="altbg2" width="85%"><?=$taxpercent?></td>
</tr>

<tr>
<td class="altbg1" width="15%" valign="top">附言:<br><span class="smalltxt">如果输入附言，系统将自动向接收者发送短消息通知</span></td>
<td class="altbg2" width="85%"><textarea name="transfermessage" rows="8" style="width: 85%; word-break: break-all"></textarea></td>
</tr>

<tr class="altbg1">
<td colspan="2" class="smalltxt"><ul><li>积分转账可以根据论坛管理员设置的交易积分，将您的积分转让给其他用户。<li>接收者收到积分的实际数值，是被扣除交易税后计算出来的，即只要进行积分交易，就可能会产生交易损失。<li>积分交易一旦提交不可恢复，请确定无误后再进行操作。</td>
</tr>

</table><br>
<center><input type="button" name="" value="计 &nbsp; 算" onclick="this.form.amount.value=Math.floor(this.form.amount.value);alert('接收者的所得为 <?=$extcredits[$creditstrans]['title']?> '+Math.floor(this.form.amount.value*(1-<?=$creditstax?>)))"> &nbsp; 
<input type="submit" name="creditssubmit" value="提 &nbsp; 交" onclick="return confirm('积分操作不能恢复，您确认吗?');">
</center></form>
<? } elseif($operation == 'exchange') { ?>
<form method="post" action="memcp.php?action=credits">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="operation" value="exchange">

<script language="JavaScript">
ratioarray = new Array();
<? if(is_array($exchcredits)) { foreach($exchcredits as $id => $ecredits) { ?>
ratioarray[<?=$id?>] = <?=$ecredits['ratio']?>;
<? } } ?>
</script>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="99%" class="tableborder">
<tr>
<td width="100%" colspan="2" class="header">积分兑换</td>
</tr>

<tr>
<td class="altbg1" width="15%">密码:</td>
<td class="altbg2" width="85%"><input type="password" size="15" name="password"></td>
</tr>

<tr>
<td class="altbg1" width="15%">积分数量:</td>
<td class="altbg2" width="85%"><input type="text" size="15" name="amount" value="0"></td>
</tr>

<tr>
<td class="altbg1" width="15%">原积分:</td>
<td class="altbg2" width="85%">
<select name="fromcredits">
<? if(is_array($exchcredits)) { foreach($exchcredits as $id => $credit) { ?>
<option value="<?=$id?>"><?=$credit['title']?> (兑换比率 <?=$credit['ratio']?>)</option>
<? } } ?>
</select>
</td>
</tr>

<tr>
<td class="altbg1">目标积分:</td>
<td class="altbg2">
<select name="tocredits">
<? if(is_array($exchcredits)) { foreach($exchcredits as $id => $ecredits) { ?>
<option value="<?=$id?>"><?=$ecredits['title']?> (兑换比率 <?=$ecredits['ratio']?>)</option>
<? } } ?>
</select>
</td>
</tr>

<tr>
<td class="altbg1" width="15%">交易后最低余额:</td>
<td class="altbg2" width="85%"><?=$exchangemincredits?></td>
</tr>

<tr>
<td class="altbg1" width="15%">积分交易税:</td>
<td class="altbg2" width="85%"><?=$taxpercent?></td>
</tr>

<tr class="altbg1">
<td colspan="2" class="smalltxt"><ul><li>积分兑换是根据论坛管理员设置的可兑换积分，将您自己的某种积分，兑换成另外一种积分。<li>兑换比率为该项积分对应一个单位标准积分的值。例如兑换比率为 2 的积分 1 分，相当于兑换比率为 1 的积分 2 分，即兑换比率越大，该项积分越有价值。<li>兑换成目标积分的实际数值，是按照兑换比率折算的目标积分，并扣除交易税后计算出来的，即只要进行积分交易，就可能会产生交易损失。<li>积分交易一旦提交不可恢复，请确定无误后再进行操作。</td>
</tr>

</table><br>
<center><input type="button" name="" value="计 &nbsp; 算" onclick="this.form.amount.value=Math.floor(this.form.amount.value);alert('兑换后所得目标积分为 '+Math.floor(this.form.amount.value*ratioarray[this.form.fromcredits.options[this.form.fromcredits.selectedIndex].value]*(1-<?=$creditstax?>)/ratioarray[this.form.tocredits.options[this.form.tocredits.selectedIndex].value]))"> &nbsp; 
<input type="submit" name="creditssubmit" value="提 &nbsp; 交" onclick="return confirm('积分操作不能恢复，您确认吗?');">
</center></form>
<? } elseif($operation == 'addfunds') { ?>
<form method="post" action="memcp.php?action=credits" target="_blank">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="operation" value="addfunds">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="99%" class="tableborder">
<tr>
<td width="100%" colspan="2" class="header">积分充值</td>
</tr>

<tr>
<td class="altbg1" width="15%" valign="top">充值规则:</td>
<td class="altbg2" width="85%">
人民币现金 <b>1</b> 元 = <?=$extcredits[$creditstrans]['title']?> <b><?=$ec_ratio?></b> <?=$extcredits[$creditstrans]['unit']?>
<? if($ec_mincredits) { ?>
<br>单次最低充值 <?=$extcredits[$creditstrans]['title']?> <b><?=$ec_mincredits?></b> <?=$extcredits[$creditstrans]['unit']?>
<? } if($ec_maxcredits) { ?>
<br>单次最高充值 <?=$extcredits[$creditstrans]['title']?> <b><?=$ec_maxcredits?></b> <?=$extcredits[$creditstrans]['unit']?>
<? } if($ec_maxcreditspermonth) { ?>
<br>最近 30 天最高充值 <?=$extcredits[$creditstrans]['title']?> <b><?=$ec_maxcreditspermonth?></b> <?=$extcredits[$creditstrans]['unit']?>
<? } ?>
</td>
</tr>

<tr>
<td class="altbg1" width="15%"><?=$extcredits[$creditstrans]['title']?> 账户充值数额:</td>
<td class="altbg2" width="85%"><input type="text" size="15" name="amount" value="0"> <?=$extcredits[$creditstrans]['unit']?></td>
</tr>

<tr>
<td colspan="2" class="category"><ul><li>您可以以人民币现金在线支付的形式，为您的交易积分账户充值用于购买帖子、用户组权限或其他虚拟消费活动。<li>积分充值不能撤销或退款，因此请您在充值前确定是否需要，及仔细核对充值的金额。<li><b>您成功支付后有系统可能需要几分钟的时间等待支付结果，因此可能无法瞬间入账，请注意查收系统发送的短消息。如果超过 48 小时仍未收到通知短消息，请与论坛管理员联系。</b></td>
</tr>

</table><br>
<center><input type="button" name="" value="计 &nbsp; 算" onclick="this.form.amount.value=Math.floor(this.form.amount.value);alert('您需要在线支付的人民币(元)金额为 '+Math.ceil(this.form.amount.value/<?=$ec_ratio?>*100)/100)"> &nbsp; 
<input type="submit" name="creditssubmit" value="提 &nbsp; 交">
</center></form>
<? } elseif($operation == 'paymentlog') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="7">主题付费记录</td></tr>
<tr align="center" class="category">
<td width="33%">标题</td>
<td width="10%">作者</td>
<td width="8%">发布时间</td>
<td width="18%">论坛</td>
<td width="8%">付费时间</td>
<td width="9%">售价</td>
<td width="9%">作者所得</td>
</tr>
<? if($loglist) { if(is_array($loglist)) { foreach($loglist as $log) { ?>
<tr>
<td class="altbg1"><a href="viewthread.php?tid=<?=$log['tid']?>"><?=$log['subject']?></a></td>
<td class="altbg2" align="center"><a href="viewpro.php?uid=<?=$log['authorid']?>"><?=$log['author']?></a></td>
<td class="altbg1" align="center"><?=$log['tdateline']?></td>
<td class="altbg2" align="center"><a href="forumdisplay.php?fid=<?=$log['fid']?>"><?=$log['name']?></a></td>
<td class="altbg1" align="center"><?=$log['dateline']?></td>
<? if(!$log['amount'] && !$log['netamount']) { ?>
<td class="altbg2" align="center" colspan="2">已退款</td>
<? } else { ?>
<td class="altbg2" align="center"><?=$extcredits[$creditstrans]['title']?> <?=$log['amount']?> <?=$extcredits[$creditstrans]['unit']?></td>
<td class="altbg1" align="center"><?=$extcredits[$creditstrans]['title']?> <?=$log['netamount']?> <?=$extcredits[$creditstrans]['unit']?></td>
<? } ?>
</tr>
<? } } } else { ?>
<td class="altbg1" colspan="7">目前没有积分交易记录。</td></tr>
<? } ?>
</table>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center"><tr><td><?=$multipage?></td></tr></table>
<? } elseif($operation == 'incomelog') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="7">主题收益记录</td></tr>
<tr align="center" class="category">
<td width="33%">标题</td>
<td width="8%">发布时间</td>
<td width="18%">论坛</td>
<td width="10%">购买者</td>
<td width="8%">付费时间</td>
<td width="9%">售价</td>
<td width="9%">作者所得</td>
</tr>
<? if($loglist) { if(is_array($loglist)) { foreach($loglist as $log) { ?>
<tr>
<td class="altbg1"><a href="viewthread.php?tid=<?=$log['tid']?>"><?=$log['subject']?></a></td>
<td class="altbg2" align="center"><?=$log['tdateline']?></td>
<td class="altbg1" align="center"><a href="forumdisplay.php?fid=<?=$log['fid']?>"><?=$log['name']?></a></td>
<td class="altbg2" align="center"><a href="viewpro.php?uid=<?=$log['uid']?>"><?=$log['username']?></a></td>
<td class="altbg1" align="center"><?=$log['dateline']?></td>
<? if(!$log['amount'] && !$log['netamount']) { ?>
<td class="altbg2" align="center" colspan="2">已退款</td>
<? } else { ?>
<td class="altbg2" align="center"><?=$extcredits[$creditstrans]['title']?> <?=$log['amount']?> <?=$extcredits[$creditstrans]['unit']?></td>
<td class="altbg1" align="center"><?=$extcredits[$creditstrans]['title']?> <?=$log['netamount']?> <?=$extcredits[$creditstrans]['unit']?></td>
<? } ?>
</tr>
<? } } } else { ?>
<td class="altbg1" colspan="7">目前没有积分交易记录。</td></tr>
<? } ?>
</table>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center"><tr><td><?=$multipage?></td></tr></table>
<? } elseif($operation == 'creditslog') { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="5">转账与兑换记录</td></tr>
<tr align="center" class="category"><td width="20%">来自/到</td><td width="25%">时间</td><td width="15%">支出</td><td width="15%">收入</td><td width="25%">操作</td></tr> 
<? if($loglist) { if(is_array($loglist)) { foreach($loglist as $log) { ?>
<tr align="center">
<td class="altbg1">
<? if($log['fromto'] == 'BANK ACCOUNT') { ?>
银行现金转入
<? } else { ?>
<a href="viewpro.php?username=<?=$log['fromtoenc']?>"><?=$log['fromto']?></a>
<? } ?>
</td>
<td class="altbg2"><?=$log['dateline']?></td>
<td class="altbg1">
<? if($log['send']) { ?>
<?=$extcredits[$log['sendcredits']]['title']?> <?=$log['send']?> <?=$extcredits[$log['sendcredits']]['unit']?>
<? } ?>
</td>
<td class="altbg2">
<? if($log['receive']) { ?>
<?=$extcredits[$log['receivecredits']]['title']?> <?=$log['receive']?> <?=$extcredits[$log['receivecredits']]['unit']?>
<? } ?>
</td>
<td class="altbg1">
<? if($log['operation'] == 'TFR') { ?>
积分转出
<? } elseif($log['operation'] == 'RCV') { ?>
积分转入
<? } elseif($log['operation'] == 'EXC') { ?>
积分兑换
<? } elseif($log['operation'] == 'UGP') { ?>
公众用户组收费
<? } elseif($log['operation'] == 'AFD') { ?>
银行现金转入
<? } ?>
</td>
</tr>
<? } } } else { ?>
<td class="altbg1" colspan="5">目前没有积分交易记录。</td></tr>
<? } ?>
</table>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center"><tr><td><?=$multipage?></td></tr></table>
<? } ?>
</td></tr></table>
<? include template('footer'); ?>
