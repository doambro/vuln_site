<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>关于我们 - 渗透测试靶场</title>
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
                <a href="index.php">首页</a>
                <a href="guestbook.php">留言板</a>
                <a href="about.php" class="active">关于我们</a>
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
                <h2>关于本靶场</h2>
                <p>本渗透测试靶场是为了帮助安全爱好者、网络安全专业人士和学生学习和实践Web应用安全测试而设计的。</p>
                
                <h3>靶场目标</h3>
                <p>我们的目标是提供一个安全的环境，让用户能够：</p>
                <ul>
                    <li>学习和理解常见的Web应用安全漏洞</li>
                    <li>练习渗透测试技术和方法</li>
                    <li>掌握漏洞利用和防护措施</li>
                    <li>提高安全意识和防护能力</li>
                </ul>
                
                <h3>包含漏洞类型</h3>
                <p>本靶场包含但不限于以下类型的安全漏洞：</p>
                <ul>
                    <li>SQL注入 (SQLi)</li>
                    <li>跨站脚本攻击 (XSS)</li>
                    <li>跨站请求伪造 (CSRF)</li>
                    <li>不安全的身份验证</li>
                    <li>敏感数据暴露</li>
                </ul>
                
                <h3>使用指南</h3>
                <p>为了获得最佳学习体验，建议按照以下步骤使用本靶场：</p>
                <ol>
                    <li>注册一个新账号</li>
                    <li>探索各个页面功能</li>
                    <li>尝试使用各种工具和技术发现漏洞</li>
                    <li>记录你的发现和学习心得</li>
                    <li>在留言板分享你的经验</li>
                </ol>
                
                <h3>免责声明</h3>
                <p>本靶场仅供学习和教育目的。请勿将在此学到的技能用于非法用途或未经授权的系统测试。使用本靶场，即表示您同意只在合法和道德的范围内应用这些知识。</p>
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
