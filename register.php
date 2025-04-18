<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 - 渗透测试靶场</title>
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
                <a href="about.php">关于我们</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">个人中心</a>
                    <a href="logout.php">退出登录</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">登录</a>
                    <a href="register.php" class="active btn-register">注册</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <main>
            <section>
                <h2>用户注册</h2>
                <p>创建一个新账号以访问所有功能</p>
                
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="username">用户名：</label>
                        <input type="text" id="username" name="username" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="password">密码：</label>
                        <input type="password" id="password" name="password" required />
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" value="注册" />
                    </div>
                    
                    <p style="text-align: center;">已有账号？<a href="login.php">点击登录</a></p>
                    
                    <?php
                    // 处理注册逻辑
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        include 'db.php';
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        
                        // 检查用户名是否已存在（仍然保留SQL注入漏洞）
                        $check_sql = "SELECT * FROM users WHERE username='$username'";
                        $check_result = $conn->query($check_sql);
                        
                        if ($check_result->num_rows > 0) {
                            echo "<p style='text-align:center; color:red;'>注册失败：用户名 '$username' 已存在！</p>";
                        } else {
                            // 插入用户（存在SQL注入漏洞）
                            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
                            if ($conn->query($sql) === TRUE) {
                                echo "<p style='text-align:center; color:green;'>注册成功！</p>";
                            } else {
                                echo "<p style='text-align:center; color:red;'>注册失败：" . $conn->error . "</p>";
                            }
                        }
                        $conn->close();
                    }
                    ?>
                </form>
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
