<?php
require_once 'config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($method === 'GET') {
    $rows = $pdo->query("SELECT * FROM stats ORDER BY sort_order ASC, id ASC")->fetchAll();
    jsonOut($rows);
}

if ($method === 'POST') {
    $in = getInput();
    $max = (int)$pdo->query("SELECT COALESCE(MAX(sort_order), 0) FROM stats")->fetchColumn();
    $stmt = $pdo->prepare("INSERT INTO stats (label, value, sort_order) VALUES (?, ?, ?)");
    $stmt->execute([$in['label'] ?? '', (int)($in['value'] ?? 0), $max + 1]);
    jsonOut(['success' => true, 'id' => $pdo->lastInsertId()]);
}

if ($method === 'PUT') {
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $in = getInput();
    $stmt = $pdo->prepare("UPDATE stats SET label=?, value=? WHERE id=?");
    $stmt->execute([$in['label'] ?? '', (int)($in['value'] ?? 0), $id]);
    jsonOut(['success' => true]);
}

if ($method === 'DELETE') {
    if (!$id) jsonOut(['error' => 'معرّف مفقود'], 400);
    $stmt = $pdo->prepare("DELETE FROM stats WHERE id=?");
    $stmt->execute([$id]);
    jsonOut(['success' => true]);
}

jsonOut(['error' => 'طريقة غير مدعومة'], 405);