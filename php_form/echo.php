<?php
// php_form/echo.php — the same as receive.php, but it answers with JSON (data)
// instead of an HTML page. JavaScript (fetch-demo.js) will read this answer.

$method = $_SERVER['REQUEST_METHOD'];

$name = '';
if ($method === 'GET') {
    $name = $_GET['displayName'] ?? '';
} elseif ($method === 'POST') {
    $name = $_POST['displayName'] ?? '';
}
$name = trim($name);

// Only this part is new:
header('Content-Type: application/json');   // "my answer is JSON, not HTML"
echo json_encode([                          // PHP array -> JSON text
    'method'      => $method,
    'displayName' => $name
]);
