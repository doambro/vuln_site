<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <script src="db_config.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>渗透测试靶场</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
// 启动会话
session_start();
?>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php">渗透测试靶场</a></h1>
            </div>
            <nav>
                <a href="index.php" class="active">首页</a>
                <a href="guestbook.php">留言板</a>
                <a href="about.php">关于我们</a>
                <a href="resources.php">资源中心</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">个人中心</a>
                    <a href="logout.php">退出登录</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">登录</a>
                    <a href="register.php" class="btn-register">注册</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <main>
            <section>
                <h2>欢迎来到渗透测试靶场</h2>
                <p>这是一个用于测试的渗透靶场，包含多种常见的Web安全漏洞示例：</p>
                <ul>
                    <li>SQL注入漏洞（登录/注册/留言板）</li>
                    <li>跨站脚本(XSS)漏洞（存储型/反射型）</li>
                    <li>认证和授权缺陷（水平越权/未授权访问）</li>
                    <li>文件上传漏洞（无类型校验/目录穿越）</li>
                    <li>CSRF漏洞（关键操作无Token验证）</li>
                    <li>敏感信息泄露（数据库凭据/错误信息）</li>
                    <li>不安全的会话管理（会话固定/Cookie未加固）</li>
                </ul>
                <p>请在此环境中练习安全测试技能，探索漏洞利用以及安全防护措施。</p>
            </section>

            <section>
                <h2>测试场景</h2>
                <p>本靶场提供以下测试场景：</p>
                <ol>
                    <li>在<a href="login.php">登录页面</a>尝试SQL注入绕过认证</li>
                    <li>在<a href="register.php">注册页面</a>尝试恶意用户注册</li>
                    <li>在<a href="guestbook.php">留言板</a>尝试XSS攻击</li>
                    <li>在<a href="profile.php">个人中心</a>测试文件上传漏洞</li>
                    <li>在<a href="admin.php">后台管理</a>测试未授权访问漏洞</li>
                    <li>在<a href="logout.php">退出功能</a>测试会话固定漏洞</li>
                    <li>在数据库连接文件测试<a href="db.php">敏感信息泄露</a></li>
                </ol>
                <p>注意：此环境仅供学习和测试使用，请勿用于非法用途。</p>
            </section>
        </main>
    </div>

    <footer>
        <div class="container">
            &copy; 2025 渗透测试靶场 | 仅供教育和学习目的
        </div>
    </footer>
</body>
</html>
