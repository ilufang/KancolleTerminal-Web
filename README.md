KancolleTerminal Web
====================

**Web-based Kancolle Proxy and Helpers**

(c) Fang Lu

*当前本项目停止维护，暂时不会添加任何新内容，如果有疑问或者bug欢迎在Issues中提出*

Features
--------

KancolleTerminal-Web是使用PHP编写的，主要目标为LAMP服务器的舰队Collection游戏中继程序。参考原Java版Kancolle Terminal的功能，Kancolle Terminal Web主要功能即作为中间人转发游戏数据，并借此优化游戏体验。经过一段时间的不断修改，目前版本的Kancolle Terminal Web主要有以下功能:

* **静态资源缓存和加速**
* **转发API请求**
* **DMM登录,免代理开始游戏**
* 修改静态资源，装饰性/功能性改变游戏外观 (如魔改资源)
* 修改API请求的返回值，装饰性/功能性改变游戏行为 (如翻译文本)
* 收集和整理玩家游戏数据，提供辅助信息 ([poi](/poooi)的网页版)
* 统计和保存玩家的开发、建造、掉落数据
* 允许玩家同时运行多个游戏实例，简化部分游戏体验
* 对于朋友间的私人主机，共享游戏数据 (查看全服建造结果，投票禁言使欧洲人猫等)
* 在因网络连接或服务器负载不稳定猫时，通过网页补发请求完成当前战斗
* 本地化处理部分请求优化游戏体验

以上所有功能均在服务器端实现，通过网页即可访问，不需要在客户端下载安装任何内容。具体功能详见wiki.

Installation
------------

您需要以下软件运行Kancolle Terminal Web

* 一台有固定地址(FQDN或者IP)的服务器
* Apache 2
* PHP 5.6+
* MySQL
* 常见Apache插件: mod_rewrite, mod_php5等
* 常见PHP插件: mysqli, curl等

安装和配置:

1. 将本repo下载或克隆到您的Web服务器目录下，如果需要为其创建vhost
2. 编辑`config.php`文件，填写您的MySQL服务器信息、服务器的域名或IP、管理员密码等
3. 在浏览器中打开`setup.php`文件初始化数据表，然后删除`setup.php`或禁止外部访问
4. 在浏览器中打开`index.php`，Kancolle Terminal Web基本功能应该已经可以正常运行了

可选步骤:

* 编辑`eula.html`文件。文件内容将在注册页面中显示为用户协议
* `filemgr`目录包含一个具有写入权限的php文件管理器，允许用户上传修改过的swf等资源文件。
	* 如果您希望禁用此功能，请删除`filemgr`文件夹；
	* 如果您希望使用此功能，请编辑`filemgr/scripts/filemanager.config.js`，将`options.fileRoot`（默认为`/var/www/kancolle/kcres/files/`）修改为您服务器上`kcres/files/`目录的绝对路径
	* 如果您希望使用您自己喜爱的PHP文件管理器，或者配置FTP服务，请删除`filemgr`内容。您的文件管理系统应该指向`kcres/files/`目录
* `kcres/swf2img.php`文件可以从服务器上的swf船只资源中提取图片格式资源放置在网页中。本功能需要通过命令行调用java
	* 如果您希望使用此功能，请编辑`kcres/swf2img.php`并按照其中的说明操作
	* 如果您无法使用此功能，请编辑`ships.php`并按照其中的说明操作
* `build-logs.php`, `kcapi/dmm-names.json`, `ban/index.php`可能包含个人信息。如果您的服务器比较开放，建议您阻止对这些文件的访问

详细配置说明请参考wiki.

Usage
-----

基本游戏用法(适用于网页或者查看器)：

1. 注册Kancolle Terminal Web账号
2. 登录后，滚动至页面底部，在`配置信息`一栏中填写您的DMM用户名(邮箱)和密码。点击更新链接，弹出`游戏链接已更新`提示后等待页面自动刷新
3. 在页面顶部`开始游戏`下会出现链接，点击`以Flash运行游戏`即可开始游戏

修改静态资源:

1. 打开文件管理，在`mods`下创建自己的文件夹，上传修改过的文件
2. 寻找`翻译器`，点击方块底部的`添加行`
3. `Type`填写`RewriteRule`，`Subject`填写`/kcs/`打头的实际游戏文件路径，`Operation`填写`/mods/`打头的您刚才上传的修改文件的路径，`Options`留空
4. 点击`更新`，弹出`替换规则已更新`即可
5. 刷新游戏即可生效。如果您的浏览器无法进行ETag/If-None-Match验证，请禁用或者删除浏览器缓存重试

修改请求/翻译:

1. 寻找`翻译器`，点击方块底部的`添加行`
2. `Type`填写`PregReplace`，`Subject`填写您希望被替换的文本(如`まるゆ`)，`Operation`填写您希望显示的文本(如`马路油`)，`Options`留空
3. 点击`更新`，弹出`替换规则已更新`即可
4. 刷新游戏即可生效

紧急发包救猫:

1. 滚动到页面底部，寻找`发包器`
2. 在新窗口中打开Viewer(`/ii`切记勿开带Flash的任何页面)
3. 根据您猫时请求，点击相应的按钮展开。如点阵型猫选择`开始战斗`，点夜战猫选择`开始战斗(夜战)`，点进击猫选择`进击`，战斗动画进入结算时猫选择`获取战斗结果`
4. 如果需要，填写参数，比如开始战斗时阵型ID为单纵=1,复纵=2,轮型=3,梯形=4,单横=5. (暂不支持联合战斗)
5. 点击发送. 等待下方出现结果。观看Viewer中`战斗分析`(prophet)的结果
6. 根据战斗分析提供的结果继续发包。两次点击`发送`需要至少间隔30s以上
7. 完成末端节点并且完成结算，或者遇到大破、重大损伤或者偏航需要撤退/回港时，请直接刷新Flash

更多详细说明请参考wiki.

License
-------

本项目遵循MIT License发布，详情参考`LICENSE`文件。
