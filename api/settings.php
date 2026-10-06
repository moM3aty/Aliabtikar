<?php
require_once 'config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $row = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
    if (!$row) {
        // لو مفيش صف، نعمل واحد
        $pdo->exec("INSERT INTO settings (site_name) VALUES ('الابتكار')");
        $row = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
    }
    jsonOut($row);
}

if ($method === 'PUT' || $method === 'POST') {
    $in = getInput();
    $stmt = $pdo->prepare("UPDATE settings SET 
        site_name = ?, tagline = ?, phone = ?, 
        whatsapp = ?, email = ?, address = ?, hours = ? 
        WHERE id = 1");
    $stmt->execute([
        $in['site_name'] ?? '',
        $in['tagline'] ?? '',
        $in['phone'] ?? '',
        $in['whatsapp'] ?? '',
        $in['email'] ?? '',
        $in['address'] ?? '',
        $in['hours'] ?? '',
    ]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'طريقة غير مدعومة'], 405);