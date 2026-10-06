<?php
require_once 'config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($method === 'GET') {
    $rows = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, id DESC")->fetchAll();
    jsonOut($rows);
}

if ($method === 'POST') {
    $in = getInput();
    $stmt = $pdo->prepare("INSERT INTO gallery (title, label, type, src, size, sort_order) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM gallery")->fetchColumn();
    $stmt->execute([
        $in['title'] ?? '',
        $in['label'] ?? '',
        $in['type'] ?? 'image',
        $in['src'] ?? '',
        $in['size'] ?? 'normal',
        $max + 1
    ]);
    jsonOut(['success' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'PUT') {
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $in = getInput();
    $stmt = $pdo->prepare("UPDATE gallery SET title=?, label=?, type=?, src=?, size=? WHERE id=?");
    $stmt->execute([
        $in['title'] ?? '',
        $in['label'] ?? '',
        $in['type'] ?? 'image',
        $in['src'] ?? '',
        $in['size'] ?? 'normal',
        $id
    ]);
    jsonOut(['success' => true]);
}

if ($method === 'DELETE') {
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id=?");
    $stmt->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'طريقة غير مدعومة'], 405);