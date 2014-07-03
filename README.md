# 苍穹 - 贴吧签到助手

![screenshot](http://ww2.sinaimg.cn/mw690/841aea59gw1ehu0z9tsubj20ka099wep.jpg "screenshot")

## 简介

* 由百度非著名用户飞龙开发，开放给全部吧友进行使用。  
* 小型轻量化的贴吧签到助手，仅有4个页面，6个表。
* 以小璨的[贴吧签到助手](http://signtb.sinaapp.com/)为基础制作，增加对非云主机的支持。
* 全自动全天候无人值守，智能监控，拒绝漏签。
* 模拟客户端签到，首签+6，连续+8。
* 永久开源，协议宽松。

## 注意

* 仅支持PHP+MYSQL环境。
* 服务器必须支持mysqli扩展。
* 一分钟最多能签到30个贴吧，用户和贴吧个数不要设置太多。

## 安装

1. 对于非SAE、BAE的用户，修改"config.php"中的数据库设置。SAE、BAE已设置好，无需更改。
2. 修改"config.php"中的密钥，以保证安全。
3. 运行"install.php"创建数据库表。如果已经安装之前版本的，请运行"update.php";
4. 非云主机请在管理面板设置定时任务（cronjob）。不支持定时任务的主机请将"cron.php"添加到第三方网站监控。

## 反馈

任何反馈请发到我的博客[龙哥盟](http://www.flygon.net)或者[苍海·国际](http://www.258ch.com)。