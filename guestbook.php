<?php
// 启动会话
session_start();
// 连接数据库
include 'db.php';

// 处理留言提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查用户是否已登录
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        $message = $_POST['message'];

        // 插入留言（存在SQL注入漏洞）
        $sql = "INSERT INTO messages (user_id, username, message, create_time) VALUES ('$user_id', '$username', '$message', NOW())";
        $conn->query($sql);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言板 - 渗透测试靶场</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php">渗透测试靶场</a></h1>
            </div>
            <nav>
                <a href="index.php">首页</a>
                <a href="guestbook.php" class="active">留言板</a>
                <a href="about.php">关于我们</a>
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
                <h2>留言板</h2>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p>欢迎在下方留下您的意见和建议</p>
                    <form action="guestbook.php" method="post">
                        <div class="form-group">
                            <label for="message">留言内容：</label>
                            <textarea id="message" name="message" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <input type="submit" value="提交留言" />
                        </div>
                    </form>
                <?php else: ?>
                    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                        您需要 <a href="login.php">登录</a> 后才能发表留言。
                    </div>
                <?php endif; ?>
            </section>
            
            <section>
                <h2>留言列表</h2>
                
                <?php
                // 读取留言
                $result = $conn->query("SELECT m.*, u.avatar FROM messages m LEFT JOIN users u ON m.user_id = u.id ORDER BY m.id DESC LIMIT 20");
                if ($result !== false && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // 漏洞点：未对输出进行过滤，存在XSS
                        echo "<div class='message'>";
                        echo "<div class='username'>";
                        if (!empty($row['avatar'])) {
                            echo "<img src='uploads/" . $row['avatar'] . "' alt='头像' width='30' height='30' style='border-radius: 50%; margin-right: 10px; vertical-align: middle;' onerror=\"this.src='uploads/default.jpg'\">";
                        }
                        echo $row['username'] . "</div>";
                        echo "<div class='content'>" . $row['message'] . "</div>";
                        if (isset($row['create_time'])) {
                            echo "<div class='time'>" . $row['create_time'] . "</div>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p>暂无留言</p>";
                }
                $conn->close();
                ?>
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
