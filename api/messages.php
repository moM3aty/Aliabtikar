<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($method === 'GET') {
    // قراءة الرسائل تتطلب تسجيل دخول (لوحة التحكم)
    requireAuth();
    $rows = $pdo->query("SELECT * FROM messages ORDER BY id DESC")->fetchAll();
    jsonOut($rows);
}

if ($method === 'POST') {
    // إرسال رسالة من الموقع — لا يحتاج تسجيل دخول
    $in = getInput();
    if (empty($in['name']) || empty($in['phone']) || empty($in['text'])) {
        jsonOut(['error' => 'كل الحقول مطلوبة'], 400);
    }
    $stmt = $pdo->prepare("INSERT INTO messages (name, phone, text, date, is_read) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([
        $in['name'],
        $in['phone'],
        $in['text'],
        date('Y-m-d')
    ]);
    jsonOut(['success' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'PUT') {
    requireAuth();
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $in = getInput();
    $stmt = $pdo->prepare("UPDATE messages SET is_read=? WHERE id=?");
    $stmt->execute([(int)($in['is_read'] ?? 0), $id]);
    jsonOut(['success' => true]);
}

if ($method === 'DELETE') {
    requireAuth();
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id=?");
    $stmt->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'طريقة غير مدعومة'], 405);