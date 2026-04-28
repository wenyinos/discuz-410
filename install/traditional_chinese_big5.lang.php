<?php

$lang = array
(

	'username' => '管理員賬號:',
	'password' => '管理員密碼:',
	'repeat_password' => '重複密碼:',
	'admin_email' => '管理員 Email:',

	'succeed' => '成功\',
	'fail' => '失敗',
	'exit' => '退出安裝嚮導',
	'enabled' => '允許\',
	'writeable' => '可寫',
	'unwriteable' => '不可寫',
	'unlimited' => '不限',

	'env_os' => '操作系統',
	'env_php' => 'PHP 版本',
	'env_mysql' => 'MySQL 版本',
	'env_attach' => '附件上傳',
	'env_diskspace' => '磁盤空間',
	'env_dir_writeable' => '目錄寫入',

	'init_log' => '初始化記錄',
	'clear_dir' => '清空目錄',
	'select_db' => '選擇數據庫',
	'create_table' => '建立數據表',

	'install_wizard' => 'Discuz! Board Installation Wizard',
	'welcome' => '歡迎來到 Crossday Discuz! Board 安裝嚮導，安裝前請仔細閱讀 license 檔的每個細節，在您確定可以完全滿足 Discuz! 的授權協議之後才能開始安裝。readme 檔提供了有關軟件安裝的說明，請您同樣仔細閱讀，以保證安裝進程的順利進行。',
	'current_process' => '當前狀態:',
	'show_license' => 'Discuz! 用戶許可協議',
	'agreement' => '請您務必仔細閱讀下面的許可協議',
	'agreement_yes' => '我完全同意',
	'agreement_no' => '我不能同意',
	'configure' => '配置 config.inc.php',
	'check_config' => '檢查配置文件狀態',
	'check_existence' => '存在檢查',
	'check_writeable' => '可寫檢查',
	'edit_config' => '瀏覽/編輯當前配置',
	'variable' => '設置選項',
	'value' => '當前值',
	'comment' => '註釋',
	'dbhost' => '數據庫服務器:',
	'dbhost_comment' => '數據庫服務器地址, 一般為 localhost',
	'dbuser' => '數據庫用戶名:',
	'dbuser_comment' => '數據庫賬號用戶名',
	'dbpw' => '數據庫密碼:',
	'dbpw_comment' => '數據庫賬號密碼',
	'dbname' => '數據庫名:',
	'dbname_comment' => '數據庫名稱',
	'email' => '系統 Email:',
	'email_comment' => '用於發送程序錯誤報告',
	'tablepre' => '表名前綴:',
	'tablepre_comment' => '同一數據庫安裝多論壇時可改變默認',
	'tablepre_prompt' => '除非您需要在同一數據庫安裝多個 Discuz! \n論壇,否則,強烈建議您不要修改表名前綴.',
	'save_config' => '保存配置信息',
	'confirm_config' => '上述配置正確',
	'refresh_config' => '刷新修改結果',
	'recheck_config' => '重新檢查設置',
	'check_env' => '檢查當前服務器環境',
	'compare_env' => 'Discuz! 所需環境和當前服務器配置對比',
	'env_required' => 'Discuz! 所需配置',
	'env_best' => 'Discuz! 最佳配置',
	'env_current' => '當前服務器',
	'confirm_preparation' => '請確認已完成如下步驟',
	'install_note' => '安裝嚮導提示',
	'add_admin' => '設置管理員賬號',
	'start_install' => '開始安裝 Discuz!',
	'installing' => '檢查管理員賬號信息並開始安裝 Discuz!。',
	'check_admin' => '檢查管理員賬號',
	'check_admin_validity' => '檢查信息合法性',
	'admin_username_invalid' => '用戶名空, 長度超過限制或包含非法字符.',
	'admin_password_invalid' => '兩次輸入密碼不一致.',
	'admin_email_invalid' => 'Email 地址無效',
	'admin_invalid' => '您的信息沒有填寫完整.',
	'fail_reason' => '失敗. 原因:',
	'go_back' => '返回上一頁修改',
	'init_file' => '初始化運行目錄與文件',

	'config_nonexistence' => '您的 config.inc.php 不存在, 無法繼續安裝, 請用 FTP 將該文件上傳後再試.',
	'config_comment' => '請在下面填寫您的數據庫賬號信息, 通常情況下不需要修改紅色選項內容.',
	'config_unwriteable' => '安裝嚮導無法寫入配置文件, 請核對現有信息, 如需修改, 請通過 FTP 將改好的 config.inc.php 上傳.',

	'php_version_406' => '您的 PHP 版本小於 4.0.6, 無法使用 Discuz!。',
	'attach_enabled' => '允許/最大尺寸 ',
	'attach_enabled_info' => '您可以上傳附件的最大尺寸: ',
	'attach_disabled' => '不允許上傳附件',
	'attach_disabled_info' => '附件上傳或相關操作被服務器禁止。',
	'mysql_version_323' => '您的 MySQL 版本低於 3.23，安裝無法繼續進行。',
	'unwriteable_template' => '模板目錄(./templates)屬性非 777 或無法寫入，在線編輯模板功能將無法使用。',
	'unwriteable_attach' => '附件目錄(默認是 ./attachments)屬性非 777 或無法寫入，附件上傳功能將無法使用。',
	'unwriteable_avatar' => '自定義頭像目錄(./customavatars)屬性非 777 或無法寫入，上傳頭像功能將無法使用。',
	'unwriteable_forumdata' => '數據目錄(./forumdata)屬性非 777 或無法寫入，論壇運行記錄和備份到數據庫功能將無法使用。',
	'unwriteable_forumdata_template' => '編譯模板目錄(./forumdata/templates)屬性非 777 或無法寫入，安裝無法繼續進行。',
	'unwriteable_forumdata_cache' => '數據緩存目錄(./forumdata/cache)屬性非 777 或無法寫入，安裝無法繼續進行。',
	'tablepre_invalid' => '您指定的數據表前綴包含點字符(".")，請返回修改。',
	'db_invalid' => '指定的數據庫不存在, 系統也無法自動建立, 無法安裝 Discuz!.',
	'db_auto_created' => '指定的數據庫不存在, 但系統已成功建立, 可以繼續安裝.',
	'db_not_null' => '數據庫中已經安裝過 Discuz!, 繼續安裝會清空原有數據.',
	'db_drop_table_confirm' => '繼續安裝會清空全部原有數據，您確定要繼續嗎?',

	'install_abort' => '由於您目錄屬性或服務器配置原因, 無法繼續安裝 Discuz!, 請仔細閱讀安裝說明.',
	'install_process' => '您的服務器可以安裝和使用 Discuz!, 請進入下一步安裝.',
	'install_succeed' => '恭喜您，Discuz! 安裝成功！',
	'goto_forum' => '點擊這裡進入論壇',

	'init_credits_karma' => '威望',
	'init_credits_money' => '金錢',

	'init_group_0' => '會員',
	'init_group_1' => '管理員',
	'init_group_2' => '超級版主',
	'init_group_3' => '版主',
	'init_group_4' => '禁止發言',
	'init_group_5' => '禁止訪問',
	'init_group_6' => '禁止 IP',
	'init_group_7' => '遊客',
	'init_group_8' => '等待驗證會員',
	'init_group_9' => '乞丐',
	'init_group_10' => '新手上路',
	'init_group_11' => '註冊會員',
	'init_group_12' => '中級會員',
	'init_group_13' => '高級會員',
	'init_group_14' => '金牌會員',
	'init_group_15' => '論壇元老',

	'init_rank_1' => '新生入學',
	'init_rank_2' => '小試牛刀',
	'init_rank_3' => '實習記者',
	'init_rank_4' => '自由撰稿人',
	'init_rank_5' => '特聘作家',

	'init_cron_1' => '清空今日發帖數',
	'init_cron_2' => '清空本月在線時間',
	'init_cron_3' => '每日數據清理家',
	'init_cron_4' => '生日統計與郵件祝福',
	'init_cron_5' => '主題回復通知',
	'init_cron_6' => '每日公告清理',
	'init_cron_7' => '限時操作清理',
	'init_cron_8' => '論壇推廣清理',
	'init_default_style' => '默認風格',
	'init_default_forum' => '默認論壇',
	'init_default_template' => '默認模板套系',
	'init_default_template_copyright' => '北京康盛世紀科技有限公司',

	'init_dataformat' => 'Y-n-j',
	'init_modreasons' => '廣告/SPAM\r\n惡意灌水\r\n違規內容\r\n文不對題\r\n重複發帖\r\n\r\n我很贊同\r\n精品文章\r\n原創內容',
	'init_link' => 'Discuz! 官方論壇',
	'init_link_note' => '提供最新 Discuz! 產品新聞、軟件下載與技術交流',

	'license' => '<p>版權所有 (c) 2001-2004，北京康盛世紀科技有限公司<br>
保留所有權利。

<p>感謝您選擇 Discuz! 論壇產品。希望我們的努力能為您提供一個高效快速和強大的 web 論壇解決方案。

<p>Discuz! 英文全稱為 Crossday Discuz! Board，中文全稱為 Discuz! 論壇，以下簡稱 Discuz!。

<p>北京康盛世紀科技有限公司（Comsenz Inc.）為 Discuz! 產品的開發商，依法獨立擁有 Discuz! 產品著作權（中國國家版權局
著作權登記號 2003SR6623）。北京康盛世紀科技有限公司網址為 http://www.comsenz.com，Discuz! 官方網站網址為 http://www.discuz.com，
Discuz! 官方討論區網址為 http://www.discuz.net。

<p>本授權協議適用且僅適用於 Discuz! 4.x.x 版本，北京康盛世紀科技有限公司擁有對本授權協議的最終解釋權。

<p>在開始安裝 Discuz! 之前，請務必仔細閱讀本授權文檔，在確定同意並滿足授權協議的全部條款後，即可繼續 Discuz! 論壇的安裝。

<p>Discuz! 著作權已在中華人民共和國國家版權局註冊，著作權受到法律和國際公約保護。4.x.x 版本為商業軟件，使用者：無論個人或組織、
盈利與否、用途如何（包括以學習和研究為目的）：查看、安裝或使用 Discuz! 的整體或部分，都必須支付商業授權費用，獲得正式授權後，
方可成為授權用戶。

<ul type="I">
<p><li><b>協議許可的權利</b>
<ul type="1">
<li>在授權期限內擁有至多二個 Discuz! 的授權拷貝安裝，前提是拷貝必須在同一 IP 的服務器上，或在同一主域名的兩個主機下（如
domain1.your.com，domain2.your.com)。
<li>依據所購買的授權類型中確定的免費升級期限、技術支持期限、技術支持方式和技術支持內容，自購買時刻起，用戶可在免費升級期限內
獲得並安裝使用最新的 Discuz! 論壇軟件包；在技術支持期限內通過指定的方式獲得指定範圍內的技術支持內容。授權用戶享有反映和提出
意見的權力，相關意見將被作為首要考慮，但沒有一定被採納的承諾或保證。
<li>您可以在協議規定的約束和限制範圍內修改 Discuz! 源代碼(如果被提供的話)或界面以適應您的網站要求。
<li>授權用戶擁有其論壇全部會員資料、文章及相關信息的所有權，並獨立承擔與文章內容的相關法律義務。
</ul>

