<?php
// 启动会话并检查登录状态
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// 获取用户信息（数字ID越权漏洞）
$user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

// 检查查询结果
if (!$result || $result->num_rows === 0) {
    header("Location: index.php");
    exit;
}
$user = $result->fetch_assoc();

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 处理头像上传（存在文件上传漏洞）
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        
        // 如果目录不存在则创建
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $target_file = $target_dir . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file);
        $avatar = basename($_FILES['avatar']['name']);
        $conn->query("UPDATE users SET avatar = '$avatar' WHERE id = $user_id");
    }

    // 更新简介（存在SQL注入漏洞）
    if (isset($_POST['bio'])) {
        $bio = $_POST['bio'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $conn->query("UPDATE users SET bio = '$bio', is_admin = $is_admin WHERE id = $user_id");
    }

    // 修改密码（存在SQL注入漏洞）
    if (!empty($_POST['new_password'])) {
        $new_password = $_POST['new_password'];
        $conn->query("UPDATE users SET password = '$new_password' WHERE id = $user_id");
        echo "<script>alert('密码已更新！')</script>";
    }

    // 刷新用户数据
    $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
    $user = $result->fetch_assoc();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人中心 - 渗透测试靶场</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-info {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        .avatar-section {
            flex: 0 0 200px;
        }
        .bio-section {
            flex: 1;
        }
        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #3498db;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="is_admin"]').checked = true;
});
</script>
</head>
<body>
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
        <a href="profile.php" class="active">个人中心</a>
<?php if (isset($user['is_admin']) && $user['is_admin']): ?>
            <a href="admin.php" class="admin-btn">管理后台</a>
        <?php endif; ?>
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
                <h2>个人资料</h2>
                <div class="profile-info">
                    <div class="avatar-section">
                        <h3>头像</h3>
                        <img src="uploads/<?php echo isset($user['avatar']) && $user['avatar'] ? $user['avatar'] : 'default.jpg'; ?>" alt="头像" class="avatar" onerror="this.src='uploads/default.jpg'">
                        <form method="post" enctype="multipart/form-data">
                            <input type="file" name="avatar" accept="image/*">
                            <button type="submit">更新头像</button>
                        </form>
                    </div>
                    <div class="bio-section">
                        <h3>基本信息</h3>
                        <form method="post">
                            <div class="form-group">
                                <label>用户名：</label>
                            <input type="text" name="username" value="<?php echo isset($_GET['username']) ? $_GET['username'] : $user['username']; ?>">
                            <?php if(isset($_GET['id'])) echo "<small style='color:red'>(UID: $user_id)</small>"; ?>
                            </div>
                            <!-- 垂直越权漏洞 -->
                            <div class="form-group" style="display:none">
                                <label>
                                    <input type="checkbox" name="is_admin" value="1" <?php echo isset($user['is_admin']) && $user['is_admin'] ? 'checked' : '' ?>> 
                                    启用高级权限
                                </label>
                            </div>
                            <div class="form-group">
                                <label>简介：</label>
                                <textarea name="bio" rows="4"><?php echo isset($user['bio']) ? $user['bio'] : ''; ?></textarea>
                            </div>
                            <button type="submit">保存信息</button>
                        </form>
                    </div>
                </div>

                <h3>修改密码</h3>
                <form method="post">
                    <div class="form-group">
                        <label>新密码：</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <button type="submit">修改密码</button>
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
