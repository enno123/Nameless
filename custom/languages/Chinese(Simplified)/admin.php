<?php
/*
 *  Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr12
 *
 *  License: MIT
 *
 *  Chinese Simplified Language - Admin
 *  Translation(Chinese Simplified) by ahdg,lian20,LingDong,NEWLY_1129514,Dreta
 *  Translation progress(v2-pr9) : 100%
 */

$language = [
    /*
     *  Admin Control Panel
     */
    // Login
    're-authenticate' => '请重新验证您的账户所有权',

    // Sidebar
    'dashboard' => '主控板',
    'configuration' => '配置',
    'layout' => '排版',
    'user_management' => '用户管理',
    'admin_cp' => '管理员控制台',
    'overview' => '总览',
    'core' => '核心',
    'integrations' => '集成',
    'minecraft' => 'Minecraft',
    'modules' => '模块',
    'security' => '安全',
    'styles' => '风格',
    'users_and_groups' => '账号与权限组',

    // Overview
    'running_nameless_version' => '运行中 NamelessMC 的版本为 <strong>{x}</strong>', // Don't replace "{x}"
    'running_php_version' => '运行中 php 的版本为 <strong>{x}</strong>', // Don't replace "{x}"
    'statistics' => '统计',
    'registrations' => '注册量',
    'topics' => '话题数',
    'posts' => '帖子数',
    'notices' => '消息',
    'no_notices' => '无消息.',
    'email_errors_logged' => '邮件错误已被记录',
    'upgrade_php_version' => '请将 PHP 更新到 7.4 或以上版本 - NamelessMC 的下一个版本将不再支持您使用的 PHP 版本。',

    // Core
    'mode_toggle' => '深色模式',
    'settings' => '设置',
    'general_settings' => '通用设置',
    'sitename' => '网站名称',
    'punished_id' => 'Punished User ID',
    'punisher_id' => 'Punisher User ID',
    'reason' => 'Ban Reason',
    'ip_ban' => 'IP ban?',
    'default_language' => '默认语言',
    'default_language_help' => '用户可从已安装的语言中选择。',
    'install_language' => '安装语言',
    'update_user_languages' => '更新用户语言配置',
    'update_user_languages_warning' => '这将会更新所有用户的语言设置，即便他们已经设置过了相关配置!',
    'updated_user_languages' => '用户语言配置已被更新。',
    'installed_languages' => '任何新语言包皆已安装成功。',
    'default_timezone' => '默认时区',
    'registration' => '注册',
    'enable_registration' => '是否启用注册?',
    'verify_with_mcassoc' => '是否通过 MCAssoc 来验证用户?',
    'email_verification' => '是否启用邮箱验证?',
    'registration_settings_updated' => '注册配置已更新成功。',
    'homepage_type' => '主页样式',
    'portal' => '传送门',
    'private_profiles' => '个人资料',
    'missing_sitename' => '请输入长度介于 2 到 64 个字符之间的网站名称。',
    'missing_contact_address' => '请输入一个介于 3 到 255 个字符之间电子邮件地址。',
    'use_friendly_urls' => '友好 URLs',
    'use_friendly_urls_help' => '提醒:你的运行环境必须被配置为 mod_rewrite 和 .htaccess 文件可写并可使用以使这项功能发挥作用',
    'config_not_writable' => '你的 <strong>core/config.php</strong> 文件并不可写。请检查文件权限。',
    'settings_updated_successfully' => '通用设置已更新成功。',
    'social_media' => '社交媒体',
    'youtube_url' => 'YouTube 链接 (URL)',
    'twitter_url' => 'Twitter 链接 (URL)',
    'twitter_dark_theme' => '使用 Twitter 暗色 主题?',
    'discord_id' => 'Discord 服务器 ID',
    'discord_widget_theme' => 'Discord Widget 主题',
    'discord_id_length' => '请确保您的 Discord ID 长 18 位。',
    'discord_id_numeric' => '请确保您的 Discord ID 只包含数字。',
    'discord_invite_info' => '您可点击 <a target="_blank" href="https://namelessmc.com/discord-bot-invite">此处</a> 来将 Nameless Link 机器人添加到您的 Discord 服务器中。添加完毕后，请运行指令 <code>/apiurl</code> 来将网站和机器人链接起来。 您也可以 <a target="_blank" href="https://github.com/NamelessMC/Nameless-Link/wiki/Own-instance">自建机器人</a>。',
    'discord_bot_must_be_setup' => '您必须配置 Discord 机器人后才能启用 Discord 集成。您可<a href="https://github.com/NamelessMC/Nameless-Link/wiki/Setup" target="_blank">点击此处</a>了解详情。',
    'discord_bot_setup' => '机器人已配置?',
    'discord_integration_not_setup' => 'Discord 集成尚未配置',
    'dark' => '暗调',
    'light' => '亮色',
    'google_plus_url' => 'Google Plus 链接 (URL)',
    'facebook_url' => 'Facebook 链接 (URL)',
    'social_media_settings_updated' => '社交媒体配置已更新成功。',
    'successfully_updated' => '更新成功',
    'debugging_and_maintenance' => '调试 & 维护模式',
    'maintenance' => '维护模式',
    'debugging_settings_updated_successfully' => '调试设置已更新成功。',
    'enable_debug_mode' => '是否启用调试模式?',
    'force_https' => '是否强制使用 https?',
    'force_https_help' => '如果启用，对您网站的所有请求都将重定向到 https。您必须具有有效的有效 SSL 证书，此功能才能正常工作。',
    'force_www' => '是否强制使用 www?',
    'contact_email_address' => '电子邮件联络地址',
    'emails' => '邮件',
    'email_errors' => '邮件配置时出现了错误',
    'registration_email' => '注册邮件',
    'contact_email' => '联络邮件',
    'forgot_password_email' => '密码找回邮件',
    'unknown' => '未知',
    'delete_email_error' => '删除时遇到了错误',
    'confirm_email_error_deletion' => '您确定要删除此错误吗?',
    'viewing_email_error' => '预览出错',
    'unable_to_write_email_config' => '无法去写入 <strong>core/email.php</strong>。请检查文件权限。',
    'enable_mailer' => '是否启用 PHPMailer?',
    'enable_mailer_help' => '如果默认情况下无法发送电子邮件，请启用此功能。使用 PHPMailer 要求您具有能够发送电子邮件的服务，例如 Gmail 或 SMTP 提供程序。',
    'outgoing_email' => '发件箱地址',
    'outgoing_email_info' => '这是 NamelessMC 用来发送电子邮件的电子邮件地址。',
    'mailer_settings_info' => '如果启用了 PHPMailer，则必须填写以下字段。 更多的填写信息请点击 <a href="https://github.com/NamelessMC/Nameless/wiki/Setting-up-SMTP-with-Nameless-(e.g.-Gmail-or-Outlook)" target="_blank">NamelessMC Wiki相关页面</a>.',
    'host' => '主机 IP',
    'email_port' => '端口',
    'email_port_invalid' => '请输入一个有效的邮箱端口。',
    'email_password_hidden' => '出于安全原因，未显示密码。',
    'send_test_email' => '发送测试邮件',
    'send_test_email_info' => '以下按钮将尝试向您的电子邮件地址 <strong>{x}</strong> 发送电子邮件. 发送电子邮件时抛出的任何错误都将显示。', // Don't replace {x}
    'send' => '发送',
    'test_email_error' => '测试邮件错误:',
    'test_email_success' => '测试邮件发送成功!',
    'edit_email_messages' => '邮件信息',
    'email_language_info' => '找不到您的语言? 请确保 \'emails.php\' 在你您语言目录中，并且可被服务器写入。',
    'editing_language' => '编辑语言',
    'email_preview_popup' => '预览',
    'email_preview_popup_message' => '点击此处来查看邮件预览',
    'email_message_greeting' => '您好,',
    'email_message_thanks' => '致以敬意,',
    'email_message_options' => '选项',
    'email_message_subject' => '标题',
    'email_message_message' => '消息',
    'terms_error' => '请输入不超过 100,000 个字符的服务条款。',
    'privacy_policy_error' => '请输入不超过 100,000 个字符的隐私政策。',
    'terms_updated' => '隐私政策和服务条款 & 状态更新成功。',
    'avatars' => '头像',
    'allow_custom_avatars' => '是否允许用户自定义头像?',
    'default_avatar' => '默认头像',
    'custom_avatar' => '自定义头像',
    'minecraft_avatar' => 'Minecraft 头像',
    'minecraft_avatar_source' => 'Minecraft 头像源',
    'built_in_avatars' => '内置头像服务',
    'minecraft_avatar_perspective' => 'Minecraft 头像透视',
    'face' => '脸',
    'head' => '头',
    'bust' => '肢体',
    'select_default_avatar' => '选择一个新的默认头像:',
    'no_avatars_available' => '没有可用的头像。请先在上方上传新图像。',
    'avatar_settings_updated_successfully' => '头像配置已更新成功。',
    'navigation' => '导引栏',
    'navbar_order' => '导引栏顺序',
    'navbar_order_instructions' => '您可以为每个项目赋予大于0的数字以在导航栏中创建项目，第一个项目为1，第二个项目为更高的数字',
    'navbar_icon' => '导引栏图标',
    'navbar_icon_instructions' => '您还可以在此处向每个导引栏项添加图标，例如使用 <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank" rel="noopener nofollow">Font Awesome</a>, <a href="https://fomantic-ui.com/elements/icon.html" target="_blank" rel="noopener nofollow">Fomantic UI</a>.',
    'navigation_settings_updated_successfully' => '导引配置已更新成功。',
    'dropdown_items' => '下拉项目',
    'enable_page_load_timer' => '是否启用页面加载计时器?',
    'captcha_general' => '在注册和联系页上启用验证码? (大陆可能无法使用)',
    'captcha_login' => '在登录页上启用验证码?',
    'captcha_type' => '验证码类型',
    'captcha_site_key' => '验证码 Site Key',
    'captcha_secret_key' => '验证码 Secret Key',
    'invalid_recaptcha_settings' => 'Invalid {x} credentials. Ensure the site key and site secret are correct.', // Don't replace {x}
    'registration_disabled_message' => '禁用注册消息',
    'enable_nicknames_on_registration' => '是否启用昵称用来注册账户?',
    'validation_promote_group' => '邮箱验证后权限组',
    'validation_promote_group_info' => '这是用户验证帐户后将被提升到的组。',
    'login_method' => '登录方式',
    'privacy_and_terms' => '隐私 & 条款',
    'dropdown_name' => '下拉菜单的名字',
    'editing_messages' => '编辑信息中',
    'emails_mass_message' => '邮件广播',
    'sending_mass_message' => '发送广播中',
    'emails_mass_message_sent_successfully' => '邮件广播发送成功。',
    'emails_mass_message_replacements' => '您可在信息中使用占位符。可用占位符: {username}（用户名）, {sitename}（网站名）',
    'emails_mass_message_loading' => '加载中... 请勿刷新此页面。这可能需要几分钟。',
    'administrator' => '管理员',
    'administrator_permission_info' => '拥有管理员权限的用户将会拥有所有权限。您只应授予受信任的用户此权限。',

    // Placeholders
    'placeholders' => '占位符',
    'enable_placeholders' => '启用占位符?',
    'updated_placeholder_settings' => '占位符设置更新成功。',
    'placeholders_info' => '通过占位符功能，NamelessMC Spigot 插件可以向您的网站上显示关于玩家的信息，这样，玩家就可以把这些信息添加到自己的个人资料上。',
    'placeholders_none' => '没有任何占位符。',
    'placeholders_server_id' => '服务器 ID',
    'placeholders_name' => '名字',
    'placeholders_value' => '值',
    'placeholders_last_updated' => '上次更新',
    'placeholders_friendly_name' => '别名',
    'placeholders_friendly_name_info' => '您可设置占位符的一个别名。显示此占位符时，别名会取代默认名字。',
    'placeholders_show_on_profile' => '在个人资料上显示',
    'placeholders_show_on_profile_info' => '是否在每个用户的个人资料上显示此占位符。',
    'placeholders_show_on_forum' => '在论坛上显示',
    'placeholders_show_on_forum_info' => '是否在每个用户的帖子上显示此占位符。',

    // Placeholder leaderboards
    'leaderboard_settings' => '排行榜设置',
    'placeholder_leaderboard_settings' => '占位符排行榜设置',
    'placeholder_leaderboard_info' => '通过占位符排行榜，您可以根据一个占位符来把玩家列在排行榜里',
    'placeholder_leaderboard_enable_info' => '排行班在占位符是数字的情况下 (例如硬币，击杀数，等等) 功能最强。文字占位符排行榜的顺序无法控制。',
    'placeholder_leaderboard_updated' => '排行榜设置更新成功',
    'placeholder_leaderboard_enabled' => '排行榜启用',
    'placeholder_leaderboard_title' => '排行榜标题',
    'placeholder_leaderboard_sort' => '排行榜排序',

    // SEO
    'seo' => 'SEO',
    'google_analytics' => 'Google Analytics (分析)',
    'google_analytics_help' => '您可在网站上添加 Google Analytics (分析) 以了解各项统计数据。您必须先创建一个 Google Analytics (分析) 帐号。请输入您的 Google Analytics Web Property ID。 此 ID 格式为 UA-XXXXA-X，您可在您的帐号信息中找到此 ID。',
    'sitemap' => '站点地图',
    'seo_settings_updated_successfully' => 'SEO settings updated successfully.',

    // Reactions
    'icon' => '图标',
    'type' => '种类',
    'positive' => '赞',
    'neutral' => '中立',
    'negative' => '踩',
    'editing_reaction' => '编辑 表态符号 中',
    'html' => 'HTML',
    'new_reaction' => '<i class="fa fa-plus-circle"></i> 新的 表态',
    'creating_reaction' => '正在创建 表态符号',
    'no_reactions' => '这里还没有 表态符号 噢。',
    'reaction_created_successfully' => '表态符号 创建成功。',
    'reaction_edited_successfully' => '表态符号 编辑成功。',
    'reaction_deleted_successfully' => '表态符号 删除成功。',
    'name_required' => '必要项: 名称(name)',
    'html_required' => '必要项: HTML',
    'type_required' => '必要项: 种类(type)',
    'name_maximum_16' => '名称不得超过 16 个字符',
    'html_maximum_255' => 'HTML 不得超过 255 个字符',
    'confirm_delete_reaction' => '你确定要删除这个表态?',

    // Custom profile fields
    'custom_fields' => '自定义个人资料字段',
    'new_field' => '<i class="fa fa-plus-circle"></i> 新的字段',
    'required' => '必填的',
    'editable' => '可编辑的',
    'public' => '公开的',
    'forum_posts' => '显示在论坛上',
    'text' => '单行文本',
    'textarea' => '文本框',
    'date' => '日期',
    'creating_profile_field' => '创建个人资料字段中',
    'editing_profile_field' => '编辑个人资料字段中',
    'field_name' => '资料字段名称',
    'profile_field_required_help' => '用户必须填写必填字段，它们将在注册期间显示。',
    'profile_field_public_help' => '公共字段将显示给所有用户，如果禁用了此属性，则只有主人可以查看该值。',
    'profile_field_error' => '请输入的空间名 (长 2 - 16 个字符)',
    'description' => '描述',
    'display_field_on_forum' => '是否在论坛允许他人访问您的空间?',
    'profile_field_forum_help' => '如果启用，则该字段将显示在论坛帖子旁边的用户栏那。',
    'profile_field_editable_help' => '如果启用，用户可在他们的配置设置中修改该空间字段。',
    'no_custom_fields' => '此处尚无自定义字段。',
    'profile_field_updated_successfully' => '配置的字段更新成功。',
    'profile_field_created_successfully' => '空间字段成功创建。',
    'profile_field_deleted_successfully' => '空间字段成功删除。',

    // Minecraft
    'enable_minecraft_integration' => '是否启用 Minecraft 集成?',
    'mc_service_status' => 'Minecraft 服务状态',
    'service_query_error' => '无法检索服务状态。',
    'authme_integration' => 'AuthMe 集成',
    'authme_integration_info' => '当 AuthMe 集成被启用时, 用户只能在游戏内注册。',
    'enable_authme' => '是否启用 AuthMe 集成?',
    'authme_db_address' => 'AuthMe 数据库地址',
    'authme_db_port' => 'AuthMe 数据库端口',
    'authme_db_name' => 'AuthMe 数据库名称',
    'authme_db_user' => 'AuthMe 数据库用户名',
    'authme_db_password' => 'AuthMe 数据库密码',
    'authme_db_password_hidden' => '出于安全原因，AuthMe 数据库密码被隐藏。',
    'authme_hash_algorithm' => 'AuthMe 哈希算法',
    'authme_db_table' => 'AuthMe 用户表',
    'enter_authme_db_details' => '请输入有效的数据库详细信息。',
    'authme_password_sync' => '是否同步 AuthMe 密码?',
    'authme_password_sync_help' => '如果启用，则每当在游戏中更新用户密码时，密码也会在网站上更新。',
    'minecraft_servers' => 'Minecraft 服务器',
    'account_verification' => 'Minecraft 账户验证',
    'server_banners' => '服务器条幅',
    'query_errors' => '查询错误',
    'add_server' => '<i class="fa fa-plus-circle"></i> 添加服务器',
    'no_servers_defined' => '尚未定义服务器',
    'query_settings' => '查询设置',
    'default_server' => '默认服务器',
    'no_default_server' => '无默认服务器',
    'external_query' => '是否使用外部查询?',
    'external_query_help' => '如果默认服务器查询不起作用，请启用此选项。',
    'adding_server' => '添加服务器中',
    'server_name' => '服务器名称',
    'server_address' => '服务器地址',
    'server_address_help' => '这是用于连接服务器的 IP 地址或域名，不允许包含端口。',
    'server_port' => '服务器端口',
    'parent_server' => '父服务器',
    'parent_server_help' => '父服务器通常是服务器连接到的 BungeeCord 实例（如果有）。',
    'no_parent_server' => '无父服务器',
    'bungee_instance' => '是否启用 BungeeCord 实例?',
    'bungee_instance_help' => '如果服务器是 BungeeCord 代理服，请开启这个配置',
    'bedrock' => 'Bedrock?',
    'bedrock_help' => 'Select this option if the server is a Bedrock server.',
    'server_query_information' => '为了显示您网站上的在线玩家列表，您的服务器 <strong>必须</strong> 开启 \'enable-query\'   配置，该配置在服务器\'s <strong>server.properties</strong> 文件',
    'enable_status_query' => '是否启用联机状态查询?',
    'status_query_help' => '如果启用此功能，状态页将显示该服务器处于联机还是脱机状态。',
    'show_ip_on_status_page' => '是否在状态页显示 IP?',
    'show_ip_on_status_page_info' => '如果启用此功能，则用户在查看“状态”页面时将能够查看和复制IP地址。',
    'enable_player_list' => '是否启用玩家列表?',
    'pre_1.7' => 'Minecraft 版本是否比 1.7 更老?',
    'player_list_help' => '如果启用此功能，状态页将显示在线玩家列表。',
    'server_query_port' => '服务器查询端口',
    'server_query_port_help' => '这是服务器的 server.properties 文件中的 query.port 选项，前提是同一文件中的 enable-query 选项设置为 true。',
    'server_name_required' => '请输入服务器名称',
    'server_name_minimum' => '请确保您的服务器名称至少为 1 个字符',
    'server_name_maximum' => '请确保您的服务器名称最多 20 个字符',
    'server_address_required' => '请输入服务器地址',
    'server_address_minimum' => '请确保您的服务器地址至少为 1 个字符',
    'server_address_maximum' => '请确保您的服务器地址最多 64 个字符',
    'server_port_required' => '请输入服务器端口',
    'server_port_minimum' => '请确保您的服务器端口至少为 2 个字符',
    'server_port_maximum' => '请确保您的服务器端口最多为 5 个字符',
    'server_parent_required' => '请选择一个父服务器',
    'query_port_maximum' => '请确保您的查询端口最多 5 个字符',
    'server_created' => '服务器创建成功。',
    'confirm_delete_server' => '你确定你想要删除这个服务器?',
    'server_updated' => '服务器已更新成功。',
    'editing_server' => '编辑服务器中',
    'server_deleted' => '服务器被成功删除',
    'unable_to_delete_server' => '无法去删除服务器',
    'leave_port_empty_for_srv' => '如果端口为 25565，或者您的域名使用 SRV 记录，则可以将端口留空',
    'viewing_query_error' => '预览查询时出错',
    'confirm_query_error_deletion' => '你确定想要删除所有查询错误日志?',
    'no_query_errors' => '无查询错误日志被记录',
    'new_banner' => '<i class="fa fa-plus-circle"></i> 新的条幅',
    'purge_errors' => '清除错误日志',
    'confirm_purge_errors' => '您确定要清除所有错误日志吗？',
    'email_errors_purged_successfully' => '电子邮件错误已成功清除。',
    'error_deleted_successfully' => '错误日志已成功删除。',
    'no_email_errors' => '无电子邮件错误日志被记录',
    'email_settings_updated_successfully' => '邮件配置已被更新成功',
    'content' => '联络',
    'mcassoc_help' => 'MCAssoc 是一项外部服务，可用于验证用户拥有其注册的 Minecraft 帐户。要使用此功能，您需要注册共享密钥 <a href="https://mcassoc.lukegb.com/" target="_blank">点击这里</a>.',
    'mcassoc_key' => 'MCAssoc 共享密匙',
    'mcassoc_instance' => 'MCAssoc 实例密钥',
    'mcassoc_instance_help' => '<a href="#" onclick="generateInstance();">点击以生成实例密钥</a>',
    'mcassoc_error' => '请确保您正确输入了共享密钥，并且正确生成了实例密钥。',
    'updated_mcassoc_successfully' => 'MCAssoc 配置更新成功。',
    'force_premium_accounts' => '是否强制使用 Minecraft 高级帐户?',
    'banner_background' => '条幅背景',
    'query_interval' => '查询间隔 (以分钟为单位，必须在 5 到 60 之间)',
    'player_graphs' => '玩家图表',
    'player_count_cronjob_info' => '你可以设置一个定时工作以 {x} 分钟为频率来查询服务器,通过以下命令:',
    'status_page' => '是否启用状态页面?',
    'minecraft_settings_updated_successfully' => '配置已更新成功。',
    'server_id_x' => '服务器 ID: {x}', // Don't replace {x}
    'server_information' => '服务器信息',
    'query_information' => '查询信息',
    'query_errors_purged_successfully' => '查询错误清除成功。',
    'query_error_deleted_successfully' => '查询错误日志删除成功。',
    'banner_updated_successfully' => '条幅已成功更新。您的更改可能需要一段时间才能生效。',

    // Modules
    'modules_installed_successfully' => '任何新模块皆已安装成功。',
    'enabled' => '已启用',
    'disabled' => '已禁用',
    'enable' => '启用',
    'disable' => '禁用',
    'module_enabled' => '模块已启用。',
    'module_disabled' => '模块已禁用。',
    'author' => '作者:',
    'author_x' => '作者: {x}', // Don't replace {x}
    'updated_x' => 'Updated at: {x}', // Don't replace {x}
    'module_outdated' => '我们检测到该模块适用于 Nameless 版本 {x}, 但你正运行 Nameless 版本 {y}', // Don't replace "{x}" or "{y}"
    'find_modules' => '寻找模块',
    'view_all_modules' => '查看所有模块',
    'unable_to_retrieve_modules' => '无法检索模块',
    'module' => '模块',
    'unable_to_enable_module' => '无法启用不兼容的模块。',
    'unable_to_enable_module_dependencies' => '因为此模块依赖于被禁用的模块 {x}, 所以无法启用。', // Don't replace {x}
    'unable_to_disable_module' => '无法禁用此模块 - 模块 {x} 依赖于此模块。', // Don't replace {x}

    // Styles
    'templates' => '模板',
    'panel_templates' => '面板模板',
    'view_all_panel_templates' => '查看所有面板模版',
    'template_outdated' => '我们检测到您的模板适用于 Nameless 版本 {x}, 但你正在运行 Nameless 版本 {y}', // Don't replace "{x}" or "{y}"
    'template_not_supported' => '从 NamelessMC 2.0.0-pr8 开始，默认模板不再被支持。为了获得更好的体验，请您使用受支持的模板。',
    'active' => '应用中的',
    'deactivate' => '停用',
    'activate' => '启用',
    'warning_editing_default_template' => '警告！建议您不要编辑默认模板。',
    'images' => '图库',
    'upload_new_image' => '上传新图片',
    'reset_background' => '重置背景',
    'install' => '<i class="fa fa-plus-circle"></i> 安装',
    'template_updated' => '模板更新成功。',
    'default' => '默认配置',
    'make_default' => '作为默认配置',
    'default_template_set' => '将默认模板设置为 {x} 的操作已成功', // Don't replace {x}
    'template_deactivated' => '模板已停用。',
    'template_activated' => '模板已启用。',
    'permissions' => '权限',
    'setting_perms_for_x' => '给 {x} 模板设置权限中', // Don't replace {x}
    'templates_installed_successfully' => '任何新模板皆已安装成功。',
    'confirm_delete_template' => '你确定要删除这个模板?',
    'delete' => '删除',
    'template_deleted_successfully' => '模板删除成功。',
    'background_image_x' => '背景图片: <strong>{x}</strong>', // Don't replace {x}
    'banner_image_x' => '条幅图片: <strong>{x}</strong>', // Don't replace {x}
    'logo_image_x' => 'Logo image: <strong>{x}</strong>', // Don't replace {x}
    'favicon_image_x' => 'Favicon image: <strong>{x}</strong>', // Don't replace {x}
    'x_directory_not_writable' => '已被选择的 <strong>{x}</strong> 目录不可写!', // Don't replace {x}
    'template_banner_reset_successfully' => '条幅重置成功。',
    'template_banner_updated_successfully' => '条幅更新成功。',
    'reset_banner' => '重置条幅',
    'logo_reset_successfully' => '图标重置成功。',
    'logo_updated_successfully' => '图标更新成功。',
    'reset_logo' => '重置图标',
    'favicon_reset_successfully' => 'Favicon 重置成功。',
    'favicon_updated_successfully' => 'Favicon 更新成功。',
    'reset_favicon' => '重置 Favicon',
    'find_templates' => '寻找模板',
    'view_all_templates' => '预览所有模板',
    'unable_to_retrieve_templates' => '无法检索模板',
    'template' => '模板',
    'stats' => '统计',
    'downloads_x' => '下载次数: {x}',
    'views_x' => '浏览量: {x}',
    'rating_x' => '评价: {x}',
    'editing_template_x' => '编辑模板 {x} 中', // Don't replace {x}
    'editing_template_file_in_template' => '编辑位于模板 {y} 中的文件 {x}  ', // Don't replace {x} or {y}
    'cant_write_to_template' => '无法写入模板文件！请检查文件权限。',
    'unable_to_delete_template' => '无法完全删除模板。请检查文件权限。',
    'background_reset_successfully' => '背景重置成功。',
    'background_updated_successfully' => '背景更新成功。',
    'unable_to_enable_template' => '无法启用不兼容的模板。',
    'background_image_info' => '注意：此选项与大部分模版都不兼容。',
    'dark_mode' => '深色模式',
    'navbar_colour' => '导航栏颜色',
    'clear_cache' => '清空模板缓存',
    'cache_cleared' => '模板缓存已清空',

    // Users & groups
    'users' => '账号',
    'groups' => '权限组',
    'group' => '权限组',
    'new_user' => '<i class="fa fa-plus-circle"></i> 新账号',
    'creating_new_user' => '创建新账号',
    'registered' => '注册过的',
    'user_created' => '账号创建成功。',
    'cant_delete_root_user' => '不能删除 root 账号!',
    'cant_modify_root_user' => '无法修改此用户的主权限组!',
    'main_group' => '主权限组',
    'user_deleted' => '账号删除成功。',
    'confirm_user_deletion' => '你确定要删除账号 <strong>{x}</strong>?', // Don't replace {x}
    'validate_user' => '验证账号',
    'update_uuid' => '更新 UUID',
    'update_mc_name' => '更新 Minecraft 账号名',
    'reset_password' => '重置密码',
    'punish_user' => '惩罚该账号',
    'delete_user' => '删除该账号',
    'minecraft_uuid' => 'Minecraft UUID',
    'other_actions' => '其他操作',
    'disable_avatar' => '停用头像',
    'select_user_group' => '你必须选择一个账号的权限组',
    'uuid_max_32' => 'UUID 最多32个字符。',
    'title_max_64' => '账号标题最多64个字符。',
    'group_id' => '权限组 ID',
    'name' => '名称',
    'title' => '账号标题',
    'new_group' => '<i class="fa fa-plus-circle"></i> 新权限组',
    'group_name_required' => '请输入一个权限组的名称。',
    'group_name_minimum' => '请确保您的权限组名称至少为 2 个字符。',
    'group_name_maximum' => '请确保您的权限组名称不超过 20 个字符。',
    'creating_group' => '正在创建新权限组',
    'group_html_maximum' => '请确保您的权限组 HTML 长度不超过 1024 个字符。',
    'group_html' => '权限组 HTML',
    'group_html_lg' => '权限组 HTML 大小',
    'group_username_colour' => '权限组用户名颜色',
    'group_username_css' => '权限组用户名 CSS',
    'group_staff' => '这个权限组是否为工作人员组?',
    'delete_group' => '删除权限组',
    'confirm_group_deletion' => '你确定要删除权限组 {x}?', // Don't replace {x}
    'group_not_exist' => '该权限组不存在',
    'secondary_groups' => '次级权限组',
    'secondary_groups_info' => '用户将从这些组中获得任何其他权限。Ctrl + 单击以选择/取消选择多个组。',
    'unable_to_update_uuid' => '无法更新 UUID。',
    'default_group' => '这个权限组是否为默认组 (对于新用户来说)?',
    'user_id' => '账号 ID',
    'uuid' => 'UUID',
    'group_order' => '组长',
    'group_created_successfully' => '权限组创建成功。',
    'group_updated_successfully' => '权限组更新成功。',
    'group_deleted_successfully' => '权限组删除成功。',
    'unable_to_delete_group' => '无法删除 默认组 或 可以查看管理员面板的组。请先更新组设置！',
    'can_view_staffcp' => '这个权限组是否能查看管理员面板?',
    'user' => '账号',
    'user_validated_successfully' => '账号验证成功。',
    'user_updated_successfully' => '账号更新成功。',
    'editing_user_x' => '编辑账号 {x} 中', // Don't replace {x}
    'details' => '详细',
    'force_tfa' => '是否强制为该组成员开启二步验证？',
    'force_tfa_warning' => '如果你不明白你在做什么，你可能会导致你自己和该组成员无法使用账户',
    'force_tfa_alert' => '你所在的组必须要开启二步验证',
    'resend_activation_email' => '重新发送激活邮件',
    'email_resent_successfully' => '邮件已重新发送。',
    'email_resend_failed' => '邮件发送失败，请检查邮件设置。',
    'no_item_selected' => 'No items selected',

    // Permissions
    'select_all' => '全选',
    'deselect_all' => '取消全选',
    'background_image' => '背景图片',
    'can_edit_own_group' => '可以编辑自己组的权限',
    'permissions_updated_successfully' => '权限更新成功。',
    'cant_edit_this_group' => '您无法编辑该组的权限!',

    // General Admin language
    'task_successful' => '任务运行成功。',
    'invalid_action' => '无效操作',
    'enable_night_mode' => '启用黑夜模式',
    'disable_night_mode' => '关闭黑夜模式',
    'view_site' => '查看站点',
    'signed_in_as_x' => '以 {x} 登录', // Don't replace {x}
    'warning' => '警告',

    // Maintenance
    'maintenance_mode' => '维护模式',
    'maintenance_enabled' => '维护模式当前正在启用',
    'enable_maintenance_mode' => '是否启用维护模式?',
    'maintenance_mode_message' => '维护模式信息',
    'maintenance_message_max_1024' => '请确保您的维护信息最多为 1024 个字符。',

    // Security
    'acp_logins' => '管理员面板登录',
    'please_select_logs' => '请选择你要查看的日志',
    'ip_address' => 'IP 地址',
    'template_changes' => '更改模板',
    'email_logs' => '邮件广播',
    'group_sync_logs' => '权限组同步变更',
    'file_changed' => '更改文件',
    'all_logs' => '所有日志',
    'action' => '操作',
    'action_info' => '操作信息',
    'groups_removed' => '删除的权限组',
    'groups_added' => '添加的权限组',

    // Updates
    'update' => '更新',
    'current_version_x' => '当前版本: <strong>{x}</strong>', // Don't replace {x}
    'new_version_x' => '新版本: <strong>{x}</strong>', // Don't replace {x}
    'new_update_available' => '有可用的新版本待更新',
    'new_urgent_update_available' => '这里有一个紧急更新，请尽快更新!',
    'up_to_date' => '你的 NamelessMC 已是最新版!',
    'urgent' => '这是一个紧急更新',
    'changelog' => '变更日志',
    'update_check_error' => '检查更新时遇到了错误:',
    'instructions' => '使用说明',
    'download' => '下载',
    'install_confirm' => '请确保你已下载更新包并上传了包含的文件!',
    'check_again' => '检查更新',

    // Widgets
    'widgets' => '小部件',
    'widget_enabled' => '小部件已启用',
    'widget_disabled' => '小部件已停用',
    'widget_updated' => '小部件已更新',
    'editing_widget_x' => '编辑小部件 {x} 中', // Don't replace {x}
    'module_x' => '模块: {x}', // Don't replace {x}
    'widget_order' => '小部件顺序',
    'widget_location' => '小部件位置',
    'left' => '左',
    'right' => '右',

    // Online users widget
    'include_staff_in_user_widget' => '是否在用户小部件中包含工作人员?',
    'show_nickname_instead_of_username' => '是否显示用户的昵称而不是用户名?',

    // Custom Pages
    'pages' => '页面',
    'custom_pages' => '自定义页面',
    'new_page' => '<i class="fa fa-plus-circle"></i> 新页面',
    'no_custom_pages' => '目前没有新页面被创建',
    'creating_new_page' => '创建新页面中',
    'page_title' => '页面标题',
    'page_path' => '页面路径 (前面带有/，例如/example)',
    'page_icon' => '页面图标',
    'page_link_location' => '页面本地链接',
    'page_link_navbar' => '导引栏',
    'page_link_footer' => '页尾',
    'page_link_more' => '“更多”下拉列表',
    'page_link_none' => '无链接',
    'page_content' => '页面内容',
    'page_redirect' => '页面重定向？',
    'page_redirect_to' => '重定向链接 (前面带有 http://)',
    'page_target' => '在新标签页中打开页面?',
    'unsafe_html' => '是否允许不安全的 HTML?',
    'unsafe_html_warning' => '启用此选项意味着可以在页面上使用任何 HTML，包括有潜在危险的 JavaScript。仅当您确定 HTML 安全时才启用此功能。',
    'include_in_sitemap' => '是否将其包含在站点地图里?',
    'sitemap_link' => '站点地图链接:',
    'basic_page' => '基本页面',
    'page_permissions' => '页面权限',
    'view_page' => '预览页面?',
    'editing_page_x' => '编辑页面 {x} 中', // Don't replace {x}
    'unable_to_create_page' => '无法创建新页面:',
    'page_title_required' => '你的页面需要一个标题。',
    'page_url_required' => '你的页面需要一个路径。',
    'link_location_required' => '你的页面需要一个本地链接',
    'page_title_minimum_2' => '页面标题必须至少 2 个字符。',
    'page_url_minimum_2' => '页面路径必须至少 2 个字符。',
    'page_title_maximum_255' => '页面标题不能超过 255 个字符。',
    'page_icon_maximum_64' => '页面图标最大不能超过 64 个字符。',
    'page_url_maximum_255' => '页面路径不能超过 255 个字符。',
    'page_content_maximum_100000' => '页面内容不能超过 100000 个字符。',
    'page_redirect_link_maximum_512' => '页面重定向链接最大不能超过 512 个字符。',
    'confirm_delete_page' => '你确定要删除这个页面?',
    'page_created_successfully' => '页面创建成功。',
    'page_updated_successfully' => '页面更新成功。',
    'page_deleted_successfully' => '页面删除成功。',

    // API
    'api' => 'API',
    'enable_api' => '是否启用 API?',
    'api_info' => '该 API 允许插件和其他服务与您的网站进行交互，例如 <a href="https://plugin.namelessmc.com" target="_blank" > Nameless 官方插件</a>.',
    'enable_legacy_api' => '是否启用旧版 API?',
    'legacy_api_info' => '旧版 API 允许使用旧的 Nameless v1 API 的插件与 v2 网站一起使用。',
    'confirm_api_regen' => '您确定要重新生成 API 密钥?',
    'api_key' => 'API 密钥',
    'api_url' => 'API 链接',
    'copy' => '复制',
    'api_key_regenerated' => 'API 密匙重新生成成功。',
    'api_registration_email' => 'API 注册邮件',
    'show_registration_link' => '显示注册链接',
    'registration_link' => '注册链接',
    'link_to_complete_registration' => '点击链接以完成注册: {x}', // Don't replace {x}
    'api_verification' => '是否启用 API 验证?',
    'api_verification_info' => '如果启用，则只能通过 API 来验证帐户，例如使用官方的 Nameless 插件在游戏中进行验证。 <strong>此选项将覆盖电子邮件验证，并且帐户将自动激活！</strong><br />您应该将默认组设置为具有受限权限，然后将管理员面板->设置->注册选项卡中的验证后组更新为具有普通权限的完整成员组。',
    'enable_username_sync' => '是否启用用户名同步?',
    'enable_username_sync_info' => '如果启用，网站用户名将被更新以匹配游戏中用户名。',
    'api_settings_updated_successfully' => 'API 设置更新成功。',
    'group_sync' => '权限组同步',
    'group_sync_info' => '您可以将 API 配置为在更改其游戏组时自动更新用户的网站组。只需在下面输入游戏中组名称和应该与之同步的网站组即可。',
    'ingame_group' => '游戏内组名',
    'website_group' => '网站内组名',
    'set_as_primary_group' => '是否设置为私有组?',
    'set_as_primary_group_info' => '如果启用，将更新用户的私有组为网站组。如果禁用，则游戏中组将添加到用户的网站上的次要组。',
    'ingame_group_maximum' => '请确保您的权限组名称最长为 64 个字符。',
    'select_website_group' => '请选择一个网站组。',
    'ingame_group_already_exists' => '已经为该游戏内组创建了权限同步规则。',
    'group_sync_rule_created_successfully' => '组同步规则已成功创建。',
    'group_sync_rules_updated_successfully' => '组同步规则已成功更新。',
    'group_sync_rule_deleted_successfully' => '组同步规则已成功删除。',
    'group_sync_plugin_not_set_up' => '插件尚未配置',
    'existing_rules' => '现有规则',
    'new_rule' => '新规则',
    'api_endpoints' => 'API 结点',
    'api_endpoints_info' => 'API 结点允许模块添加第三方程序 (例如 Minecraft 和 Discord) 与您的 NamelessMC 网站互动的方法。<a href="https://docs.namelessmc.com/en/api-documentation" target="_blank">Check out the API documentation here</a>',
    'route' => '路径',
    'method' => 'Method',
    'transformers' => 'Transformers',

    // File uploads
    'drag_files_here' => '将文件扔到这以上传',
    'invalid_file_type' => '无效的文件类型!',
    'file_too_big' => '你上传的文件太大了，你上传的文件大小为 {{filesize}} 但你最大能上传 {{maxFilesize}}', // Don't replace {{filesize}} or {{maxFilesize}}
    'allowed_proxies' => '允许的代理',
    'allowed_proxies_info' => '允许的代理 IP 的行分隔列表。',

    // Error logs
    'error_logs' => '错误日志',
    'notice_log' => '消息日志',
    'warning_log' => '警告日志',
    'custom_log' => '自定义日志',
    'other_log' => '其它日志',
    'fatal_log' => '致命错误日志',
    'log_file_not_found' => '日志文件未被找到。',
    'log_purged_successfully' => '日志已被清除成功。',
    'forum_topic_reply_email' => '论坛主题回复邮件',

    // Hooks
    'hooks' => 'Webhooks',
    'hooks_info' => 'Webhooks 允许第三方服务在出现特定事件时被提醒。',
    'no_hooks_yet' => '还没有任何 Webhook 呢!',
    'new_hook' => '创建新 Webhook',
    'creating_new_hook' => '正在创建新 Webhook',
    'editing_hook' => '编辑 Webhook',
    'delete_hook' => '您确定要删除此 Webhook 吗?',
    'hook_deleted' => 'Hook 删除成功。',
    'hook_name' => 'Webhook 名称',
    'hook_created' => 'Hook 已被成功创建。',
    'hook_edited' => 'Hook 已被成功编辑。',
    'hook_select_info' => '只有被选择的 \'New topic\' (新话题) 的 Hook 被当作事件来展示。',
    'hook_url' => 'Webhook 链接 (URL)',
    'hook_type' => 'Webhook 种类 (Type)',
    'hook_events' => '可触发该 Webhook 的事件',
    'invalid_hook_url' => '无效的 Webhook 链接 (URL)',
    'invalid_hook_name' => '无效的 Webhook 名称 (name)',
    'invalid_hook_events' => '您必须至少选择 1 个事件',
    'register_hook_info' => '用户注册',
    'validate_hook_info' => '用户验证',
    'delete_hook_info' => '用户删除',
    'report_hook_info' => '举报创建',
    'ban_hook_info' => 'User banned',
    'warning_hook_info' => 'User warned',

    // Sitemap
    'unable_to_load_sitemap_file_x' => '无法加载站点地图文件 {x}', // Don't replace {x}
    'sitemap_generated' => 'Sitemap 生成成功',
    'sitemap_not_writable' => '<strong>cache/sitemaps</strong> 目录不可写。',
    'cache_not_writable' => '<strong>cache</strong> 目录不可写。',
    'generate_sitemap' => '生成 Sitemap',
    'download_sitemap' => '下载 Sitemap',
    'sitemap_not_generated_yet' => '目前没有 Sitemap 生成!',
    'sitemap_last_generated_x' => '生成站点地图最近一次的时间  {x}', // Don't replace {x}

    // Page metadata
    'page_metadata' => '页面元数据',
    'metadata_page_x' => '查看页面 {x} 的元数据', // Don't replace {x}
    'keywords' => '关键词',
    'description_max_500' => '描述最多为500个字符。',
    'page' => '页面',
    'metadata_updated_successfully' => '元数据更新成功。',

    // Dashboard
    'total_users' => '总用户量',
    'recent_users' => '在线用户量',
    'recent_topics' => '话题量',
    'recent_posts' => '帖子数',
    'average_players' => '普通玩家',
    'nameless_news' => 'NamelessMC 新闻',
    'unable_to_retrieve_nameless_news' => '无法获取最新新闻',
    'confirm_leave_site' => '您即将离开我们的网站！您确定要访问 <strong id="leaveSiteURL">{x}</strong>?', // don't replace {x} and make sure it has the id leaveSiteURL
    'server_compatibility' => '服务器兼容性',
    'issues' => '问题',

    // Other
    'source' => '资源',
    'support' => '帮助',
    'admin_dir_still_exists' => '警告! <strong>modules/Core/pages/admin</strong> 目录仍不存在。请删除这个目录。',
    'mod_dir_still_exists' => '警告! <strong>modules/Core/pages/mod</strong> 目录仍不存在。请删除这个目录。',

    // Announcements
    'announcements' => '公告',
    'new_announcement' => '新公告',
    'announcement_info' => '创建公告来对所选定的组的所有成员在指定的页面上显示',
    'creating_announcement' => '创建公告',
    'editing_announcement' => '编辑公告',
    'creating_announcement_success' => '创建公告成功。',
    'editing_announcement_success' => '更新公告成功。',
    'creating_announcement_failure' => '创建公告失败。',
    'editing_announcement_failure' => '更新公告失败。',
    'announcement_icon_instructions' => '您也可以为公告添加图标，例如使用 <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank" rel="noopener nofollow">Font Awesome</a> 或 <a href="https://fomantic-ui.com/elements/icon.html" target="_blank" rel="noopener nofollow">Fomantic UI</a>.',
    'header' => '头部',
    'message' => '信息',
    'text_colour' => '文字颜色',
    'background_colour' => '背景颜色',
    'closable' => '可关闭',
    'can_view_announcement' => '可查看公告',
    'verify_delete_announcement' => '您确定要删除此公告吗?',
    'deleted_announcement_success' => '公告删除成功。',
    'header_required' => '头部为必填项',
    'message_required' => '消息为必填项',
    'background_colour_required' => '背景颜色为必填项',
    'text_colour_required' => '文字颜色为必填项',
    'no_announcements' => '还没有任何公告呢!',
    'announcement_order' => '顺序',
    'announcement_hook_info' => '公告创建',

    // OAuth
    'oauth' => 'OAuth',
    'oauth_info' => 'Configure OAuth providers to allow users to login with their social network accounts. <a href="https://docs.namelessmc.com/en/oauth" target="_blank">Check out our documentation for help.</a>',
    'unlink' => 'Unlink',
    'identifier' => 'Identifier',
    'unlink_account_confirm' => 'Are you sure you want to forcibly unlink this provider from this user?',
    'unlink_account_success' => 'Successfully unlinked their account from {x}.', // Don't replace {x}
];
