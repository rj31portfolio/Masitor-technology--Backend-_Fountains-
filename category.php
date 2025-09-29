<?php
require_once __DIR__."/inc/config.php";
require_once __DIR__."/inc/head.php";

// Get category slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (!$slug) {
    // Redirect or show error if no slug provided
    header("Location: index.php");
    exit;
}

$conn = getDBConnection();

// Fetch category info
$stmt = $conn->prepare("SELECT id, name FROM categories WHERE slug = ? AND status = 'active' LIMIT 1");
$stmt->bind_param('s', $slug);
$stmt->execute();
$catResult = $stmt->get_result();
$category = $catResult->fetch_assoc();

if (!$category) {
    // Category not found
    header("Location: index.php");
    exit;
}

// Fetch all fountains in this category
$fountains = [];
$fountainStmt = $conn->prepare("SELECT id, title, slug, feature_image, meta_description, mrp_price, selling_price FROM fountains WHERE category_id = ? AND status = 'active' ORDER BY id DESC");
$fountainStmt->bind_param('i', $category['id']);
$fountainStmt->execute();
$fountainResult = $fountainStmt->get_result();
while ($row = $fountainResult->fetch_assoc()) {
    $fountains[] = $row;
}

$conn->close();
?>
<?php require_once __DIR__."/inc/config.php"; ?>
  <?php require_once __DIR__."/inc/head.php"; ?>

<!-- HEADER
================================================== -->
  <?php require_once __DIR__."/inc/header.php"; ?>

 <!-- Breadcrumb Section -->
<section class="breadcrumb-section d-flex align-items-center text-center text-white" 
         style="background: url('assets/img/banner/about-banner.png') center/cover no-repeat; height: 250px; position: relative;">

  <!-- Overlay -->
  <div style="background: rgba(0,0,0,0.6); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
  <div class="container position-relative">
    <h1 class="display-5 fw-bold mb-2 text-white"><?php echo htmlspecialchars($category['name']); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($category['name']); ?></li>
      </ol>
    </nav>
  </div>
</section>

<section>
  <div class="container py-5">
    <div class="row">
      <?php if (empty($fountains)): ?>
        <div class="col-12">
          <p>No fountains found in this category.</p>
        </div>
      <?php else: ?>
        <?php foreach ($fountains as $fountain): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
              <img src="<?php echo BASE_URL; ?>admin/fountains/uploads/fountains/<?php echo htmlspecialchars($fountain['feature_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($fountain['title']); ?>">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($fountain['title']); ?></h5>
                <?php if (!empty($fountain['mrp_price']) || !empty($fountain['selling_price'])): ?>
                  <div class="mb-2">
                    <?php if (!empty($fountain['mrp_price'])): ?>
                      <span class="text-muted" style="text-decoration:line-through;">
                        MRP: ₹<?php echo number_format($fountain['mrp_price'], 2); ?>
                      </span>
                    <?php endif; ?>
                    <?php if (!empty($fountain['selling_price'])): ?>
                      <span class="fw-bold text-success ms-2">
                        Price: ₹<?php echo number_format($fountain['selling_price'], 2); ?>
                      </span>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                <p class="card-text"><?php echo htmlspecialchars($fountain['meta_description']); ?></p>
                <a href="fountain-details.php?slug=<?php echo urlencode($fountain['slug']); ?>" class="btn-style1 text-white btn-sm">View Details</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- FOOTER
================================================== -->
<?php require_once __DIR__."/inc/footer.php"; ?>