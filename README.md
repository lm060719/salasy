# ⏱️ Work Hours & Salary Tracker

一个基于 PHP 和 MySQL 开发的工时记录与工资计算网站，支持用户注册登录、工时录入、月度统计、薪资预估、CSV 导出等功能，适合兼职、小时工或个人工时管理场景。

---

## 📌 项目简介

Work Hours & Salary Tracker 是一个轻量级 Web 应用，用于记录每日工作时长，并根据基础时薪和加班倍率自动计算预计工资。

用户可以按月份查看自己的工时数据，系统会自动汇总本月总工时、预计薪资，并通过图表展示每日工时变化趋势，方便个人进行工资核算与工作记录管理。

---

## ✨ 功能特性

### 👤 用户系统
- 用户注册
- 用户登录 / 退出登录
- Session 登录状态校验

### 📝 工时管理
- 新增工时记录
- 删除工时记录
- 按日期记录工作时长
- 支持填写备注信息

### 💰 薪资计算
- 支持设置个人基础时薪
- 支持加班倍率计算
- 自动计算当日薪资
- 自动统计本月预计工资

### 📊 数据展示
- 按月份筛选工时记录
- 展示本月累计工时
- 展示本月预计薪资
- 折线图展示每日工时走势

### 📤 数据导出
- 支持导出当月 CSV 报表
- 兼容 Excel 打开

---

## 🧱 技术栈

### 后端
- PHP
- PDO
- MySQL

### 前端
- HTML5
- Bootstrap 5
- JavaScript

### 图表
- Chart.js

---

## 📁 项目结构

```text
gongshi.mo776.xyz/
├── config.php         # 数据库连接与登录校验
├── login.php          # 登录 / 注册页面
├── logout.php         # 退出登录
├── index.php          # 主仪表盘页面
├── add_entry.php      # 新增工时记录
├── delete_entry.php   # 删除工时记录
└── export.php         # 导出 CSV 报表
```
🚀 快速开始
1. 克隆项目
```
git clone https://github.com/你的用户名/你的仓库名.git
```
cd 你的仓库目录

3. 配置运行环境

确保本地或服务器已安装：
```

PHP 7.4 及以上

MySQL / MariaDB

Apache / Nginx
```
也可以直接使用 XAMPP、宝塔、PHPStudy 等集成环境运行。

3. 创建数据库

先创建数据库，例如：

CREATE DATABASE gongshi CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
4. 创建数据表

根据项目逻辑，至少需要 users 和 work_hours 两张表。

users 表
```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    hourly_wage DECIMAL(10,2) NOT NULL DEFAULT 0.00
);
```
work_hours 表
```
CREATE TABLE work_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    work_date DATE NOT NULL,
    hours DECIMAL(5,2) NOT NULL,
    multiplier DECIMAL(3,1) NOT NULL DEFAULT 1.0,
    notes VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```
5. 修改数据库配置

编辑 config.php，填写你自己的数据库连接信息：
```
$db_host = 'localhost';
$db_port = '3306';
$db_name = 'gongshi';
$db_user = 'your_username';
$db_pass = 'your_password';
```
6. 启动项目

将项目放到网站根目录后，浏览器访问：
```
http://localhost/项目目录/login.php
```
🖥️ 核心页面说明
登录 / 注册页

用户可直接注册账号并登录系统。

仪表盘首页

登录后可查看：

当前月份

本月累计工时

本月预计薪资

当前基础时薪

工时录入模块

支持填写：

日期

工作时长

加班倍率

备注

工时图表

使用折线图展示当月每日工时变化。

明细表格

展示当月全部记录，并支持删除。

CSV 导出

可将当月工时和工资预估导出为报表文件。

🧠 薪资计算规则

系统工资计算方式为：

当日薪资 = 工时 × 基础时薪 × 加班倍率

本月预计薪资为当月所有记录的当日薪资总和。

加班倍率示例

1.0x：正常工时

1.5x：周末工时

2.0x：节假日工时
