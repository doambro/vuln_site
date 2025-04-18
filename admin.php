<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
require_once 'db.php';
session_start();

// 未授权访问漏洞（移除了管理员权限验证）
if (false) { // 原权限验证已被注释
    header("Location: login.php");
    exit();
}

// 分页设置
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// 获取用户总数（排除已删除）
$totalQuery = "SELECT COUNT(*) FROM users WHERE deleted_at IS NULL";
$totalResult = $conn->query($totalQuery);
if ($totalResult === false) {
    error_log("数据库查询错误: " . $conn->error);
    $totalUsers = 0;
} elseif ($totalResult->num_rows > 0) {
    $totalUsers = $totalResult->fetch_row()[0];
    $totalResult->free();
} else {
    $totalUsers = 0;
}
$totalPages = ceil($totalUsers / $perPage);

// 获取用户列表
$query = "SELECT id, username, email, avatar, bio, is_admin, status, created_at 
          FROM users 
          WHERE deleted_at IS NULL 
          ORDER BY created_at DESC 
          LIMIT $perPage OFFSET $offset";
$result = $conn->query($query);

// 处理用户操作
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_status'])) {
        $userId = (int)$_POST['user_id'];
        $newStatus = $_POST['current_status'] == 1 ? 0 : 1;
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $newStatus, $userId);
        $stmt->execute();
    } 
    
    if (isset($_POST['delete_user'])) {
        $userId = (int)$_POST['user_id'];
        $stmt = $conn->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
    
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>渗透测试靶场 - 管理后台</title>
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
                <a href="admin.php" class="active">用户管理</a>
                <a href="profile.php">个人中心</a>
                <a href="logout.php">退出登录</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <main>
            <section>
                <h2>用户列表</h2>
                <div class="table-responsive">
                    <table>
                <thead>
                    <tr class="table-header">
                        <th>用户名</th>
                        <th>邮箱</th>
                        <th>管理员</th>
                        <th>状态</th>
                        <th>注册时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $user['is_admin'] ? '是' : '否' ?></td>
                        <td><?= $user['status'] ? '正常' : '禁用' ?></td>
                        <td><?= (new DateTime($user['created_at']))->format('Y-m-d H:i') ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="current_status" value="<?= $user['status'] ?>">
                                <button type="submit" name="toggle_status" class="btn-login">
                                    <?= $user['status'] ? '禁用' : '启用' ?>
                                </button>
                            </form>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('确定要删除该用户？');">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <button type="submit" name="delete_user" class="btn-register delete-btn">删除</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
                <div class="pagination">
                    <?php if ($totalPages > 1): ?>
                        <?php if ($page > 1): ?>
                            <a href="admin.php?page=<?= $page - 1 ?>">« 上一页</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="admin.php?page=<?= $i ?>"<?= $i == $page ? ' class="active"' : '' ?>><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="admin.php?page=<?= $page + 1 ?>">下一页 »</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <div class="container">
            &copy; 2025 渗透测试靶场 | 仅供教育和学习目的
        </div>
    </footer>
</body>
</html>
