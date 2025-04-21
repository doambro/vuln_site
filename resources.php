<?php
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
require_once 'db.php';
session_start();

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$message = '';
$error = '';
$resource_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$resource = [];

if ($resource_id) {
    $stmt = $conn->prepare("SELECT id, title, url, description, category FROM resources WHERE id = ?");
    $stmt->bind_param("i", $resource_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $resource = $result->fetch_assoc();
    }
    $stmt->close();
}

// 处理表单提交逻辑
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_resource'])) {
        $resource_id = intval($_POST['resource_id']);
        $stmt = $conn->prepare("DELETE FROM resources WHERE id = ?");
        $stmt->bind_param("i", $resource_id);
        if ($stmt->execute()) {
            $message = "资源删除成功！";
            header("Location: resources.php?message=" . urlencode($message));
            exit();
        } else {
            $error = "删除失败：" . $conn->error;
        }
        $stmt->close();
    } elseif (isset($_POST['save_resource'])) {
        $id = intval($_POST['resource_id']);
        $title = trim($_POST['title']);
        $url = trim($_POST['url']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);

        if (!empty($title) && !empty($url) && !empty($description) && !empty($category)) {
            if ($id) {
                $stmt = $conn->prepare("UPDATE resources SET title=?, url=?, description=?, category=? WHERE id=?");
                $stmt->bind_param("ssssi", $title, $url, $description, $category, $id);
            } else {
                $stmt = $conn->prepare("INSERT INTO resources (title, url, description, category) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $title, $url, $description, $category);
            }
            if ($stmt->execute()) {
                $message = $id ? "资源更新成功！" : "资源添加成功！";
                header("Location: resources.php?message=" . urlencode($message));
                exit();
            } else {
                $error = ($id ? "更新" : "添加") . "失败：" . $conn->error;
            }
            $stmt->close();
        }
    }
}

if (isset($_GET['message'])) {
    $message = urldecode($_GET['message']);
}

// 获取资源数据
$resources_by_category = [];
$query = "SELECT id, title, url, description, category, created_at FROM resources ORDER BY category, created_at DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($resource = $result->fetch_assoc()) {
        $resources_by_category[$resource['category']][] = $resource;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>课程资源中心 - 渗透测试靶场</title>
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
                <a href="guestbook.php">留言板</a>
                <a href="about.php">关于我们</a>
                <a href="resources.php" class="active">资源中心</a>
                <?php if(isset($_SESSION['username'])): ?>
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
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <section class="admin-section card">
                <h2><i class="icon-<?= $resource_id ? 'edit' : 'add' ?>"></i> <?= $resource_id ? '编辑' : '添加' ?>新资源</h2>
                <form method="POST" action="resources.php" class="resource-form">
                    <input type="hidden" name="resource_id" value="<?= $resource_id ?>">
                    <input type="hidden" name="save_resource" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">资源标题</label>
                            <input type="text" id="title" name="title" value="<?= isset($resource['title']) ? $resource['title'] : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category">分类</label>
                            <input type="text" id="category" name="category" value="<?= isset($resource['category']) ? $resource['category'] : '' ?>" required placeholder="例如: 工具、教程、文章">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="url">资源链接</label>
                        <input type="url" id="url" name="url" value="<?= isset($resource['url']) ? $resource['url'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">详细描述</label>
                        <textarea id="description" name="description" required rows="4"><?= isset($resource['description']) ? $resource['description'] : '' ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $resource_id ? '保存修改' : '添加资源' ?></button>
                </form>
            </section>
        <?php endif; ?>

        <section class="resource-list">
            <h2>课程资源中心</h2>
            <p class="page-intro">这里汇集了课程学习所需的各种资料、工具和知识介绍</p>
            
            <?php if (!empty($resources_by_category)): ?>
                <?php foreach ($resources_by_category as $category => $resources): ?>
                    <div class="category-card">
                        <h2 class="category-title"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></h2>
                        <div class="resource-items">
                            <?php foreach ($resources as $resource): ?>
                                <div class="resource-item">
                                    <div class="resource-content">
                                        <h3><?= htmlspecialchars($resource['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                        <p><?= nl2br(htmlspecialchars($resource['description'], ENT_QUOTES, 'UTF-8')) ?></p>
                                        <div class="resource-meta">
                                            <a href="<?= htmlspecialchars($resource['url'], ENT_QUOTES, 'UTF-8') ?>" 
                                               target="_blank" class="btn btn-visit">
                                                <i class="icon-link"></i> 访问资源
                                            </a>
                                            <span class="resource-date">
                                                <?= (new DateTime($resource['created_at']))->format('Y-m-d') ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($is_admin): ?>
                                        <div class="resource-actions">
                                            <a href="resources.php?edit=<?= $resource['id'] ?>" class="btn btn-secondary">编辑</a>
                                            
                                            <form method="POST" action="resources.php" 
                                                  onsubmit="return confirm('确定要删除这个资源吗？');">
                                                <input type="hidden" name="resource_id" value="<?= $resource['id'] ?>">
                                                <button type="submit" name="delete_resource" class="btn btn-delete">
                                                    <i class="icon-delete"></i> 删除
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>暂无资源<?php if($is_admin) echo '，快去添加一些吧！'; ?></p>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

    <footer>
        <div class="container">
            &copy; 2025 渗透测试靶场 | 仅供学习和教育目的
        </div>
    </footer>
</body>
</html>
