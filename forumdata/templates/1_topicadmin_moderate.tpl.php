<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> <?=$navigation?> &raquo; 
<? if($operation == 'delete') { ?>
删除主题
<? } elseif($operation == 'move') { ?>
移动主题
<? } elseif($operation == 'highlight') { ?>
高亮显示
<? } elseif($operation == 'type') { ?>
主题分类
<? } elseif($operation == 'close') { ?>
关闭/打开主题
<? } elseif($operation == 'stick') { ?>
置顶/解除置顶
<? } elseif($operation == 'digest') { ?>
加入/解除精华
<? } ?>
</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<form method="post" action="topicadmin.php?action=moderate&operation=<?=$operation?>">
<input type="hidden" name="formhash" value="<?=FORMHASH?>">
<input type="hidden" name="fid" value="<?=$fid?>">
<input type="hidden" name="referer" value="<?=$referer?>">

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header">
<td colspan="2">
<? if($operation == 'delete') { ?>
删除主题
<? } elseif($operation == 'move') { ?>
移动主题
<? } elseif($operation == 'highlight') { ?>
高亮显示
<? } elseif($operation == 'type') { ?>
主题分类
<? } elseif($operation == 'close') { ?>
关闭/打开主题
<? } elseif($operation == 'stick') { ?>
置顶/解除置顶
<? } elseif($operation == 'digest') { ?>
<a href="member.php?action=credits&view=digest" target="_blank"><img src="<?=IMGDIR?>/credits.gif" alt="查看积分策略说明" align="right" border="0"></a>加入/解除精华
<? } ?>
</td>
</tr>

