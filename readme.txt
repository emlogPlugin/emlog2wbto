插件名称：同步到社交网络

说明：基于微博通开放API，可以将在emlog内发布的碎语、日志同步到新浪微博、豆瓣广场、QQ说说，可在发表日志或碎语时选择是否同步。你需要在微博通内配置各平台的访问权限。 基于089858的微博通插件制作，主要增加发布文章和碎语时可选择是否同步（同时也限制了只能在此三个平台同步）。

  安装：
    1、上传插件至博客插件目录。
    2、修改admin/views目录下index.php文件，找到<div class="box_1"><textarea class="box2" name="t">为今天写点什么吧 ……</textarea></div>一行，在其下加上<?php doAction('adm_twitter_head'); ?>
    3、修改修改admin/views目录下twitter.php文件，找到<div class="box_1"><textarea class="box" name="t"></textarea></div>一行，在旗下加上<?php doAction('twitter_head'); ?>
    3、后台激活插件，并设置。
    4、发表文章时可分别勾选同步到新浪微博、豆瓣广场、QQ说说。


注意：

1、需要注册微博通wbto.cn帐号，并设置各平台访问权限。
2、由于官方未提供碎语界面显示的挂载点，所以需要修改程序文件增加挂载点才可使用本插件，请谨慎考虑。

Please visit http://www.justintseng.com for more information.