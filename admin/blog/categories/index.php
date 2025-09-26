<?php
require_once __DIR__.'/../../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../../includes/functions.php';

$conn = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$pagination = getPagination('blog_categories', $page, $perPage);

// Get categories
$sql = "SELECT * FROM blog_categories ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $pagination['offset'], $pagination['perPage']);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Get post counts for each category
$postCounts = [];
if (!empty($categories)) {
    $categoryIds = array_column($categories, 'id');
    $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
    $countSql = "SELECT category_id, COUNT(*) as post_count FROM blog_posts WHERE category_id IN ($placeholders) GROUP BY category_id";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param(str_repeat('i', count($categoryIds)), ...$categoryIds);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    
    while ($row = $countResult->fetch_assoc()) {
        $postCounts[$row['category_id']] = $row['post_count'];
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $categoryId = (int)$_GET['delete'];
    
    // Check if category has posts
    $checkSql = "SELECT COUNT(*) as post_count FROM blog_posts WHERE category_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('i', $categoryId);
    $checkStmt->execute();
    $postCount = $checkStmt->get_result()->fetch_assoc()['post_count'];
    
    if ($postCount > 0) {
        $_SESSION['error'] = 'Cannot delete category that has blog posts!';
    } else {
        $deleteSql = "DELETE FROM blog_categories WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param('i', $categoryId);
        
        if ($deleteStmt->execute()) {
            $_SESSION['success'] = 'Category deleted successfully!';
        } else {
            $_SESSION['error'] = 'Error deleting category!';
        }
    }
    
    header('Location: index.php');
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Categories - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-folder"></i> Blog Categories</h1>
            <p>Manage your blog categories</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="data-table">
            <div class="table-header">
                <h3>All Blog Categories</h3>
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Category
                </a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Posts</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No categories found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo isset($postCounts[$category['id']]) && $postCounts[$category['id']] > 0 ? 'active' : 'inactive'; ?>">
                                            <?php echo isset($postCounts[$category['id']]) ? $postCounts[$category['id']] : 0; ?> posts
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $category['status'] === 'active' ? 'active' : 'inactive'; ?>">
                                            <?php echo ucfirst($category['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="index.php?delete=<?php echo $category['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($pagination['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>