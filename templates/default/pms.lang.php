<?php

// P.M. Pack for Discuz! Version 1.0
// Translated by Crossday

// ATTENTION: Please add slashes(\) before (') and (")

$language = array
(

	'reason_moderate_subject' => '[Discuz!] 您发表的主题被执行管理操作',
	'reason_moderate_message' => '这是由论坛系统自动发送的通知短消息。

[b]以下您所发表的主题被 [url={$boardurl}viewpro.php?uid={$discuz_uid}][i]{$discuz_user}[/i][/url] 执行 {$modaction} 操作。[/b]

[b]主题:[/b] [url={$boardurl}viewthread.php?tid={$thread[tid]}]{$thread[subject]}[/url]
[b]发表时间:[/b] {$thread[dateline]}
[b]所在论坛:[/b] [url={$boardurl}forumdisplay.php?fid={$fid}]{$forumname}[/url]

[b]操作理由:[/b] {$reason}

如果您对本管理操作有异议，请与我取得联系。',

	'reason_delete_post_subject' => '[Discuz!] 您发表的回复被执行管理操作',
	'reason_delete_post_message' => '这是由论坛系统自动发送的通知短消息。

[b]以下您所发表的回复被 [url={$boardurl}viewpro.php?uid={$discuz_uid}][i]{$discuz_user}[/i][/url] 执行 删除 操作。[/b]
[quote]{$post[message]}[/quote]

[b]发表时间:[/b] {$post[dateline]}
[b]所在论坛:[/b] [url={$boardurl}forumdisplay.php?fid={$fid}]{$forumname}[/url]

[b]操作理由:[/b] {$reason}

如果您对本管理操作有异议，请与我取得联系。',

	'reason_move_subject' => '[Discuz!] 您发表的主题被执行管理操作',
	'reason_move_message' => '这是由论坛系统自动发送的通知短消息。

[b]以下您所发表的主题被 [url={$boardurl}viewpro.php?uid={$discuz_uid}][i]{$discuz_user}[/i][/url] 执行 移动 操作。[/b]

[b]主题:[/b] [url={$boardurl}viewthread.php?tid={$thread[tid]}]{$thread[subject]}[/url]
[b]发表时间:[/b] {$thread[dateline]}
[b]原论坛:[/b] [url={$boardurl}forumdisplay.php?fid={$fid}]{$forumname}[/url]
[b]目标论坛:[/b] [url={$boardurl}forumdisplay.php?fid={$toforum[fid]}]{$toforum[name]}[/url]

[b]操作理由:[/b] {$reason}

如果您对本管理操作有异议，请与我取得联系。',

	'rate_reason_subject' => '[Discuz!] 您发表的帖子被评分',
	'rate_reason_message' => '这是由论坛系统自动发送的通知短消息。

[b]以下您所发表的帖子被 [url={$boardurl}viewpro.php?uid={$discuz_uid}][i]{$discuz_user}[/i][/url] 评分。[/b]
[quote]{$post[message]}[/quote]

[b]发表时间:[/b] {$post[dateline]}
[b]所在论坛:[/b] [url={$boardurl}forumdisplay.php?fid={$fid}]{$forumname}[/url]
[b]所在主题:[/b] [url={$boardurl}viewthread.php?tid={$tid}&page={$page}#pid{$pid}]{$thread[subject]}[/url]

[b]评分分数:[/b] {$ratescore}
[b]操作理由:[/b] {$reason}',

	'transfer_subject' => '[Discuz!] 您收到一笔积分转账',
	'transfer_message' => '这是由论坛系统自动发送的通知短消息。

[b]您收到一笔来自他人的积分转账。[/b]

[b]来自:[/b] [url={$boardurl}viewpro.php?uid={$discuz_uid}][i]{$discuz_user}[/i][/url]
[b]时间:[/b] {$transfertime}
[b]积分:[/b] {$extcredits[$creditstrans][title]} {$amount} {$extcredits[$creditstrans][unit]}
[b]净收入:[/b] {$extcredits[$creditstrans][title]} {$netamount} {$extcredits[$creditstrans][unit]}

[b]附言:[/b] {$transfermessage}

详情请[url={$boardurl}memcp.php?action=credits&operation=creditslog]点击这里[/url]访问您的积分转账与兑换记录。',

	'reportpost_subject'	=> '[Discuz!] $discuz_user 向您报告一篇帖子',
	'reportpost_message'	=> '[i]{$discuz_user}[/i] 向您报告以下的帖子，详细内容请访问:
[url]{$posturl}[/url]

他/她的报告理由是: {$reason}',

	'addfunds_subject' => '[Discuz!] 积分充值成功完成',
	'addfunds_message' => '这是由论坛系统自动发送的通知短消息。

[b]您提交的积分充值请求已成功完成，相应数额的积分已经存入您的积分账户。[/b]

[b]订单号:[/b] {$order[orderid]}
[b]提交时间:[/b] {$submitdate}
[b]确认时间:[/b] {$confirmdate}

[b]支出:[/b] 人民币 {$order[price]} 元
[b]收入:[/b] {$extcredits[$creditstrans][title]} {$order[amount]} {$extcredits[$creditstrans][unit]}

详情请[url={$boardurl}memcp.php?action=credits&operation=creditslog]点击这里[/url]访问您的积分转账与兑换记录。'

);

?>