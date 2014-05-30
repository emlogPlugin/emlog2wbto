<?php
/*
Plugin Name: 同步到社交网络
Version: 0.1
Plugin URL:http://www.justintseng.com
Description: 可以将在emlog内发布的碎语、日志同步到新浪微博、豆瓣广场、QQ说说，可在发表日志或碎语时选择是否同步，基于089858的微博通插件制作。
Author: Justin Tseng
Author Email: admin@justintseng.com
Author URL: http://www.justintseng.com
*/
!defined('EMLOG_ROOT') && exit('access deined!');

require_once 'emlog2wbto_profile.php';
require_once 'emlog2wbto_config.php';

function emlog2wbto_Blog_hide()
{
?>
    <input type="checkbox" id="douban" value="1" name="douban" checked="checked" /><label for="douban">同步到豆瓣广播</label>
    <input type="checkbox" id="weibo" value="1" name="weibo" checked="checked" /><label for="weibo">同步到新浪微博</label>
    <input type="checkbox" id="qq" value="1" name="qq"/><label for="qq">同步到QQ说说</label>
<?php
}
    addAction('adm_writelog_head','emlog2wbto_Blog_hide');//挂载

function emlog2wbto_Blog($blogid) {
    global $title, $ishide, $action, $isurlrewrite, $douban_hide, $weibo_hide, $qq_hide;

    $douban_hide = isset($_POST['douban']) ? 'y' : 'n';
	$weibo_hide = isset($_POST['weibo']) ? 'y' : 'n';
	$qq_hide = isset($_POST['qq']) ? 'y' : 'n';

    if('y' == $ishide) {//忽略写日志时自动保存
        return false;
    }
    if('edit' == $action) {//忽略编辑日志
        return false;
    }
    if('autosave' == $action && 'n' == $ishide) {//忽略编辑日志时异步保存
        return false;
    }

    $t = stripcslashes(trim($title)) . ' ' . Url::log($blogid);
	
	$hide_pid = '';
	if($weibo_hide == 'y'){
		$hide_pid .= "1,";
	}
	if($douban_hide == 'y'){
		$hide_pid .= "14,";
	}
	if($qq_hide == 'y'){
		$hide_pid .= "35";
	}
	
    if($hide_pid == '') {//全部不同步
        return false;
    }

    $postData = 'content='.urlencode($t);
	$postData .= '&aid=0&pid=' . $hide_pid;
	$postData .= '&source=' . WBTO_API_SOURCE;
	
    emlog2wbto_httpRequestSocket($postData, WBTO_API_DOMAIN, WBTO_API_POST_PATH);
}

if (WBTO_SYNC == '3' || WBTO_SYNC == '1') {
    addAction('save_log', 'emlog2wbto_Blog');
}

function emlog2wbto_twitter_hide()
{
?>
    <div style="margin:5px;">
    <input type="checkbox" id="douban" value="1" name="douban" checked="checked" /><label for="douban">同步到豆瓣广播</label>
    <input type="checkbox" id="weibo" value="1" name="weibo" checked="checked" /><label for="weibo">同步到新浪微博</label>
    <input type="checkbox" id="qq" value="1" name="qq"/><label for="qq">同步到QQ说说</label>
    </div>
<?php
}
    addAction('twitter_head','emlog2wbto_twitter_hide');//挂载

function emlog2wbto_twitter_hide2()
{
?>
    <div style="margin:5px;">
    <input type="checkbox" id="douban" value="1" name="douban" checked="checked" /><label for="douban">同步到豆瓣广播</label>
    <input type="checkbox" id="weibo" value="1" name="weibo" checked="checked" /><label for="weibo">同步到新浪微博</label>
    <input type="checkbox" id="qq" value="1" name="qq"/><label for="qq">同步到QQ说说</label>
    </div>
<?php
}
    addAction('adm_twitter_head','emlog2wbto_twitter_hide2');//挂载

function emlog2wbto_Twitter($t) {
	
	global $douban_hide, $weibo_hide, $qq_hide;

	$douban_hide = isset($_POST['douban']) ? 'y' : 'n';
	$weibo_hide = isset($_POST['weibo']) ? 'y' : 'n';
	$qq_hide = isset($_POST['qq']) ? 'y' : 'n';

	$hide_pid = '';
	if($weibo_hide == 'y'){
		$hide_pid .= "1,";
	}
	if($douban_hide == 'y'){
		$hide_pid .= "14,";
	}
	if($qq_hide == 'y'){
		$hide_pid .= "35";
	}
	
    if($hide_pid == '') {//全部不同步
        return false;
    }

    $postData = 'content='.urlencode(stripcslashes($t));
	$postData .= '&aid=0&pid=' . $hide_pid;
    if (WBTO_TFROM == '4') {
        $postData = 'content='.urlencode(stripcslashes(subString($t, 0, 300)) . ' - 来自博客：' . BLOG_URL);
    }
	$postData .= '&source=' . WBTO_API_SOURCE;
	
    emlog2wbto_httpRequestSocket($postData, WBTO_API_DOMAIN, WBTO_API_POST_PATH);
}
if (WBTO_SYNC == '2' || WBTO_SYNC == '1') {
    addAction('post_twitter', 'emlog2wbto_Twitter');
}
function emlog2wbto_menu() {
    echo '<div class="sidebarsubmenu" id="emlog_emlog2wbto"><a href="./plugin.php?plugin=emlog2wbto">同步到社交网络</a></div>';
}

addAction('adm_sidebar_ext', 'emlog2wbto_menu');
function emlog2wbto_httpRequestSocket($request, $host, $path, $port = 80) {
    $contentLength = strlen($request);
	$http_request  = "POST $path HTTP/1.1\r\n";
	$http_request .= "Host: $host\r\n";
	$http_request .= "Content-type: application/x-www-form-urlencoded\r\n";
	$http_request .= "Content-Length: $contentLength\r\n";
	$http_request .= "Authorization: Basic ".base64_encode(WBTO_USER_NAME . ':' . WBTO_USER_PASSWD)."\r\n";
	$http_request .= "User-Agent: emlog2wbto V1.0\r\n";
    $http_request .= "Connection: close\r\n";
    $http_request .= "\r\n";
	$http_request .= $request;

	$response = '';
	if( false != ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {
		fwrite($fs, $http_request);
		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);
	}
	return $response; 
}
?>