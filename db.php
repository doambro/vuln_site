<?php
// 数据库配置
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'pentest_lab';

// 连接数据库
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die('数据库连接失败: ' . $conn->connect_error);
}
?>
