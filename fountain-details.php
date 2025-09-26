<?php
require_once __DIR__."/inc/config.php";
require_once __DIR__."/inc/head.php";

// Get fountain slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (!$slug) {
    header("Location: category.php");
    exit;
}

$conn = getDBConnection();

// Fetch fountain details
$stmt = $conn->prepare("SELECT f.*, c.name AS category_name, c.slug AS category_slug 
                        FROM fountains f 
                        LEFT JOIN categories c ON f.category_id = c.id 
                        WHERE f.slug = ? AND f.status = 'active' LIMIT 1");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$fountain = $result->fetch_assoc();

if (!$fountain) {
    header("Location: category.php");
    exit;
}

// Fetch gallery images
$gallery = [];
$imgStmt = $conn->prepare("SELECT image_path FROM fountain_images WHERE fountain_id = ? ORDER BY sort_order ASC, id ASC");
$imgStmt->bind_param('i', $fountain['id']);
$imgStmt->execute();
$imgResult = $imgStmt->get_result();
while ($row = $imgResult->fetch_assoc()) {
    $gallery[] = $row['image_path'];
}
$conn->close();

// Always put feature image at the start of gallery
array_unshift($gallery, $fountain['feature_image']);

// Extract YouTube video ID if link exists
$youtubeEmbed = '';
if (!empty($fountain['youtube_link'])) {
    // Support both full and short YouTube URLs
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_\-]+)/', $fountain['youtube_link'], $matches)) {
        $youtubeEmbed = $matches[1];
    }
} 
?>
<?php require_once __DIR__."/inc/header.php"; ?>

<!-- Breadcrumb Section -->
<section class="breadcrumb-section d-flex align-items-center text-center text-white" 
         style="background: url('assets/img/banner/about-banner.png') center/cover no-repeat; height: 250px; position: relative;">
  <div style="background: rgba(0,0,0,0.6); position: absolute; top:0; left:0; width:100%; height:100%;"></div>
  <div class="container position-relative">
    <h1 class="display-5 fw-bold mb-2 text-white"><?php echo htmlspecialchars($fountain['title']); ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="category.php?slug=<?php echo urlencode($fountain['category_slug']); ?>" class="text-white text-decoration-none"><?php echo htmlspecialchars($fountain['category_name']); ?></a></li>
        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($fountain['title']); ?></li>
      </ol>
    </nav>
  </div>
</section>

<section>
  <div class="container py-5">
    <div class="row">
      <!-- Gallery and Feature Image -->
      <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="mb-3" id="mainMedia">
          <img id="mainImage" src="<?php echo BASE_URL; ?>admin/fountains/uploads/fountains/<?php echo htmlspecialchars($gallery[0]); ?>" class="img-fluid rounded" alt="Fountain Image" style="max-height:400px;object-fit:cover;">
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <?php foreach ($gallery as $index => $img): ?>
            <img src="<?php echo $index == 0 ? BASE_URL . 'admin/fountains/uploads/fountains/' : BASE_URL . 'admin/fountains/uploads/fountains/gallery/'; ?><?php echo htmlspecialchars($img); ?>"
                 class="img-thumbnail"
                 style="width:80px;height:80px;object-fit:cover;cursor:pointer;"
                 onclick="showMainImage('<?php echo $index == 0 ? BASE_URL . 'admin/fountains/uploads/fountains/' : BASE_URL . 'admin/fountains/uploads/fountains/gallery/'; ?><?php echo htmlspecialchars($img); ?>')">
          <?php endforeach; ?>
          <?php if ($youtubeEmbed): ?>
            <img src="https://img.youtube.com/vi/<?php echo $youtubeEmbed; ?>/hqdefault.jpg"
                 class="img-thumbnail"
                 style="width:80px;height:80px;object-fit:cover;cursor:pointer;position:relative;"
                 onclick="showMainVideo('<?php echo $youtubeEmbed; ?>')"
                 title="Play Video">
          <?php endif; ?>
        </div>
      </div>
      <!-- Details -->
      <div class="col-lg-6">
        <h2><?php echo htmlspecialchars($fountain['title']); ?></h2>
        <p class="text-muted"><?php echo htmlspecialchars($fountain['meta_description']); ?></p>
        <div class="mb-3">
          <?php echo $fountain['content']; // Assuming safe HTML ?>
        </div>
        <div class="mb-4">
          <?php if (!empty($fountain['catalog_file'])): ?>
            <a href="<?php echo BASE_URL; ?>admin/fountains/uploads/catalogs/<?php echo htmlspecialchars($fountain['catalog_file']); ?>" class=" btn-style1 me-2" target="_blank" download>
              <i class="fa fa-download"></i> Download Catalog
            </a>
          <?php endif; ?>
          <a href="enquiry.php?fountain=<?php echo urlencode($fountain['slug']); ?>" class="btn-style1">
            <i class="fa fa-envelope"></i> Enquiry
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function showMainImage(src) {
  document.getElementById('mainMedia').innerHTML =
    '<img id="mainImage" src="' + src + '" class="img-fluid rounded" alt="Fountain Image" style="max-height:400px;object-fit:contain;">';
}
function showMainVideo(vid) {
  document.getElementById('mainMedia').innerHTML =
    '<div class="ratio ratio-16x9"><iframe src="https://www.youtube.com/embed/' + vid + '?autoplay=1&mute=1&rel=0" allow="autoplay; encrypted-media" allowfullscreen style="border-radius:8px;"></iframe></div>';
}
</script>

<!-- FOOTER -->
<?php require_once __DIR__."/inc/footer.php"; ?>
