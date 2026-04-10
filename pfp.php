<?php
// pfp.php - Profile Picture Upload Function

function uploadProfilePicture($file, $userId, $conn) {
    
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $targetDir = "uploads/";
    
    // Create uploads folder if it doesn't exist
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            return false;
        }
    }

    // Security checks
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mimeType = mime_content_type($file['tmp_name']);

    if (!in_array($ext, $allowedExts) || !in_array($mimeType, $allowedMimes)) {
        return false;
    }

    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        return false;
    }

    // Secure unique filename
    $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $targetFile = $targetDir . $imageName;

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        
        // Update database
        $qry = $conn->prepare("UPDATE users SET image = ? WHERE id = ?");
        $qry->bind_param("si", $imageName, $userId);
        
        if ($qry->execute()) {
            // Update session so navbar shows new image immediately
            $_SESSION['image'] = $imageName;
            return $imageName;
        }
    }

    return false;
}
?>