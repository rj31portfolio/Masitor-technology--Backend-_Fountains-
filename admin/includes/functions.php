<?php
// File upload function
function uploadFile($file, $targetDir, $allowedTypes = null) {
    if ($allowedTypes === null) {
        $allowedTypes = ALLOWED_IMAGE_TYPES;
    }
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File size too large. Maximum allowed: 5MB'];
    }
    
    // Check file type
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'filename' => $fileName, 'filepath' => $targetFile];
    } else {
        return ['success' => false, 'error' => 'Failed to upload file'];
    }
}

// Generate slug from title
function generateSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return $slug;
}

// Check if slug exists
function slugExists($table, $slug, $excludeId = null) {
    $conn = getDBConnection();
    $sql = "SELECT id FROM $table WHERE slug = ?";
    $params = [$slug];
    
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    
    $stmt->close();
    $conn->close();
    
    return $exists;
}

// Make unique slug
function makeUniqueSlug($table, $title, $excludeId = null) {
    $slug = generateSlug($title);
    $originalSlug = $slug;
    $counter = 1;
    
    while (slugExists($table, $slug, $excludeId)) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

// Delete file
function deleteFile($filePath) {
    if (file_exists($filePath) && is_file($filePath)) {
        return unlink($filePath);
    }
    return false;
}

// Get pagination data
function getPagination($table, $page = 1, $perPage = 10, $where = '') {
    $conn = getDBConnection();
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM $table";
    if ($where) {
        $countSql .= " WHERE $where";
    }
    $countResult = $conn->query($countSql);
    $total = $countResult->fetch_assoc()['total'];
    
    // Calculate pagination
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;
    
    $conn->close();
    
    return [
        'total' => $total,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'perPage' => $perPage,
        'offset' => $offset
    ];
}
?>