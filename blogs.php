<?php
require_once __DIR__ . "/inc/config.php";
require_once __DIR__ . "/inc/head.php";

// DB connection
$conn = getDBConnection();

// Fetch all published blog posts
$blogs = [];
$result = $conn->query("SELECT id, title, slug, meta_description, feature_image, published_at 
                        FROM blog_posts 
                        WHERE status='published' 
                        ORDER BY published_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
}

// Fetch all blog categories
$categories = [];
$catResult = $conn->query("SELECT id, name, slug 
                           FROM blog_categories 
                           WHERE status='active' 
                           ORDER BY name ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch recent posts (latest 3)
$recentPosts = [];
$recentResult = $conn->query("SELECT title, slug, feature_image, published_at 
                              FROM blog_posts 
                              WHERE status='published' 
                              ORDER BY published_at DESC 
                              LIMIT 3");
if ($recentResult) {
    while ($row = $recentResult->fetch_assoc()) {
        $recentPosts[] = $row;
    }
}
?>

<!-- HEADER ================================================== -->
<?php require_once __DIR__ . "/inc/header.php"; ?>

<!-- Breadcrumb Section -->
<section class="breadcrumb-section d-flex align-items-center text-center text-white" 
         style="background: url('assets/img/banner/about-banner.png') center/cover no-repeat; height: 250px; position: relative;">
  <div style="background: rgba(0,0,0,0.6); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
  <div class="container position-relative">
    <h1 class="display-5 fw-bold mb-2 text-white">Blogs</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page">Blogs</li>
      </ol>
    </nav>
  </div>
</section>

<!-- BLOG LIST ================================================== -->
<section>
    <div class="container">
        <div class="row mt-n2-9">
            <div class="col-lg-8 mt-2-9">
                <div class="row mt-n1-9">
                    <?php if (empty($blogs)): ?>
                        <div class="col-12 mt-1-9">
                            <p>No blog posts found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($blogs as $blog): ?>
                            <div class="col-12 mt-1-9 wow fadeIn" data-wow-delay="200ms">
                                <article class="card card-style04 h-100">
                                    <div class="blog-img position-relative overflow-hidden rounded-top">
                                        <img src="<?php echo BASE_URL; ?>admin/blog/posts/uploads/blog/<?php echo htmlspecialchars($blog['feature_image']); ?>" 
                                             class="rounded-top img-fluid" 
                                             alt="<?php echo htmlspecialchars($blog['title']); ?>">
                                    </div>
                                    <div class="card-body position-relative pt-2-6 pb-1-9 pb-xl-2-6 px-1-9 px-xl-2-4">
                                        <div class="post-date">
                                            <?php $date = $blog['published_at'] ? date_create($blog['published_at']) : null; ?>
                                            <?php if ($date): ?>
                                                <span class="mb-0 d-block lh-1 display-22 display-xl-17">
                                                    <?php echo date_format($date, 'd'); ?>
                                                </span>
                                                <span class="d-block display-31">
                                                    <?php echo date_format($date, 'M'); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="h2 mb-3">
                                            <a href="blog-details.php?slug=<?php echo urlencode($blog['slug']); ?>">
                                                <?php echo htmlspecialchars($blog['title']); ?>
                                            </a>
                                        </h3>
                                        <p class="mb-0"><?php echo htmlspecialchars($blog['meta_description']); ?></p>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4 mt-2-9">
                <div class="blog-sidebar mt-n1-9 ps-xl-5">
                    <!-- Search -->
                    <div class="widget bg-secondary mt-1-9 wow fadeInUp" data-wow-delay="200ms">
                        <div class="section-title-01 mb-1-9">
                            <span class="text-primary font-weight-600 text-uppercase letter-spacing-1 position-relative ps-2">Search</span>
                        </div>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-start border-0" placeholder="Search here...">
                            <div class="input-group-append">
                                <button class="btn-style1 border-primary rounded-end" type="button"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    <div class="widget bg-secondary mt-1-9 wow fadeInUp" data-wow-delay="200ms">
                        <div class="section-title-01 mb-1-9">
                            <span class="text-primary font-weight-600 text-uppercase letter-spacing-1 position-relative ps-2">Recent Posts</span>
                        </div>
                        <?php foreach ($recentPosts as $post): ?>
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0 me-3">
                                    <img src="<?php echo BASE_URL; ?>admin/blog/posts/uploads/blog/<?php echo htmlspecialchars($post['feature_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                         style="width:60px;height:60px;object-fit:cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="mb-2 h6">
                                        <a href="blog-details.php?slug=<?php echo urlencode($post['slug']); ?>" 
                                           class="text-white text-white-hover-light">
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
                    <div class="widget bg-secondary mt-1-9 wow fadeInUp" data-wow-delay="200ms">
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
                    <div class="widget bg-secondary mt-1-9 wow fadeInUp" data-wow-delay="200ms">
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
<?php require_once __DIR__ . "/inc/footer.php"; ?>


