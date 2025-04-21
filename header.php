<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav>
    <a href="index.php">首页</a>
    <a href="about.php">关于</a>
    <a href="resources.php">资源中心</a>
    <?php if(isset($_SESSION['username'])): ?>
        <a href="logout.php">退出</a>
        <?php if($_SESSION['username'] === 'admin'): ?>
            <a href="admin.php">管理</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="login.php">登录</a>
    <?php endif; ?>
</nav>