<tr>
<td class="altbg1" width="21%">用户名:</td>
<td class="altbg2"><?=$discuz_userss?> <span class="smalltxt">[<a href="<?=$link_logout?>">退出登录</a>]</span></td>
</tr>
<? if($operation == 'move') { ?>
<tr>
<td class="altbg1">目标论坛/分类:</td>
<td class="altbg2"><select name="moveto">
<?=$forumselect?>
</select></td>
</tr>

<tr>
<td class="altbg1" valign="top">移动方式:</td>
<td class="altbg2"><input type="radio" name="type" value="normal" checked> 移动主题<br>
<input type="radio" name="type" value="redirect"> 移动主题并在原来的论坛中保留转向<br></td>
</tr>
<? } elseif($operation == 'highlight') { ?>
<tr>
<td class="altbg1" width="21%">字体样式:</td>
<td class="altbg2">
<input type="checkbox" name="highlight_style[1]" value="1" <?=$stylecheck['1']?>> <b>粗体</b>&nbsp;
<input type="checkbox" name="highlight_style[2]" value="1" <?=$stylecheck['2']?>> <i>斜体</i>&nbsp;
<input type="checkbox" name="highlight_style[3]" value="1" <?=$stylecheck['3']?>> <u>下划线</u>
</td>
</tr>

<tr>
<td class="altbg1" width="21%">字体颜色:</td>
<td class="altbg2">

<table border="0" cellspacing="0" cellpadding="0"><tr>
<td><input type="radio" name="highlight_color" value="0" <?=$colorcheck['0']?>></td><td>默认</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="1" <?=$colorcheck['1']?>></td><td width="20" bgcolor="red">&nbsp;</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="2" <?=$colorcheck['2']?>></td><td width="20" bgcolor="orange">&nbsp;</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="3" <?=$colorcheck['3']?>></td><td width="20" bgcolor="yellow">&nbsp;</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="4" <?=$colorcheck['4']?>></td><td width="20" bgcolor="green">&nbsp;</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="5" <?=$colorcheck['5']?>></td><td width="20" bgcolor="cyan">&nbsp;</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="6" <?=$colorcheck['6']?>></td><td width="20" bgcolor="blue">&nbsp;</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="7" <?=$colorcheck['7']?>></td><td width="20" bgcolor="purple">&nbsp;</td>
<td> &nbsp; <input type="radio" name="highlight_color" value="8" <?=$colorcheck['8']?>></td><td width="20" bgcolor="gray">&nbsp;</td>
</tr></table>
<? } elseif($operation == 'type') { ?>
<tr>
<td class="altbg1">目标论坛/分类:</td>
<td class="altbg2"><?=$typeselect?></td>
</tr>
<? } elseif($operation == 'close') { ?>
<tr>
<td class="altbg1">操作:</td>
<td class="altbg2">
<input type="radio" name="close" value="0" <?=$closecheck['0']?>> 打开主题 &nbsp; &nbsp; 
<input type="radio" name="close" value="1" <?=$closecheck['1']?>> 关闭主题
</tr>
<? } elseif($operation == 'stick') { ?>
<tr>
<td class="altbg1">级别:</td>
<td class="altbg2">
<? if(!$single || $threadlist['0']['displayorder'] > 0) { ?>
<input type="radio" name="level" value="0" onclick="findobj('expirationarea').disabled=1"> 解除置顶 &nbsp; &nbsp; 
<? } ?>
<input type="radio" name="level" value="1" <?=$stickcheck['1']?> onclick="findobj('expirationarea').disabled=0"> <img src="<?=IMGDIR?>/star_level1.gif"> &nbsp; &nbsp; 
<? if($allowstickthread >= 2) { ?>
<input type="radio" name="level" value="2" <?=$stickcheck['2']?> onclick="findobj('expirationarea').disabled=0"> <img src="<?=IMGDIR?>/star_level1.gif"><img src="<?=IMGDIR?>/star_level1.gif"> &nbsp; &nbsp; 
<? if($allowstickthread == 3) { ?>
<input type="radio" name="level" value="3" <?=$stickcheck['3']?> onclick="findobj('expirationarea').disabled=0"> <img src="<?=IMGDIR?>/star_level1.gif"><img src="<?=IMGDIR?>/star_level1.gif"><img src="<?=IMGDIR?>/star_level1.gif"></td>
<? } } ?>
</tr>
<? } elseif($operation == 'digest') { ?>
<tr>
<td class="altbg1">级别:</td>
<td class="altbg2">
<? if(!$single || ($single && $threadlist['0']['digest'])) { ?>
<input type="radio" name="level" value="0" <?=$digestcheck['0']?> onclick="findobj('expiration').disabled=1"> 解除精华 &nbsp; &nbsp; 
<? } ?>
<input type="radio" name="level" value="1" <?=$digestcheck['1']?> onclick="findobj('expiration').disabled=0"> <img src="<?=IMGDIR?>/star_level1.gif"> &nbsp; &nbsp; 
<input type="radio" name="level" value="2" <?=$digestcheck['2']?> onclick="findobj('expiration').disabled=0"> <img src="<?=IMGDIR?>/star_level1.gif"><img src="<?=IMGDIR?>/star_level1.gif"> &nbsp; &nbsp; 
<input type="radio" name="level" value="3" <?=$digestcheck['3']?> onclick="findobj('expiration').disabled=0"> <img src="<?=IMGDIR?>/star_level1.gif"><img src="<?=IMGDIR?>/star_level1.gif"><img src="<?=IMGDIR?>/star_level1.gif"></td></tr>
<? } if(in_array($operation, array('stick', 'digest', 'highlight', 'close'))) { ?>
<tr id="expirationarea">
<td class="altbg1">有效期:</td>
<td class="altbg2"><span class="smalltxt"><input type="text" name="expiration" size="10" value="<?=$expirationdefault?>" maxlength="10"> 本操作的有效期限，格式为 yyyy-mm-dd，范围 <u><?=$expirationmin?></u> 至 <u><?=$expirationmax?></u>，留空为不限制</a></td>
</tr>
<? } include template('topicadmin_reason'); if(in_array($operation, array('stick', 'digest', 'highlight'))) { ?>
<tr>
<td class="altbg1">后续操作:</td>
<td class="altbg2"><input type="radio" name="next" value="" checked> 无 &nbsp; 
<? if($operation != 'highlight') { ?>
<input type="radio" name="next" value="highlight"> 高亮显示 &nbsp; 
<? } if($operation != 'stick') { ?>
<input type="radio" name="next" value="stick"> 置顶/解除置顶 &nbsp; 
<? } if($operation != 'digest') { ?>
<input type="radio" name="next" value="digest"> 加入/解除精华 &nbsp; 
<? } ?>
</tr>
<? } ?>
</table><br>
<? if($single) { ?>
<input type="hidden" name="moderate[]" value="<?=$moderate['0']?>">
<? if($loglist) { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr class="header"><td colspan="4">主题管理记录</td></tr>
<tr align="center" class="category"><td width="15%">操作者</td><td width="25%">时间</td><td width="30%">管理操作</td><td width="30%">有效期</td></tr>
<? if(is_array($loglist)) { foreach($loglist as $log) { ?>
<tr align="center">
<td class="altbg1">
<? if($log['uid']) { ?>
<a href="viewpro.php?uid=<?=$log['uid']?>" target="_blank"><?=$log['username']?></a>
<? } else { ?>
任务系统
<? } ?>
</td>
<td class="altbg2"><?=$log['dateline']?></td>
<td class="altbg1" <?=$log['status']?>><?=$modactioncode[$log['action']]?></td>
<td class="altbg2" <?=$log['status']?>>
<? if($log['expiration']) { ?>
<?=$log['expiration']?>
<? } elseif(in_array($log['action'], array('STK', 'HLT', 'DIG', 'CLS', 'OPN'))) { ?>
永久有效
<? } ?>
</td>
</tr>
<? } } ?>
</table><br>
<? } } else { ?>
<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr align="center" class="header">
<td width="48">&nbsp;</td>
<td width="42%">标题</td>
<td>作者</td>
<td>回复</td>
<td>最后发表</td>
</tr>
<? if(is_array($threadlist)) { foreach($threadlist as $thread) { ?>
<tr>
<td class="altbg1" align="center"><input type="checkbox" name="moderate[]" value="<?=$thread['tid']?>" checked></td>
<td class="altbg2" onMouseOver="this.className='altbg1'" onMouseOut="this.className='altbg2'"><a href="viewthread.php?tid=<?=$thread['tid']?>&extra=<?=$extra?>"><?=$thread['subject']?></a></td>
<td class="altbg1" align="center">
<? if($thread['author']) { ?>
<a href="viewpro.php?uid=<?=$thread['authorid']?>"><?=$thread['author']?></a>
<? } else { ?>
匿名
<? } ?>
</td>
<td class="altbg2" align="center"><?=$thread['replies']?></td>
<td class="altbg1" align="center"><font class="smalltxt"><?=$thread['lastpost']?> by 
<? if($thread['lastposter']) { ?>
<a href="viewpro.php?username=<?=$thread['lastposterenc']?>"><?=$thread['lastposter']?></a>
<? } else { ?>
匿名
<? } ?>
</font></td>
</tr>
<? } } ?>
</td></tr></table><br>
<? } ?>
<center><input type="submit" name="modsubmit" value="提 &nbsp; 交"></center>
</form>
<? include template('footer'); ?>
