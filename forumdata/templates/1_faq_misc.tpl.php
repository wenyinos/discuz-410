<? if(!defined('IN_DISCUZ')) exit('Access Denied'); include template('header'); ?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=TABLEWIDTH?>" align="center" style="table-layout: fixed"> 
<tr><td class="nav" width="90%" align="left" nowrap>&nbsp;<a href="index.php"><?=$bbname?></a> &raquo; <a href="faq.php">帮助</a> &raquo; 其他问题</td>
<td align="right" width="10%">&nbsp;<a href="#bottom"><img src="<?=IMGDIR?>/arrow_dw.gif" border="0" align="absmiddle"></a></td>        
</tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">其他问题</td></tr>
<tr><td width="100%" class="altbg2" class="smalltxt"><ul>
<li><a href="faq.php?page=misc#1">能说明一下 Discuz! 代码的用法吗？</a></li>
<li><a href="faq.php?page=misc#2">普通用户如何成为版主？</a></li>
<li><a href="faq.php?page=misc#3">我如何在 <?=$bbname?> 具有更多的权限？</a></li>
</td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">能说明一下 Discuz! 代码的用法吗？</td><a name=#1></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">
&nbsp; &nbsp; 您可以使用 Discuz! 代码--一个 HTML 代码的简化版本，来简化对帖子显示格式的控制。<br><br>
<ol type="1">
<li>[b]粗体文字 Abc[/b] &nbsp; 效果:<b>粗体文字 Abc</b> （粗体字）<br><br></li>
<li>[i]斜体文字 Abc[/i] &nbsp; 效果:<i>斜体文字 Abc</i> （斜体字）<br><br></li>
<li>[u]下划线文字 Abc[/u] &nbsp; 效果:<u>下划线文字 Abc</u> （下划线）<br><br></li>
<li>[color=red]红颜色[/color] &nbsp; 效果:<font color="red">红颜色</font> （改变文字颜色）<br><br></li>
<li>[size=3]文字大小为 3[/size] &nbsp; 效果:<font size="3">文字大小为 3</font> （改变文字大小）<br><br></li>
<li>[font=仿宋]字体为仿宋[/font] &nbsp; 效果:<font face"仿宋">字体为仿宋</font> （改变字体）<br><br></li>
<li>[align=Center]内容居中[/align] &nbsp; （格式内容位置） 效果:<br><center>内容居中</center><br></li>
<li>[url]http://www.comsenz.com[/url] &nbsp; 效果:<a href="http://www.comsenz.com" target="_blank">http://www.comsenz.com</a> （超级连接）<br><br></li>
<li>[url=http://www.Discuz.net]Discuz! 论坛[/url] &nbsp; 效果:<a href="http://www.Discuz.net" target="_blank">Discuz! 论坛</a> （超级连接）<br><br></li>
<li>[email]myname@mydomain.com[/email] &nbsp; 效果:<a href="mailto:myname@mydomain.com">myname@mydomain.com</a> （E-Mail 链接）<br><br></li>
<li>[email=support@discuz.net]Discuz! 技术支持[/email] &nbsp; 效果:<a href="mailto:support@discuz.net">Discuz! 技术支持</a> （E-Mail 链接）<br><br></li>
<li>[quote]Discuz! Board 是由北京康盛世纪科技有限公司开发的论坛软件[/quote] &nbsp; （引用内容，类似的代码还有 [code][/code]）<br><br></li>
<li>[hide]免费帐号为: username/password[/hide] &nbsp; （按回复隐藏内容，仅限版主及管理员使用）<br>效果:只有当浏览者回复本帖时，才显示其中的内容，否则显示为“<b>**** 隐藏信息 跟帖后才能显示 *****</b>”<br><br></li>
<li>[hide=20]免费帐号为: username/password[/hide] &nbsp; （按积分隐藏内容，仅限版主及管理员使用）<br>效果:只有当浏览者积分高于 20 点时，才显示其中的内容，否则显示为“<b>**** 隐藏信息 积分高于 20 点才能显示 ****</b>”<br><br></li>
<li>[list]<br>
[*]列表项 #1<br>
[*]列表项 #2<br>
[*]列表项 #3<br>
[/list] &nbsp; (列表)<br><br></li>
<? if(is_array($discuzcodes)) { foreach($discuzcodes as $code) { ?>
<li><?=$code['example']?> &nbsp; (<?=$code['explanation']?>)<br><br></li>
<? } } ?>
<br>以下 Discuz! 代码需论坛可用 [img] 代码才能使用<hr noshade size="0" width="50%" color="<?=BORDERCOLOR?>" align="left"><br>
<li>[img]<?=$boardurl?>images/default/logo.gif[/img] &nbsp; （链接图像）<br>效果:<br><img src="images/default/logo.gif"> <br><br></li>
<li>[img=88,31]<?=$boardurl?>images/logo.gif[/img] &nbsp;（链接图像并限制大小）<br> 效果:<br><img src="images/logo.gif" height="31" width="88"> <br><br></li>
<li>[swf]<?=$boardurl?>images/banner.swf[/swf] &nbsp; （链接 flash 动画，用法与 [img] 类似）<br><br></li>
</ol>
</td></tr></table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">普通用户如何成为版主？</td><a name=#2></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 论坛的版主是自愿申请的，管理员可能会要求版主需要达到一定积分，或在论坛注册超过一定时间等。版主应该是诚实守信、乐于助人、大公无私的表率，同时还要熟悉专业，经验丰富，有良好的口碑。如果你确认已经达到上面几点，并希望担任本站的版主，可以与管理员联系。</td></tr>
</table><br>

<table cellspacing="<?=INNERBORDERWIDTH?>" cellpadding="<?=TABLESPACE?>" width="<?=TABLEWIDTH?>" align="center" class="tableborder">
<tr><td width="100%" class="header">我如何在 <?=$bbname?> 具有更多的权限？</td><a name=#3></a></tr>
<tr><td width="100%" class="altbg2" class="smalltxt">&nbsp; &nbsp; 本站所使用的 Discuz! 论坛是按照系统头衔和用户积分区分的，积分可以参考您的发帖量，其他用户的评分，或两者综合来决定。
当积分达到一定等级要求时，系统会自动为您开通新的权限，并给予相应星星标志。因此，拥有较高的积分数，不仅代表您在本论坛的资历与活跃程度，同时也意味着能够拥有比其他
用户更多的高级权限。</td></tr>
</table>
<? include template('footer'); ?>
