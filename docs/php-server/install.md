# Install

## Welcome

### 安装说明

本安装程序将自动检测服务器环境是否符合最低配置需求. 如果不符合, 将在上方出现提示信息, 请按照提示信息检查您的主机配置. 如果服务器环境符合要求, 将在下方出现 "开始下一步" 的按钮, 点击此按钮即可一步完成安装.

### 许可及协议

`PhpServer` 基于 [Apache License 2.0](https://www.apache.org/licenses/LICENSE-2.0) 协议发布, 我们允许用户在 Apache License 2.0 协议许可的范围内使用, 拷贝, 修改和分发此程序. 在 Apache License 2.0 许可的范围内，您可以自由地将其用于商业以及非商业用途.

`PhpServer` 软件由其社区提供支持, 核心开发团队负责维护程序日常开发工作以及新特性的制定. 如果您遇到使用上的问题, 程序中的 BUG, 以及期许的新功能, 欢迎您在社区中交流或者直接向我们贡献代码. 对于贡献突出者, 他的名字将出现在贡献者名单中.

## Initialize

### 路由

- 是否开启伪静态`/?/`: `true`(default)

### 接口

### 视图

- 重定向
  - 主页(`/src/views/home => /home`): `false`(default)
  - 后台(`/src/views/admin => /admin`): `false`(default)
  - 其它

### 数据存储

- 存储类型
  - 数据库(default)
  - 本地目录
- 存储路径

### 数据库

- 数据库适配器
  - MySQL(default)
  - SQLite
- 数据库地址: `localhost`(default)
- 数据库端口: `3306`(default)
- 数据库用户名
- 数据库密码
- 数据库名: `phpserver`(default)
- 数据库前缀: `phpserver_`(default)

#### metas 标识表

| 列名        | 类型        | 说明     | 默认值 | 可选值 |
| ----------- | ----------- | -------- | ------ | ------ |
| mid         | int(10)     | 编号     |
| name        | varchar(25) | 名称     |
| slug        | varchar()   | 别名     |
| type        | varchar()   | 类型     |
| description | varchar()   | 描述     |
| count       | int(10)     | 计数统计 |
| order       | int(10)     | 排序权重 |
| parent      | int(10)     | 父级编号 |

**默认数据**

| mid | name | slug | type | description | count | order | parent |
| --- | ---- | ---- | ---- | ----------- | ----- | ----- | ------ |
| 1   |      |      |      |             |       |       |        |
| 2   |      |      |      |             |       |       |        |

**建表语句**

```sql
CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}metas`  (
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
```

#### contents 文本表

| 列名         | 类型 | 说明 | 默认值 | 可选值 |
| ------------ | ---- | ---- | ------ | ------ |
| cid          |      |      |        |
| title        |      |      |        |
| slug         |      |      |        |
| created      |      |      |        |
| modified     |      |      |        |
| text         |      |      |        |
| order        |      |      |        |
| authorId     |      |      |        |
| template     |      |      |        |
| type         |      |      |        |
| status       |      |      |        |
| password     |      |      |        |
| commentsNum  |      |      |        |
| allowComment |      |      |        |
| allowPing    |      |      |        |
| allowFeed    |      |      |        |
| parent       |      |      |        |

**建表语句**

```sql
CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}contents`  (
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
```

#### relationships 关联表

| 列名 | 类型 | 说明     | 默认值 | 可选值 |
| ---- | ---- | -------- | ------ | ------ |
| cid  |      | 文本编号 |
| mid  |      | 标识编号 |

**建表语句**

```sql

```

#### fields 附加字段表

| 列名        | 类型        | 说明     | 默认值 | 可选值 |
| ----------- | ----------- | -------- | ------ | ------ |
| cid         | int(10)     | 文本编号 |
| name        | varchar(25) |          |
| type        | varchar(25) | 类型     |
| str_value   | varchar()   | 字符     |
| int_value   | int()       | 整数     |
| float_value | float()     | 浮点     |

**建表语句**

```sql
CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}fields`  (
  `cid` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `type` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'str',
  `str_value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `int_value` int(10) NULL DEFAULT 0,
  `float_value` float NULL DEFAULT 0,
  PRIMARY KEY (`cid`, `name`) USING BTREE,
  INDEX `int_value`(`int_value`) USING BTREE,
  INDEX `float_value`(`float_value`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
```

#### comments 回复表

| 列名 | 类型 | 说明 | 默认值 | 可选值 |
| ---- | ---- | ---- | ------ | ------ |
|      |      |      |        |
|      |      |      |        |
|      |      |      |        |
|      |      |      |        |
|      |      |      |        |
|      |      |      |        |

**建表语句**

```sql
CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}comments`  (
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
```

#### users 用户表

| 列名       | 类型 | 说明 | 默认值 | 可选值 |
| ---------- | ---- | ---- | ------ | ------ |
| uid        |      |      |        |
| name       |      |      |        |
| password   |      |      |        |
| mail       |      |      |        |
| url        |      |      |        |
| screenName |      |      |        |
| created    |      |      |        |
| activated  |      |      |        |
| logged     |      |      |        |
| group      |      |      |        |
| authCode   |      |      |        |

**建表语句**

```sql
CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}users`  (
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
```

#### options 配置表

| 列名  | 类型 | 说明 | 默认值 | 可选值 |
| ----- | ---- | ---- | ------ | ------ |
| name  |      |      |        |
| user  |      |      |        |
| value | text |      |        |

```sql
CREATE TABLE `{$config['db_config']['dbname']}`.`{$config['db_config']['prefix']}options`  (
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`name`, `user`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
```

#### logs 日志表

### 管理员账号

- 网站地址
- 用户名: `admin`(default)
- 密码
- 邮箱

## Setup

## Complete
