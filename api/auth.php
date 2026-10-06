<?php
/**
 * مصادقة لوحة التحكم
 * POST   → تسجيل دخول
 * GET    → التحقق من الجلسة
 * DELETE → تسجيل خروج
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = getInput();
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if ($username === ADMIN_USER && password_verify($password, ADMIN_PASS_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['login_time'] = time();

        jsonOut([
            'success' => true,
            'user' => $username,
            'message' => 'تم تسجيل الدخول بنجاح'
        ]);
    } else {
        jsonOut(['error' => 'بيانات الدخول غير صحيحة'], 401);
    }
}

if ($method === 'GET') {
    if (!empty($_SESSION['admin_logged_in'])) {
        jsonOut([
            'logged_in' => true,
            'user' => $_SESSION['admin_user'] ?? 'admin'
        ]);
    } else {
        jsonOut(['logged_in' => false], 401);
    }
}

if ($method === 'DELETE') {
    session_destroy();
    jsonOut(['success' => true, 'message' => 'تم تسجيل الخروج']);
}

jsonOut(['error' => 'طريقة غير مدعومة'], 405);