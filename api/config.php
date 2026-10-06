<?php
/**
 * إعدادات الاتصال بقاعدة البيانات
 * عدّل القيم دي ببياناتك من Hostinger
 */

// ============ غيّر البيانات دي ============
define('DB_HOST', 'localhost');
define('DB_NAME', 'u834188565_ibtikar');      
define('DB_USER', 'u834188565_admin');        // اسم المستخدم
define('DB_PASS', 'OIkqY5M;h');      // كلمة المرور
// ==========================================

define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', password_hash('admin123', PASSWORD_DEFAULT));


define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '/uploads/');

// بدء الجلسة
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// رؤوس CORS و JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// اتصال PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'فشل الاتصال بقاعدة البيانات']);
    exit;
}

/**
 * قراءة جسم الطلب (JSON)
 */
function getInput() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * إخراج JSON مع رمز الحالة
 */
function jsonOut($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * التحقق من تسجيل الدخول
 */
function requireAuth() {
    if (empty($_SESSION['admin_logged_in'])) {
        jsonOut(['error' => 'غير مصرح بالدخول'], 401);
    }
}