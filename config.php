<?php
// Loader sederhana untuk file .env (key=value per baris, abaikan #komentar)
function loadEnv($path){
    if(!is_readable($path)) return;
    foreach(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line){
        if(str_starts_with(ltrim($line), '#')) continue;
        $parts = explode('=', $line, 2);
        if(count($parts) === 2){
            $k = trim($parts[0]);
            $v = trim($parts[1]);
            if($k !== '' && getenv($k) === false){
                putenv("{$k}={$v}");
                $_ENV[$k] = $v; // fallback
            }
        }
    }
}

loadEnv(__DIR__.'/.env');

// Ambil variabel dari environment atau fallback default
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'ssdlc';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (Throwable $e) {
    // Jangan bocorkan detail koneksi ke user akhir
    http_response_code(500);
    die('Database connection failed.');
}
?>

