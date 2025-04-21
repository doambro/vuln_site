// 示例删除功能逻辑（需要与后端对接）
function confirmDelete(resourceId) {
    if (confirm('确定要删除该资源吗？')) {
        // 这里需要调用后端删除接口
        fetch(`/delete.php?id=${resourceId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // 删除成功后刷新页面
            }
        });
    }
}