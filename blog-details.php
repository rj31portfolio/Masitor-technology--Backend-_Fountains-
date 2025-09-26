<?php
require_once __DIR__."/inc/config.php";
require_once __DIR__."/inc/head.php";

// Get slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (!$slug) {
    header("Location: blogs.php");
    exit;
}

$conn = getDBConnection();

// Fetch blog post by slug
$stmt = $conn->prepare("SELECT bp.*, bc.name AS category_name, bc.slug AS category_slug 
                        FROM blog_posts bp 
                        LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                        WHERE bp.slug = ? AND bp.status = 'published' LIMIT 1");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if (!$blog) {
    // Blog not found
    header("Location: blogs.php");
    exit;
}

// Fetch recent posts (latest 3)
$recentPosts = [];
$recentResult = $conn->query("SELECT title, slug, feature_image, published_at 
                              FROM blog_posts 
                              WHERE status='published' AND slug != '".$conn->real_escape_string($slug)."' 
                              ORDER BY published_at DESC LIMIT 3");
if ($recentResult) {
    while ($row = $recentResult->fetch_assoc()) {
        $recentPosts[] = $row;
    }
}

// Fetch all blog categories
$categories = [];
$catResult = $conn->query("SELECT id, name, slug FROM blog_categories WHERE status='active' ORDER BY name ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}
$conn->close();
?>

<!-- HEADER ================================================== -->
<?php require_once __DIR__."/inc/header.php"; ?>

<!-- Breadcrumb Section -->
<section class="breadcrumb-section d-flex align-items-center text-center text-white" 
         style="background: url('assets/img/banner/about-banner.png') center/cover no-repeat; height: 250px; position: relative;">
  <div style="background: rgba(0,0,0,0.6); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
  <div class="container position-relative">
    <h1 class="display-5 fw-bold mb-2 text-white"><?php echo htmlspecialchars($blog['title']); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="blogs.php" class="text-white text-decoration-none">Blogs</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($blog['title']); ?></li>
      </ol>
    </nav>
  </div>
</section>

<!-- BLOG DETAILS ================================================== -->
<section>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-5 mb-lg-0">
                <div class="row">
                    <div class="col-lg-12">
                        <article class="card card-style04">
                            <div class="blog-img position-relative overflow-hidden rounded-top wow fadeIn" data-wow-delay="200ms">
                                <img src="<?php echo BASE_URL; ?>admin/blog/posts/uploads/blog/<?php echo htmlspecialchars($blog['feature_image']); ?>" class="rounded-top" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            </div>
                            <div class="card-body position-relative pt-2-6 pb-1-9 pb-xl-2-6 px-1-9 px-xl-2-4">
                                <div class="wow fadeIn" data-wow-delay="200ms">
                                    <div class="post-date">
                                        <?php $date = $blog['published_at'] ? date_create($blog['published_at']) : null; ?>
                                        <?php if ($date): ?>
                                            <span class="mb-0 d-block lh-1 display-22 display-xl-17"><?php echo date_format($date, 'd'); ?></span>
                                            <span class="d-block display-31"><?php echo date_format($date, 'M'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-3">
                                        <?php if ($blog['category_name']): ?>
                                            <a href="blogs.php?category=<?php echo urlencode($blog['category_slug']); ?>" class="text-uppercase fw-bold display-31">
                                                <?php echo htmlspecialchars($blog['category_name']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <h2 class="mb-3"><?php echo htmlspecialchars($blog['title']); ?></h2>
                                    <p class="mb-0"><?php echo htmlspecialchars($blog['meta_description']); ?></p>
                                    <div class="mt-3">
                                        <?php echo $blog['content']; // Assuming content is safe HTML ?>
                                    </div>
                                </div>
                                <!-- Tags and Share (optional, if you have tags) -->
                            </div>
                        </article>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="blog-sidebar mt-n1-9 ps-xl-5">
                    <!-- Recent Posts -->
                    <div class="widget bg-secondary mt-1-9 wow fadeIn" data-wow-delay="200ms">
                        <div class="section-title-01 mb-1-9">
                            <span class="text-primary font-weight-600 text-uppercase letter-spacing-1 position-relative ps-2">Recent Posts</span>
                        </div>
                        <?php foreach ($recentPosts as $post): ?>
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0 me-3">
                                    <img src="uploads/blog/<?php echo htmlspecialchars($post['feature_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width:60px;height:60px;object-fit:cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-2 h6">
                                        <a href="blog-details.php?slug=<?php echo urlencode($post['slug']); ?>" class="text-white text-white-hover-light">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h4>
                                    <span class="text-white opacity8 small">
                                        <?php echo date('F d, Y', strtotime($post['published_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Categories -->
                    <div class="widget bg-secondary mt-1-9 wow fadeIn" data-wow-delay="200ms">
                        <div class="section-title-01 mb-1-9">
                            <span class="text-primary font-weight-600 text-uppercase letter-spacing-1 position-relative ps-2">Categories</span>
                        </div>
                        <ul class="category-list list-unstyled mb-0">
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="blogs.php?category=<?php echo urlencode($cat['slug']); ?>">
                                        <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- Follow Us -->
                    <div class="widget bg-secondary mt-1-9 wow fadeIn" data-wow-delay="200ms">
                        <div class="section-title-01 mb-1-9">
                            <span class="text-primary font-weight-600 text-uppercase letter-spacing-1 position-relative ps-2">Follow Us</span>
                        </div>
                        <ul class="social-icon-style2 ps-0">
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER ================================================== -->
<?php require_once __DIR__."/inc/footer.php"; ?>