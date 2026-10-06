<?php
require_once 'config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($method === 'GET') {
    $rows = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id DESC")->fetchAll();
    jsonOut($rows);
}

if ($method === 'POST') {
    $in = getInput();
    $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM testimonials")->fetchColumn();
    $stmt = $pdo->prepare("INSERT INTO testimonials (name, text, stars, sort_order) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $in['name'] ?? '',
        $in['text'] ?? '',
        (int)($in['stars'] ?? 5),
        $max + 1
    ]);
    jsonOut(['success' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'PUT') {
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $in = getInput();
    $stmt = $pdo->prepare("UPDATE testimonials SET name=?, text=?, stars=? WHERE id=?");
    $stmt->execute([
        $in['name'] ?? '',
        $in['text'] ?? '',
        (int)($in['stars'] ?? 5),
        $id
    ]);
    jsonOut(['success' => true]);
}

if ($method === 'DELETE') {
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id=?");
    $stmt->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'طريقة غير مدعومة'], 405);