<p><li><b>協議規定的約束和限制</b>
<ul type="1">
<li>不得將一份商業授權安裝於不同 IP 的並且不在同一主域名下的服務器空間上，也不得在同一 IP 或同一域名下將一份授權安裝為兩個以上
的拷貝使用。
<li>禁止以任何目的，包括以學習或研究為目的通過 Internet 或其他媒介將所獲授權的產品提供給第三人或公眾。
<li>禁止任何形式的重新分發，更不得利用非法重新分發獲利。
<li>不得對本軟件進行出租、租借、發放子許可證、出售或抵押。
<li>無論如何，即無論用途如何、是否經過修改或美化、修改程度如何，只要使用 Discuz! 的整體或任何部分，未經書面許可，論壇頁面頁腳處
的 Discuz! 名稱和北京康盛世紀科技有限公司下屬網站（http://www.discuz.com 或 http://www.discuz.net） 的鏈接都必須保留，而不能清除
或修改。
<li>如果您未能遵守本協議的條款，您的授權將被終止，所被許可的權利將被收回。
</ul>

<p><li><b>有限擔保和免責聲明</b>
<ul type="1">
<li>Discuz! 及所附帶的文件是作為不提供任何明確的或隱含的賠償或擔保的形式出售的。
<li>北京康盛世紀科技有限公司不對使用 Discuz! 構建的論壇中的文章或信息承擔責任。
<li>您必須瞭解使用本軟件的風險。十分必要時，北京康盛世紀科技有限公司所承擔的責任僅限於產品版本更換，或在第一次成功安裝 Discuz!
前退還購買費用。
</ul>
</ul>

