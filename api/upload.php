<?php
/**
 * رفع الصور والفيديوهات
 * POST multipart/form-data مع حقل اسمه file
 */
require_once 'config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonOut(['error' => 'طريقة غير مدعومة'], 405);
}

if (empty($_FILES['file'])) {
    jsonOut(['error' => 'لم يتم إرسال ملف'], 400);
}

$file = $_FILES['file'];
$maxSize = 5 * 1024 * 1024; // 5MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    jsonOut(['error' => 'فشل رفع الملف (كود ' . $file['error'] . ')'], 400);
}

if ($file['size'] > $maxSize) {
    jsonOut(['error' => 'حجم الملف يتجاوز 5 ميجابايت'], 400);
}

// التحقق من النوع
$allowedImages = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$allowedVideos = ['video/mp4', 'video/webm', 'video/ogg'];
$mime = mime_content_type($file['tmp_name']);

if (!in_array($mime, array_merge($allowedImages, $allowedVideos))) {
    jsonOut(['error' => 'نوع الملف غير مسموح'], 400);
}

// امتداد الملف
$extMap = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
    'video/mp4'  => 'mp4',
    'video/webm' => 'webm',
    'video/ogg'  => 'ogv',
];
$ext = $extMap[$mime] ?? 'bin';
$filename = uniqid('media_', true) . '.' . $ext;

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

$target = UPLOAD_DIR . $filename;

if (!move_uploaded_file($file['tmp_name'], $target)) {
    jsonOut(['error' => 'فشل حفظ الملف'], 500);
}

jsonOut([
    'success' => true,
    'url' => UPLOAD_URL . $filename,
    'type' => in_array($mime, $allowedVideos) ? 'video' : 'image'
]);