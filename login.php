<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 渗透测试靶场</title>
    <link rel="stylesheet" href="styles.css">
</head>
<script src="db_config.js"></script>
<body>
<?php
// 启动会话
session_start();

// 如果用户已登录，重定向到首页
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
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
                    <a href="login.php" class="active btn-login">登录</a>
                    <a href="register.php" class="btn-register">注册</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="container">
        <main>
            <section>
                <h2>用户登录</h2>
                <p>请输入您的账号和密码登录系统</p>
                
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="username">用户名：</label>
                        <input type="text" id="username" name="username" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="password">密码：</label>
                        <input type="password" id="password" name="password" required />
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" value="登录" />
                    </div>
                    
                    <p style="text-align: center;">没有账号？<a href="register.php">点击注册</a></p>
                    
                    <?php
                    // 处理登录逻辑
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        try {
                            include 'db.php';
                            $username = $_POST['username'];
                            $password = $_POST['password'];
                            
                            // 显示SQL语句用于调试
                            // echo "<!-- SQL: SELECT * FROM users WHERE username='$username' AND password='$password' -->";
                            
                            // 查询用户（存在SQL注入漏洞）
                            $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
                            $result = $conn->query($sql);
                            
                            if ($result === false) {
                                throw new Exception("查询失败: " . $conn->error);
                            }
                            if ($result->num_rows > 0) {
                                $user = $result->fetch_assoc();
                                // 设置会话变量
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['username'] = $user['username'];
                                $_SESSION['is_admin'] = $user['is_admin'];
                                
                                echo "<p style='text-align:center; color:green;'>登录成功！正在跳转...</p>";
                                // 使用JavaScript重定向
                                echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 1000);</script>";
                            } else {
                                echo "<p style='text-align:center; color:red;'>用户名或密码错误！</p>";
                            }
                        } catch (Exception $e) {
                            echo "<p style='text-align:center; color:red;'>登录异常：".htmlspecialchars($e->getMessage())."</p>";
                            if (isset($conn)) {
                                $conn->close();
                            }
                        }
                    }
                    ?>
                </form>
                <script>
                // 管理员账号：admin
                // 密码：0192023a7bbd73250516f069df18b500
                </script>
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
