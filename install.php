<?php
// 若存在配置文件，则拒绝请求
if (file_exists(__DIR__ . '/config.inc.php')) http_response_code(404);
?>
<?php
define('__APP_NAME__', 'PhpServer');
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/autoload.php';
$excp; // 错误实例
// 默认程序配置信息,若存在 POST 数据，则更新为 POST 数据
$config = [
  'app_config' => [
    'name' => __APP_NAME__,
    'license' => ''
  ], // 应用配置
  'route_config' => [
    'rootPath' => isset($_POST['routeRootPath']) ? $_POST['routeRootPath'] : '/?',
  ], // 路由配置
  'api_config' => [], // 接口配置
  'view_config' => [], // 视图配置
  'proxy_config' => [], // 代理配置
  'storage_config' => [
    'type' => isset($_POST['storageType']) ? $_POST['storageType'] : 'db',
    'path' => isset($_POST['storagePath']) ? $_POST['storagePath'] : '/storage/',
  ], // 数据存储信息
  'db_config' => [
    'driver' => isset($_POST['dbDriver']) ? $_POST['dbDriver'] : 'pdo_mysql',
    "host" => isset($_POST['dbHost']) ? $_POST['dbHost'] : 'localhost',
    "user" => isset($_POST['dbUser']) ? $_POST['dbUser'] : 'root',
    "password" => isset($_POST['dbPassword']) ? $_POST['dbPassword'] : '',
    "dbname" => isset($_POST['dbDatabase']) ? $_POST['dbDatabase'] : strtolower(__APP_NAME__),
    "port" => isset($_POST['dbPort']) ? $_POST['dbPort'] : '3306',
    "prefix" => isset($_POST['dbPrefix']) ? $_POST['dbPrefix'] : (strtolower(__APP_NAME__) . "_"),
  ], // 数据库信息
  'admin_config' => [
    'url' => isset($_POST['userUrl']) ? $_POST['userUrl'] : ($_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']),
    'name' => isset($_POST['userName']) ? $_POST['userName'] : 'admin',
    'password' => isset($_POST['userPassword']) ? $_POST['userPassword'] : '',
    'mail' => isset($_POST['userMail']) ? $_POST['userMail'] : 'webmaster@yourdomain.com',
  ], // 管理员信息
]; // 程序配置

$created = time();
$tables = [
  'users' => "CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}users`  (
    `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
    `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
    `mail` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
    `url` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
    `screenName` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
    `created` int(10) UNSIGNED NULL DEFAULT 0,
    `activated` int(10) UNSIGNED NULL DEFAULT 0,
    `logged` int(10) UNSIGNED NULL DEFAULT 0,
    `group` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'visitor',
    `authCode` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
    PRIMARY KEY (`uid`) USING BTREE,
    UNIQUE INDEX `name`(`name`) USING BTREE,
    UNIQUE INDEX `mail`(`mail`) USING BTREE
  ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}users` (`name`, `mail`, `url`, `screenName`, `created`, `group`) VALUES ('{$config['admin_config']['name']}', '{$config['admin_config']['mail']}', '{$config['admin_config']['url']}', '{$config['admin_config']['name']}', {$created}, 'administrator');
  ",
  'metas' => "CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}metas`  (
    `mid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `slug` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `type` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `description` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `count` int(10) UNSIGNED NULL DEFAULT 0,
    `order` int(10) UNSIGNED NULL DEFAULT 0,
    `parent` int(10) UNSIGNED NULL DEFAULT 0,
    PRIMARY KEY (`mid`) USING BTREE,
    INDEX `slug`(`slug`) USING BTREE
  ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}metas` (`mid`, `name`, `slug`, `type`, `description`, `count`, `order`, `parent`) VALUES (1, '默认分类', 'default', 'category', '只是一个默认分类', 1, 1, 0);
  ",
  'contents' => "CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}contents`  (
    `cid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `slug` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `created` int(10) UNSIGNED NULL DEFAULT 0,
    `modified` int(10) UNSIGNED NULL DEFAULT 0,
    `text` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
    `order` int(10) UNSIGNED NULL DEFAULT 0,
    `authorId` int(10) UNSIGNED NULL DEFAULT 0,
    `template` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'post',
    `status` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'publish',
    `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `commentsNum` int(10) UNSIGNED NULL DEFAULT 0,
    `allowComment` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
    `allowPing` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
    `allowFeed` char(1) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0',
    `parent` int(10) UNSIGNED NULL DEFAULT 0,
    PRIMARY KEY (`cid`) USING BTREE,
    UNIQUE INDEX `slug`(`slug`) USING BTREE,
    INDEX `created`(`created`) USING BTREE
  ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}contents` (`cid`, `title`, `slug`, `created`, `modified`, `text`, `order`, `authorId`, `template`, `type`, `status`, `password`, `commentsNum`, `allowComment`, `allowPing`, `allowFeed`, `parent`) VALUES (1, '欢迎使用 Typecho', 'start', 1656999862, 1656999862, '<!--markdown-->如果您看到这篇文章,表示您的 blog 已经安装成功.', 0, 1, NULL, 'post', 'publish', NULL, 1, '1', '1', '1', 0);
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}contents` (`cid`, `title`, `slug`, `created`, `modified`, `text`, `order`, `authorId`, `template`, `type`, `status`, `password`, `commentsNum`, `allowComment`, `allowPing`, `allowFeed`, `parent`) VALUES (2, '关于', 'start-page', 1656999862, 1656999862, '<!--markdown-->本页面由 Typecho 创建, 这只是个测试页面.', 0, 1, NULL, 'page', 'publish', NULL, 0, '1', '1', '1', 0);
  ",
  'fields' => "CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}fields`  (
    `cid` int(10) UNSIGNED NOT NULL,
    `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `type` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'str',
    `str_value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
    `int_value` int(10) NULL DEFAULT 0,
    `float_value` float NULL DEFAULT 0,
    PRIMARY KEY (`cid`, `name`) USING BTREE,
    INDEX `int_value`(`int_value`) USING BTREE,
    INDEX `float_value`(`float_value`) USING BTREE
  ) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;",
  'comments' => "CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}comments`  (
    `coid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `cid` int(10) UNSIGNED NULL DEFAULT 0,
    `created` int(10) UNSIGNED NULL DEFAULT 0,
    `author` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `authorId` int(10) UNSIGNED NULL DEFAULT 0,
    `ownerId` int(10) UNSIGNED NULL DEFAULT 0,
    `mail` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `url` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `ip` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `agent` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `text` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
    `type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'comment',
    `status` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'approved',
    `parent` int(10) UNSIGNED NULL DEFAULT 0,
    PRIMARY KEY (`coid`) USING BTREE,
    INDEX `cid`(`cid`) USING BTREE,
    INDEX `created`(`created`) USING BTREE
  ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}comments` (`coid`, `cid`, `created`, `author`, `authorId`, `ownerId`, `mail`, `url`, `ip`, `agent`, `text`, `type`, `status`, `parent`) VALUES (1, 1, 1656999862, 'Typecho', 0, 1, NULL, 'http://typecho.org', '127.0.0.1', 'Typecho 1.1/17.10.30', '欢迎加入 Typecho 大家族', 'comment', 'approved', 0);
  ",
  'options' => "CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options`  (
    `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `user` int(10) UNSIGNED NOT NULL DEFAULT 0,
    `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
    PRIMARY KEY (`name`, `user`) USING BTREE
  ) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('theme', 0, 'WebStack');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('theme:WebStack', 0, 'a:52:{s:7:\"weather\";i:1;s:6:\"usecdn\";i:0;s:9:\"one_wapsl\";i:0;s:8:\"one_pcsl\";i:3;s:6:\"one_ah\";i:1;s:7:\"one_icp\";s:20:\"豫ICP备12222222号\";s:11:\"one_time_no\";i:1;s:8:\"one_time\";s:18:\"5/12/2020 11:13:14\";s:10:\"one_tongji\";s:1:\" \";s:5:\"one_r\";s:8:\"GOGOBODY\";s:9:\"JLazyLoad\";N;s:7:\"Biglogo\";s:36:\"/usr/themes/WebStack/images/logo.png\";s:9:\"smalllogo\";s:49:\"/usr/themes/WebStack/images/logo-collapsed@2x.png\";s:11:\"navrightUrl\";s:36:\"https://github.com/gogobody/WebStack\";s:12:\"navrightText\";s:6:\"Github\";s:12:\"navrightIcon\";s:9:\"fa-github\";s:8:\"one_name\";s:12:\"即刻学术\";s:7:\"one_url\";s:28:\"https://bbs.geekscholar.net/\";s:9:\"one_links\";s:42:\"http://127.0.0.1:9001/index.php/links.html\";s:7:\"Isabout\";s:42:\"http://127.0.0.1:9001/index.php/about.html\";s:14:\"JIndexCarousel\";N;s:15:\"JIndexRecommend\";N;s:16:\"one_footer_links\";i:1;s:12:\"one_top_main\";i:1;s:21:\"one_top_main_one_name\";s:12:\"配置手册\";s:21:\"one_top_main_one_icon\";s:12:\"fa fa-safari\";s:20:\"one_top_main_one_url\";s:28:\"https://bbs.geekscholar.net/\";s:21:\"one_top_main_two_name\";s:12:\"即刻学术\";s:21:\"one_top_main_two_icon\";s:10:\"fa fa-star\";s:20:\"one_top_main_two_url\";s:27:\"https://bbs.geekscholar.net\";s:23:\"one_top_main_three_name\";s:12:\"关于导航\";s:23:\"one_top_main_three_icon\";s:16:\"fa fa-registered\";s:22:\"one_top_main_three_url\";s:41:\"https://bbs.geekscholar.net/d/35-webstack\";s:22:\"one_top_main_four_name\";s:12:\"更多主题\";s:22:\"one_top_main_four_icon\";s:13:\"fa fa-diamond\";s:21:\"one_top_main_four_url\";s:27:\"https://bbs.geekscholar.net\";s:8:\"isSearch\";i:1;s:6:\"isLink\";i:1;s:7:\"fk_zmki\";i:1;s:13:\"fk_one_gzhimg\";s:38:\"/usr/themes/WebStack/images/gzhimg.png\";s:14:\"fk_one_gzhtext\";s:34:\"极客导航-很有范的导航站\";s:9:\"fk_one_qq\";s:9:\"123456789\";s:12:\"fk_one_email\";s:9:\"a@zmki.cn\";s:11:\"fk_one_name\";s:12:\"即刻学术\";s:10:\"fk_one_url\";s:28:\"https://bbs.geekscholar.net/\";s:7:\"bgcolor\";s:7:\"#f1f5f8\";s:8:\"nbgcolor\";s:7:\"#2c2e2f\";s:14:\"hidecategories\";N;s:11:\"use_explore\";i:1;s:18:\"explore_categories\";N;s:17:\"explore_help_href\";s:41:\"https://bbs.geekscholar.net/d/35-webstack\";s:17:\"explore_hot_sites\";s:38:\"https://www.bilibili.com/ranking||B站\";}');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('timezone', 0, '28800');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('lang', 0, 'zh_CN');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('charset', 0, 'UTF-8');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('contentType', 0, 'text/html');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('gzip', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('generator', 0, 'Typecho 1.1/17.10.30');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('title', 0, 'Hello World');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('description', 0, 'Just So So ...');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('keywords', 0, 'typecho,php,blog');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('rewrite', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('frontPage', 0, 'recent');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('frontArchive', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsRequireMail', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsWhitelist', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsRequireURL', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsRequireModeration', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('plugins', 0, 'a:2:{s:9:\"activated\";a:1:{s:8:\"WebStack\";a:1:{s:7:\"handles\";a:10:{s:27:\"admin/write-post.php:option\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:17:\"write_post_option\";}}s:31:\"Widget_Contents_Post_Edit:write\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:30:\"widget_content_post_edit_write\";}}s:20:\"Widget_Archive:query\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:18:\"handleArchiveQuery\";}}s:25:\"Widget_Archive:handleInit\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:23:\"handleArchivehandleInit\";}}s:21:\"Widget_Archive:handle\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:6:\"handle\";}}s:39:\"Widget_Contents_Post_Edit:finishPublish\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:23:\"handlePostPublishFinish\";}}s:20:\"admin/footer.php:end\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:12:\"end_redefine\";}}s:34:\"Widget_Abstract_Contents:contentEx\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:5:\"parse\";}}s:34:\"Widget_Abstract_Contents:excerptEx\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:5:\"parse\";}}s:34:\"Widget_Abstract_Comments:contentEx\";a:1:{i:0;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:5:\"parse\";}}}}}s:7:\"handles\";a:10:{s:27:\"admin/write-post.php:option\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:17:\"write_post_option\";}}s:31:\"Widget_Contents_Post_Edit:write\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:30:\"widget_content_post_edit_write\";}}s:20:\"Widget_Archive:query\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:18:\"handleArchiveQuery\";}}s:25:\"Widget_Archive:handleInit\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:23:\"handleArchivehandleInit\";}}s:21:\"Widget_Archive:handle\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:6:\"handle\";}}s:39:\"Widget_Contents_Post_Edit:finishPublish\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:23:\"handlePostPublishFinish\";}}s:20:\"admin/footer.php:end\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:12:\"end_redefine\";}}s:34:\"Widget_Abstract_Contents:contentEx\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:5:\"parse\";}}s:34:\"Widget_Abstract_Contents:excerptEx\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:5:\"parse\";}}s:34:\"Widget_Abstract_Comments:contentEx\";a:1:{i:90;a:2:{i:0;s:15:\"WebStack_Plugin\";i:1;s:5:\"parse\";}}}}');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentDateFormat', 0, 'F jS, Y \\a\\t h:i a');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('siteUrl', 0, 'http://127.0.0.1:9001');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('defaultCategory', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('allowRegister', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('defaultAllowComment', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('defaultAllowPing', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('defaultAllowFeed', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('pageSize', 0, '5');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('postsListSize', 0, '10');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsListSize', 0, '10');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsHTMLTagAllowed', 0, NULL);
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('postDateFormat', 0, 'Y-m-d');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('feedFullText', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('editorSize', 0, '350');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('autoSave', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('markdown', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('xmlrpcMarkdown', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsMaxNestingLevels', 0, '5');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsPostTimeout', 0, '2592000');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsUrlNofollow', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsShowUrl', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsMarkdown', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsPageBreak', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsThreaded', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsPageSize', 0, '20');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsPageDisplay', 0, 'last');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsOrder', 0, 'ASC');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsCheckReferer', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsAutoClose', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsPostIntervalEnable', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsPostInterval', 0, '60');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsShowCommentOnly', 0, '0');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsAvatar', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsAvatarRating', 0, 'G');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('commentsAntiSpam', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('routingTable', 0, 'a:26:{i:0;a:25:{s:5:\"index\";a:6:{s:3:\"url\";s:1:\"/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:8:\"|^[/]?$|\";s:6:\"format\";s:1:\"/\";s:6:\"params\";a:0:{}}s:7:\"archive\";a:6:{s:3:\"url\";s:6:\"/blog/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:13:\"|^/blog[/]?$|\";s:6:\"format\";s:6:\"/blog/\";s:6:\"params\";a:0:{}}s:2:\"do\";a:6:{s:3:\"url\";s:22:\"/action/[action:alpha]\";s:6:\"widget\";s:9:\"Widget_Do\";s:6:\"action\";s:6:\"action\";s:4:\"regx\";s:32:\"|^/action/([_0-9a-zA-Z-]+)[/]?$|\";s:6:\"format\";s:10:\"/action/%s\";s:6:\"params\";a:1:{i:0;s:6:\"action\";}}s:4:\"post\";a:6:{s:3:\"url\";s:24:\"/archives/[cid:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:26:\"|^/archives/([0-9]+)[/]?$|\";s:6:\"format\";s:13:\"/archives/%s/\";s:6:\"params\";a:1:{i:0;s:3:\"cid\";}}s:10:\"attachment\";a:6:{s:3:\"url\";s:26:\"/attachment/[cid:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:28:\"|^/attachment/([0-9]+)[/]?$|\";s:6:\"format\";s:15:\"/attachment/%s/\";s:6:\"params\";a:1:{i:0;s:3:\"cid\";}}s:8:\"category\";a:6:{s:3:\"url\";s:17:\"/category/[slug]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:25:\"|^/category/([^/]+)[/]?$|\";s:6:\"format\";s:13:\"/category/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"slug\";}}s:3:\"tag\";a:6:{s:3:\"url\";s:12:\"/tag/[slug]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:20:\"|^/tag/([^/]+)[/]?$|\";s:6:\"format\";s:8:\"/tag/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"slug\";}}s:6:\"author\";a:6:{s:3:\"url\";s:22:\"/author/[uid:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:24:\"|^/author/([0-9]+)[/]?$|\";s:6:\"format\";s:11:\"/author/%s/\";s:6:\"params\";a:1:{i:0;s:3:\"uid\";}}s:6:\"search\";a:6:{s:3:\"url\";s:19:\"/search/[keywords]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:23:\"|^/search/([^/]+)[/]?$|\";s:6:\"format\";s:11:\"/search/%s/\";s:6:\"params\";a:1:{i:0;s:8:\"keywords\";}}s:10:\"index_page\";a:6:{s:3:\"url\";s:21:\"/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:22:\"|^/page/([0-9]+)[/]?$|\";s:6:\"format\";s:9:\"/page/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"page\";}}s:12:\"archive_page\";a:6:{s:3:\"url\";s:26:\"/blog/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:27:\"|^/blog/page/([0-9]+)[/]?$|\";s:6:\"format\";s:14:\"/blog/page/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"page\";}}s:13:\"category_page\";a:6:{s:3:\"url\";s:32:\"/category/[slug]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:34:\"|^/category/([^/]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:16:\"/category/%s/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"slug\";i:1;s:4:\"page\";}}s:8:\"tag_page\";a:6:{s:3:\"url\";s:27:\"/tag/[slug]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:29:\"|^/tag/([^/]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:11:\"/tag/%s/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"slug\";i:1;s:4:\"page\";}}s:11:\"author_page\";a:6:{s:3:\"url\";s:37:\"/author/[uid:digital]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:33:\"|^/author/([0-9]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:14:\"/author/%s/%s/\";s:6:\"params\";a:2:{i:0;s:3:\"uid\";i:1;s:4:\"page\";}}s:11:\"search_page\";a:6:{s:3:\"url\";s:34:\"/search/[keywords]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:32:\"|^/search/([^/]+)/([0-9]+)[/]?$|\";s:6:\"format\";s:14:\"/search/%s/%s/\";s:6:\"params\";a:2:{i:0;s:8:\"keywords\";i:1;s:4:\"page\";}}s:12:\"archive_year\";a:6:{s:3:\"url\";s:18:\"/[year:digital:4]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:19:\"|^/([0-9]{4})[/]?$|\";s:6:\"format\";s:4:\"/%s/\";s:6:\"params\";a:1:{i:0;s:4:\"year\";}}s:13:\"archive_month\";a:6:{s:3:\"url\";s:36:\"/[year:digital:4]/[month:digital:2]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:30:\"|^/([0-9]{4})/([0-9]{2})[/]?$|\";s:6:\"format\";s:7:\"/%s/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"year\";i:1;s:5:\"month\";}}s:11:\"archive_day\";a:6:{s:3:\"url\";s:52:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:41:\"|^/([0-9]{4})/([0-9]{2})/([0-9]{2})[/]?$|\";s:6:\"format\";s:10:\"/%s/%s/%s/\";s:6:\"params\";a:3:{i:0;s:4:\"year\";i:1;s:5:\"month\";i:2;s:3:\"day\";}}s:17:\"archive_year_page\";a:6:{s:3:\"url\";s:38:\"/[year:digital:4]/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:33:\"|^/([0-9]{4})/page/([0-9]+)[/]?$|\";s:6:\"format\";s:12:\"/%s/page/%s/\";s:6:\"params\";a:2:{i:0;s:4:\"year\";i:1;s:4:\"page\";}}s:18:\"archive_month_page\";a:6:{s:3:\"url\";s:56:\"/[year:digital:4]/[month:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:44:\"|^/([0-9]{4})/([0-9]{2})/page/([0-9]+)[/]?$|\";s:6:\"format\";s:15:\"/%s/%s/page/%s/\";s:6:\"params\";a:3:{i:0;s:4:\"year\";i:1;s:5:\"month\";i:2;s:4:\"page\";}}s:16:\"archive_day_page\";a:6:{s:3:\"url\";s:72:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:55:\"|^/([0-9]{4})/([0-9]{2})/([0-9]{2})/page/([0-9]+)[/]?$|\";s:6:\"format\";s:18:\"/%s/%s/%s/page/%s/\";s:6:\"params\";a:4:{i:0;s:4:\"year\";i:1;s:5:\"month\";i:2;s:3:\"day\";i:3;s:4:\"page\";}}s:12:\"comment_page\";a:6:{s:3:\"url\";s:53:\"[permalink:string]/comment-page-[commentPage:digital]\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:36:\"|^(.+)/comment\\-page\\-([0-9]+)[/]?$|\";s:6:\"format\";s:18:\"%s/comment-page-%s\";s:6:\"params\";a:2:{i:0;s:9:\"permalink\";i:1;s:11:\"commentPage\";}}s:4:\"feed\";a:6:{s:3:\"url\";s:20:\"/feed[feed:string:0]\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:4:\"feed\";s:4:\"regx\";s:17:\"|^/feed(.*)[/]?$|\";s:6:\"format\";s:7:\"/feed%s\";s:6:\"params\";a:1:{i:0;s:4:\"feed\";}}s:8:\"feedback\";a:6:{s:3:\"url\";s:31:\"[permalink:string]/[type:alpha]\";s:6:\"widget\";s:15:\"Widget_Feedback\";s:6:\"action\";s:6:\"action\";s:4:\"regx\";s:29:\"|^(.+)/([_0-9a-zA-Z-]+)[/]?$|\";s:6:\"format\";s:5:\"%s/%s\";s:6:\"params\";a:2:{i:0;s:9:\"permalink\";i:1;s:4:\"type\";}}s:4:\"page\";a:6:{s:3:\"url\";s:12:\"/[slug].html\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";s:4:\"regx\";s:22:\"|^/([^/]+)\\.html[/]?$|\";s:6:\"format\";s:8:\"/%s.html\";s:6:\"params\";a:1:{i:0;s:4:\"slug\";}}}s:5:\"index\";a:3:{s:3:\"url\";s:1:\"/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:7:\"archive\";a:3:{s:3:\"url\";s:6:\"/blog/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:2:\"do\";a:3:{s:3:\"url\";s:22:\"/action/[action:alpha]\";s:6:\"widget\";s:9:\"Widget_Do\";s:6:\"action\";s:6:\"action\";}s:4:\"post\";a:3:{s:3:\"url\";s:24:\"/archives/[cid:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:10:\"attachment\";a:3:{s:3:\"url\";s:26:\"/attachment/[cid:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:8:\"category\";a:3:{s:3:\"url\";s:17:\"/category/[slug]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:3:\"tag\";a:3:{s:3:\"url\";s:12:\"/tag/[slug]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:6:\"author\";a:3:{s:3:\"url\";s:22:\"/author/[uid:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:6:\"search\";a:3:{s:3:\"url\";s:19:\"/search/[keywords]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:10:\"index_page\";a:3:{s:3:\"url\";s:21:\"/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:12:\"archive_page\";a:3:{s:3:\"url\";s:26:\"/blog/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:13:\"category_page\";a:3:{s:3:\"url\";s:32:\"/category/[slug]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:8:\"tag_page\";a:3:{s:3:\"url\";s:27:\"/tag/[slug]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:11:\"author_page\";a:3:{s:3:\"url\";s:37:\"/author/[uid:digital]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:11:\"search_page\";a:3:{s:3:\"url\";s:34:\"/search/[keywords]/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:12:\"archive_year\";a:3:{s:3:\"url\";s:18:\"/[year:digital:4]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:13:\"archive_month\";a:3:{s:3:\"url\";s:36:\"/[year:digital:4]/[month:digital:2]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:11:\"archive_day\";a:3:{s:3:\"url\";s:52:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:17:\"archive_year_page\";a:3:{s:3:\"url\";s:38:\"/[year:digital:4]/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:18:\"archive_month_page\";a:3:{s:3:\"url\";s:56:\"/[year:digital:4]/[month:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:16:\"archive_day_page\";a:3:{s:3:\"url\";s:72:\"/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:12:\"comment_page\";a:3:{s:3:\"url\";s:53:\"[permalink:string]/comment-page-[commentPage:digital]\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}s:4:\"feed\";a:3:{s:3:\"url\";s:20:\"/feed[feed:string:0]\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:4:\"feed\";}s:8:\"feedback\";a:3:{s:3:\"url\";s:31:\"[permalink:string]/[type:alpha]\";s:6:\"widget\";s:15:\"Widget_Feedback\";s:6:\"action\";s:6:\"action\";}s:4:\"page\";a:3:{s:3:\"url\";s:12:\"/[slug].html\";s:6:\"widget\";s:14:\"Widget_Archive\";s:6:\"action\";s:6:\"render\";}}');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('actionTable', 0, 'a:1:{s:15:\"webstack-action\";s:15:\"WebStack_Action\";}');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('panelTable', 0, 'a:2:{s:5:\"child\";a:1:{i:3;a:3:{i:0;a:6:{i:0;s:18:\"添加导航链接\";i:1;s:18:\"添加导航链接\";i:2;s:61:\"extending.php?panel=WebStack%2Fpages%2Fadmin%2Fwrite-navi.php\";i:3;s:13:\"administrator\";i:4;b:0;i:5;s:0:\"\";}i:1;a:6:{i:0;s:18:\"管理导航链接\";i:1;s:18:\"管理导航链接\";i:2;s:62:\"extending.php?panel=WebStack%2Fpages%2Fadmin%2Fmanage-navi.php\";i:3;s:13:\"administrator\";i:4;b:0;i:5;s:0:\"\";}i:2;a:6:{i:0;s:18:\"管理友情链接\";i:1;s:18:\"管理友情链接\";i:2;s:63:\"extending.php?panel=WebStack%2Fpages%2Fadmin%2Fmanage-links.php\";i:3;s:13:\"administrator\";i:4;b:0;i:5;s:0:\"\";}}}s:4:\"file\";a:3:{i:0;s:41:\"WebStack%2Fpages%2Fadmin%2Fwrite-navi.php\";i:1;s:42:\"WebStack%2Fpages%2Fadmin%2Fmanage-navi.php\";i:2;s:43:\"WebStack%2Fpages%2Fadmin%2Fmanage-links.php\";}}');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('attachmentTypes', 0, '@image@');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('secret', 0, 'Xl^Mgyf2$b!Q%!y7fXl)n^5C5w6vCHdX');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('installed', 0, '1');
  INSERT INTO `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options` (`name`, `user`, `value`) VALUES ('allowXmlRpc', 0, '2');
  "
];
// ?config: 配置页接受数据并更新配置数据
if (isset($_GET['config'])) :
  !isset($_GET['storageType']) ?: ($config['storage_config']['type'] = $_GET['storageType']); // 更新数据存储类型
  !isset($_GET['dbDriver']) ?: ($config['db_config']['driver'] = $_GET['dbDriver']); // 更新适配器
  $action = 'config';
  // [POST]?config: 验证配置信息
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && _get('storage_type') == 'db') :
    try {
      $conn = \Doctrine\DBAL\DriverManager::getConnection(_get('db'));
      // 测试连接
      $count = (int)$conn->fetchOne("SELECT COUNT(*) FROM	information_schema.TABLES WHERE	TABLE_SCHEMA = '{$config['db_config']['dbname']}' AND TABLE_NAME IN (" . "'" . implode(
        "','",
        array_map(function ($value) use ($config) {
          return $config['db_config']['prefix'] . $value;
        }, array_keys($tables))
      ) . "'" . ");");
      // 检测数据库中是否含有原有数据表
      if ($count == sizeof($tables)) : $action = "start&has_old";
      else : $action = "start";
      endif;
    } catch (Exception $e) {
      // 连接失败
      $excp = $e;
    }
  else : $action = "start";
  endif;
endif;
// ?start: 安装页
if (isset($_GET['start']) && !isset($_GET['has_old'])) :
  $action = 'finish';
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && _get('storage_type') == 'db') :
    try {
      $conn = \Doctrine\DBAL\DriverManager::getConnection($config['db_config']);
      $sql = "";
      // 删除原有数据表
      // if (isset($_POST['delete'])) :
      $sql .= "DROP TABLE IF EXISTS " . implode(
        ", ",
        array_map(function ($value) use ($config) {
          return $config['db_config']['prefix'] . $value;
        }, array_keys($tables))
      ) . ";\n\n";
      // endif;
      // 创建对应数据表&插入数据
      $sql .= implode("\n\n", array_values($tables));
      $conn->executeStatement($sql);
    } catch (Exception $e) {
      // 连接失败
      $excp = $e;
      $action = 'start';
    }
  endif;
endif;
// ?finish 完成页
if (isset($_GET['finish'])) :
  if ($_SERVER['REQUEST_METHOD'] == 'POST') :
    // 写入配置文件
    $config_content = "<?php return array(\n";
    foreach ($config as $key => $value) :
      if (is_array($value)) :
        $config_content .= "\t'{$key}' => array(\n";
        foreach ($value as $child_key => $child_value) :
          $config_content .= "\t\t'{$child_key}' => '{$child_value}',\n";
        endforeach;
        $config_content .= "\t),\n";
      else :
        $config_content .= "\t'{$key}' => '{$value}',\n";
      endif;
    endforeach;
    $config_content .= ");";
    file_put_contents(__DIR__ . "/config.inc.php", $config_content);
  endif;
endif;

?>


<!doctype html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo _get('app_name'); ?> 安装程序</title>
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
  <style>
    h1,
    h3,
    h3 {
      font-weight: bold;
    }

    .hidden {
      display: none;
    }

    .jumbotron {
      background-color: #292D33;
      color: #FFF;
    }

    .jumbotron ol {
      margin-top: 30px;
      list-style: none;
      color: #999;
    }

    .jumbotron li {
      display: inline-block;
      margin: 0 .8em;
    }

    .jumbotron li.active {
      color: #fff;
      font-weight: bold;
    }

    .jumbotron li span {
      display: inline-block;
      margin-right: 5px;
      width: 24px;
      height: 24px;
      line-height: 20px;
      border: 2px solid #999;
      border-radius: 50%;
      text-align: center;
    }

    .form-group .checkbox {
      margin-left: 20px;
    }

    .form-control[type='checkbox'] {
      width: 20px;
      height: 20px;
    }

    .form-control[type='checkbox']+p {
      margin-left: 10px;
      line-height: 26px;
    }
  </style>
</head>

<body>
  <div class="jumbotron text-center">
    <h1><strong><?php echo _get('app_name'); ?></strong></h1>
    <ol>
      <li <?php if (!isset($_GET['finish']) && !isset($_GET['start']) && !isset($_GET['config'])) : ?> class="active" <?php endif; ?>><span>1</span>欢迎使用</li>
      <li <?php if (isset($_GET['config'])) : ?> class="active" <?php endif; ?>><span>2</span>初始化配置</li>
      <li <?php if (isset($_GET['start'])) : ?> class="active" <?php endif; ?>><span>3</span>开始安装</li>
      <li <?php if (isset($_GET['finish'])) : ?> class="active" <?php endif; ?>><span>4</span>安装成功</li>
    </ol>
  </div>
  <?php if (!isset($_GET['finish']) && !isset($_GET['start']) && !isset($_GET['config'])) : ?>
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <form method="post" action="?config">
            <h1>欢迎使用 <?php echo _get('app_name'); ?></h1>
            <div class="form-group">
              <h3>安装说明</h3>
              <p><strong>本安装程序将自动检测服务器环境是否符合最低配置需求. 如果不符合, 将在上方出现提示信息, 请按照提示信息检查您的主机配置. 如果服务器环境符合要求, 将在下方出现 "开始下一步" 的按钮, 点击此按钮即可一步完成安装.</strong></p>
              <h3>许可及协议</h3>
              <p><?php echo _get('app_name'); ?> 基于 <a href="https://www.apache.org/licenses/LICENSE-2.0">Apache License 2.0</a> 协议发布, 我们允许用户在 Apache License 2.0 协议许可的范围内使用, 拷贝, 修改和分发此程序. 在 Apache License 2.0 许可的范围内，您可以自由地将其用于商业以及非商业用途.</p>
              <p><?php echo _get('app_name'); ?> 软件由其社区提供支持, 核心开发团队负责维护程序日常开发工作以及新特性的制定. 如果您遇到使用上的问题, 程序中的 BUG, 以及期许的新功能, 欢迎您在社区中交流或者直接向我们贡献代码. 对于贡献突出者, 他的名字将出现在贡献者名单中.</p>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">我准备好了, 开始下一步 »</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php elseif (isset($_GET['config']) || isset($_GET['start'])) : ?>
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <form method="post" action="?<?php echo $action ?>" name="config">
            <div style="<?php echo !isset($_GET['start']) ?: 'display: none;' ?>">
              <h1>确认您的配置</h1>
              <div>
                <h3>路由配置</h3>
                <div class="form-group">
                  <label for="routeRootPath">是否开启伪静态</label>
                  <div class="checkbox">
                    <input class="form-control" type="checkbox" name="routeRootPath" value="" <?php echo _get('route_rootPath') == '' ? "checked" : NULL; ?> />
                    <p class="text-muted">去掉地址栏中的 "/?/"</p>
                  </div>
                </div>
              </div>
              <div>
                <h3>应用程序接口配置</h3>
              </div>
              <div>
                <h3>视图配置</h3>
              </div>
              <div>
                <h3>数据存储配置</h3>
                <div class="form-group">
                  <label for="storageType">存储类型</label>
                  <select class="form-control" name="storageType" id="storageType">
                    <option value="db" <?php echo _get('storage_type') == 'db' ? "selected" : NULL; ?>>数据库</option>
                    <option value="file" <?php echo _get('storage_type') == 'file' ? "selected" : NULL; ?>>本地目录</option>
                  </select>
                </div>
                <div class="form-group <?php echo _get('storage_type') == 'file' ? NULL : 'hidden' ?>">
                  <label for="storagePath">存储路径</label>
                  <input class="form-control" name="storagePath" type="text" value="<?php echo _get('storage_path') ?>" />
                </div>
              </div>
              <div class="<?php echo _get('storage_type') == 'db' ? NULL : 'hidden' ?>">
                <h3>数据库配置</h3>
                <?php if (!empty($excp) && !empty($_POST)) : ?>
                  <div class="alert alert-danger" role="alert">
                    对不起，无法连接数据库，请先检查数据库配置再继续进行安装
                  </div>
                <?php endif; ?>
                <div class="form-group">
                  <label for="dbDriver">数据库适配器</label>
                  <select class="form-control" name="dbDriver" id="dbDriver">
                    <option value="pdo_mysql" <?php echo  _get('db_driver') == 'pdo_mysql' ? "selected" : NULL; ?>>Pdo 驱动 Mysql 适配器</option>
                    <option value="pdo_sqlite" <?php echo _get('db_driver') == 'pdo_sqlite' ? "selected" : NULL; ?>>Pdo 驱动 SQLite 适配器 (SQLite 3.x)</option>
                    <!-- <option value="mysqli"></option> -->
                    <!-- <option value="pdo_pgsql"></option> -->
                    <!-- <option value="pdo_sqlsrv"></option> -->
                    <!-- <option value="sqlsrv"></option> -->
                  </select>
                  <p class="text-muted">请根据您的数据库类型选择合适的适配器</p>
                </div>
                <div class="form-group">
                  <label for="dbHost">数据库地址</label>
                  <input class="form-control" type="text" class="text" name="dbHost" id="dbHost" value="<?php echo _get('db_host') ?>">
                  <p class="text-muted">您可能会使用 "localhost"</p>
                </div>
                <div class="form-group">
                  <label for="dbPort">数据库端口</label>
                  <input class="form-control" type="text" class="text" name="dbPort" id="dbPort" value="<?php echo _get('db_port') ?>">
                  <p class="text-muted">如果您不知道此选项的意义, 请保留默认设置</p>
                </div>
                <div class="form-group">
                  <label for="dbUser">数据库用户名</label>
                  <input class="form-control" type="text" class="text" name="dbUser" id="dbUser" value="<?php echo _get('db_user') ?>">
                  <p class="text-muted">您可能会使用 "root"</p>
                </div>
                <div class="form-group">
                  <label for="dbPassword">数据库密码</label>
                  <input class="form-control" type="password" class="text" name="dbPassword" id="dbPassword" value="<?php echo _get('db_password') ?>">
                </div>
                <div class="form-group">
                  <label for="dbDatabase">数据库名</label>
                  <input class="form-control" type="text" class="text" name="dbDatabase" id="dbDatabase" value="<?php echo _get('db_dbname') ?>">
                  <p class="text-muted">请您指定数据库名称</p>
                </div>
                <input type="hidden" name="dbCharset" value="utf8">
                <div class="form-group">
                  <label for="dbPrefix">数据库前缀</label>
                  <input class="form-control" type="text" class="text" name="dbPrefix" id="dbPrefix" value="<?php echo _get('db_prefix') ?>">
                  <p class="text-muted">默认前缀是 "<?php echo strtolower(_get('app_name')) . "_"; ?>"</p>
                </div>
              </div>
              <div>
                <h3>创建您的管理员帐号</h3>
                <div class="form-group">
                  <label for="userUrl">网站地址</label>
                  <input class="form-control" type="text" name="userUrl" id="userUrl" class="text" value="<?php echo _get('admin_url') ?>">
                  <p class="text-muted">这是程序自动匹配的网站路径, 如果不正确请修改它</p>
                </div>
                <div class="form-group">
                  <label for="userName">用户名</label>
                  <input class="form-control" type="text" name="userName" id="userName" class="text" value="<?php echo _get('admin_name') ?>">
                  <p class="text-muted">请填写您的用户名</p>
                </div>
                <div class="form-group">
                  <label for="userPassword">登录密码</label>
                  <input class="form-control" type="password" name="userPassword" id="userPassword" class="text" value="<?php echo _get('admin_password') ?>">
                  <p class="text-muted">请填写您的登录密码, 如果留空系统将为您随机生成一个</p>
                </div>
                <div class="form-group">
                  <label for="userMail">邮件地址</label>
                  <input class="form-control" type="text" name="userMail" id="userMail" class="text" value="<?php echo _get('admin_mail') ?>">
                  <p class="text-muted">请填写一个您的常用邮箱</p>
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">确认, 开始安装 »</button>
              </div>
            </div>
            <?php if (isset($_GET['start'])) : ?>
              <?php if (isset($_GET['has_old'])) : ?>
                <h1>安装失败</h1>
                <div class="alert alert-danger" role="alert">
                  <p>安装程序检查到原有数据表已经存在.</p>
                  <br>
                  <button type="submit" name="delete" value="1" class="btn btn-danger">删除原有数据</button>
                  或者
                  <button type="submit" name="goahead" value="1" class="btn btn-primary">使用原有数据</button>
                </div>
              <? endif ?>
              <?php if (!empty($excp)) : ?>
                <div class="alert alert-danger" role="alert">
                  <h1>安装失败</h1>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </div>
    <script>
      <?php if (isset($_GET['config'])) : ?>
        var storageType = document.config.storageType;
        var dbDriver = document.config.dbDriver;
        storageType.onchange = function() {
          document.config.action = "./install.php?config&storageType=" + this.value;
          $('button[type="submit"]').click();
        }
        dbDriver.onchange = function() {
          document.config.action = "install.php?config&dbDriver=" + this.value;
          $('button[type="submit"]').click();
        }
      <?php endif ?>
      <?php if ((isset($_GET['config']) && sizeof($_GET) == 1 && in_array($action, ['start', 'start&has_old'])) || (isset($_GET['start']) && in_array($action, ['finish']))) : ?>
        $('button[type="submit"]').click();
      <?php endif; ?>
    </script>
  <?php elseif (isset($_GET['finish'])) : ?>
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <form method="post" action="">
            <h1>安装成功</h1>
            <div class="alert alert-success" role="alert">
              您选择了使用原有的数据, 您的用户名和密码和原来的一致
            </div>
            <div class="alert alert-success" role="alert">
              <ul style="padding-inline-start: 20px;">
                <li>您的用户名是: <?php echo _get('admin_name') ?></li>
                <li>您的密码是: <?php echo _get('admin_password') ?></li>
              </ul>
            </div>
            <div class="alert alert-warning" role="alert">
              <a href="#">
                参与用户调查, 帮助我们完善产品
              </a>
            </div>
          </form>
          <p>
            您可以将下面两个链接保存到您的收藏夹:
          </p>
          <ul>
            <li><a href="#">
                点击这里访问您的控制面板
              </a></li>
            <li><a href="#">
                点击这里查看您的 Blog
              </a></li>
          </ul>
          <p>
            希望您能尽情享用 <?php echo _get('app_name') ?> 带来的乐趣!
          </p>
        </div>
      </div>
    </div>
  <?php endif; ?>

</body>

</html>