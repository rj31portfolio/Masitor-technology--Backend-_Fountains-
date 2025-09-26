<?php
require_once __DIR__.'/../../../inc/config.php';
requireAdminAuth();
require_once __DIR__.'/../../includes/functions.php';

$conn = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$pagination = getPagination('blog_posts', $page, $perPage);

// Get posts with category names
$sql = "SELECT p.*, c.name as category_name 
        FROM blog_posts p 
        LEFT JOIN blog_categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $pagination['offset'], $pagination['perPage']);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);

// Delete post
if (isset($_GET['delete'])) {
    $postId = (int)$_GET['delete'];
    
    // Get post data
    $getSql = "SELECT feature_image FROM blog_posts WHERE id = ?";
    $getStmt = $conn->prepare($getSql);
    $getStmt->bind_param('i', $postId);
    $getStmt->execute();
    $post = $getStmt->get_result()->fetch_assoc();
    
    // Delete feature image
    if ($post['feature_image']) {
        deleteFile('uploads/blog/' . $post['feature_image']);
    }
    
    // Delete from database
    $deleteSql = "DELETE FROM blog_posts WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param('i', $postId);
    
    if ($deleteStmt->execute()) {
        $_SESSION['success'] = 'Blog post deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting blog post!';
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
    <title>Blog Posts - MASITOR TECHNOLOGY Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_ASSETS_URL; ?>css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <?php include '../../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-blog"></i> Blog Posts</h1>
            <p>Manage your blog posts</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="data-table">
            <div class="table-header">
                <h3>All Blog Posts</h3>
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Post
                </a>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No posts found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <?php if ($post['feature_image']): ?>
                                            <img src="<?php echo ADMIN_URL; ?>blog/posts/uploads/blog/<?php echo $post['feature_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo $post['category_name'] ? htmlspecialchars($post['category_name']) : 'Uncategorized'; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $post['status'] === 'published' ? 'active' : 'inactive'; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($post['published_at']): ?>
                                            <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                                        <?php else: ?>
                                            <span class="status-badge inactive">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="index.php?delete=<?php echo $post['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this blog post?')">
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