<p>有關 Discuz! 授權包含的服務範圍，付費方式等，Discuz! 官方網站提供惟一的解釋和官方價目表。北京康盛世紀科技有限公司擁有在不事先
通知的情況下，修改授權協議和價目表的權力，修改後的協議或價目表對自改變之日起的新授權用戶生效。

<p>電子文本形式的授權協議如同雙方書面簽署的協議一樣，具有完全的和等同的法律效力。您一旦開始安裝 Discuz!，即被視為完全理解並接受
本協議的各項條款，在享有上述條款授予的權力的同時，受到相關的約束和限制。協議許可範圍以外的行為，將直接違反本授權協議並構成侵權，
我們有權隨時終止授權，責令停止損害，並保留追究相關責任的權力。',
	'preparation' => '<li>將壓縮包中 Discuz! 目錄下全部文件和目錄上傳到服務器.</li><li>修改服務器上的 config.inc.php 文件以適合您的配置, 有關數據庫賬號信息請咨詢您的空間服務提供商.</li><li>如果您使用非 WINNT 系統請修改以下屬性:<br>&nbsp; &nbsp; <b>./templates</b> 目錄 777;&nbsp; &nbsp; <b>./attachments</b> 目錄 777;&nbsp; &nbsp; <b>./customavatars</b> 目錄 777;&nbsp; &nbsp; <b>./forumdata</b> 目錄 777;<br><b>&nbsp; &nbsp; ./forumdata/cache</b> 目錄 777;&nbsp; &nbsp; <b>./forumdata/templates</b> 目錄 777;&nbsp; &nbsp; <br></li><li>確認 URL 中 /attachments 可以訪問服務器目錄 ./attachments 內容.</li>',

);

?>