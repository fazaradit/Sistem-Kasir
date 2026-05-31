<?php
// Definisikan root path agar bisa di-require dari mana saja
define('ROOT_PATH', dirname(__DIR__));

// Baca file .env secara manual (tanpa library)
if (file_exists(ROOT_PATH . '/.env')) {
    $lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            [$key, $val] = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($val));
        }
    }
}

// Load koneksi database
require_once ROOT_PATH . '/config/database.php';

// Load semua Model
require_once ROOT_PATH . '/app/Models/ProdukModel.php';
require_once ROOT_PATH . '/app/Models/TransaksiModel.php';

// Load semua Controller
require_once ROOT_PATH . '/app/Controllers/KasirController.php';

// Routing Sederhana 
$page   = $_GET['page']   ?? 'kasir';
$action = $_GET['action'] ?? 'index';

// Sanitasi input routing
$page   = preg_replace('/[^a-zA-Z0-9_]/', '', $page);
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $action);

// Map page → Controller
$routes = [
    'kasir' => 'KasirController',
];

if (isset($routes[$page])) {
    $controllerName = $routes[$page];
    $controller = new $controllerName();

    // Cek apakah method action ada
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        http_response_code(404);
        echo "Action tidak ditemukan.";
    }
} else {
    http_response_code(404);
    echo "Halaman tidak ditemukan.";
}