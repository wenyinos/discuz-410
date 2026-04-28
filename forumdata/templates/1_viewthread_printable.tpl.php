<? if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>

<html>
<head>
<title><?=$bbname?> - Powered by Discuz! Board</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charset?>">
<style type="text/css">
body,table {font-size: <?=FONTSIZE?>; font-family: <?=FONT?> }
</style>
</head>

<body leftmargin="80">
<img src="<?=IMGDIR?>/<?=BOARDIMG?>" alt="Board logo" border="0"><br><br>
<b>标题: </b><?=$thread['subject']?> <b><a href="###" onclick="this.style.visibility='hidden';window.print();this.style.visibility='visible'">[打印本页]</a></b></span><br>
<? if(is_array($postlist)) { foreach($postlist as $post) { ?>
<hr noshade size="2" width="100%" color="#808080">
<b>作者: </b>
<? if($post['author'] && !$post['anonymous']) { ?>
<?=$post['author']?>
<? } else { ?>
匿名
<? } ?>
&nbsp; &nbsp; <b>时间: </b><?=$post['dateline']?>
<? if($post['subject']) { ?>
 &nbsp; &nbsp; <b>标题: </b><?=$post['subject']?>
<? } ?>
<br><br><?=$post['message']?>
<? if(is_array($post['attachments'])) { foreach($post['attachments'] as $attach) { ?>
<br><br><?=$attach['attachicon']?> 
<? if(!$attach['attachimg'] || !$allowgetattach) { ?>
附件: 
<? if($attach['description']) { ?>
[<?=$attach['description']?>]
<? } ?>
 <b><?=$attach['filename']?></b> (<?=$attach['dateline']?>, <?=$attach['attachsize']?>) / 该附件被下载次数 <?=$attach['downloads']?><br><?=$boardurl?>attachment.php?aid=<?=$attach['aid']?>
<? } else { ?>
图片附件: 
<? if($attach['description']) { ?>
[<?=$attach['description']?>]
<? } ?>
 <b><?=$attach['filename']?></b> (<?=$attach['dateline']?>, <?=$attach['attachsize']?>) / 该附件被下载次数 <?=$attach['downloads']?><br><?=$boardurl?>attachment.php?aid=<?=$attach['aid']?><br><br><img src="<?=$attachurl?>/<?=$attach['attachment']?>" border="0" onload="if(this.width>screen.width*0.8) this.width=screen.width*0.8">
<? } } } } } ?>
<br><br><br><br><hr noshade size="2" width="100%" color="<?=BORDERCOLOR?>">
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center" style="font-size: <?=SMFONTSIZE?>; font-family: <?=SMFONT?>">
<tr><td>欢迎光临 <?=$bbname?> (<?=$boardurl?>)</td>
<td align="right">
Powered by Discuz! <?=$version?></td></tr></table>

</body>
</html>