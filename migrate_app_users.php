<?php
// Migration script: add password_hash column, populate from plaintext password, then (optionally) drop plaintext.
// Run once via browser or CLI: php migrate_app_users.php
require_once __DIR__ . '/config.php';

try {
    // 1. Add password_hash column if not exists
    $pdo->exec("ALTER TABLE app_users ADD COLUMN password_hash VARCHAR(255) NULL AFTER password");
} catch (Throwable $e) {
    // Ignore if already exists
}

// 2. Select users missing password_hash
$stmt = $pdo->query("SELECT id, password FROM app_users WHERE password_hash IS NULL OR password_hash = ''");
$toUpdate = $stmt->fetchAll();

if ($toUpdate) {
    $upd = $pdo->prepare("UPDATE app_users SET password_hash = ? WHERE id = ?");
    foreach ($toUpdate as $row) {
        $hash = password_hash($row['password'], PASSWORD_DEFAULT);
        $upd->execute([$hash, $row['id']]);
    }
}

// 3. Make column NOT NULL
try {
    $pdo->exec("ALTER TABLE app_users MODIFY password_hash VARCHAR(255) NOT NULL");
} catch (Throwable $e) {}

echo 'Migration done. Users updated: ' . count($toUpdate) . '<br />';
echo 'Review and then (optional) remove plaintext column with:<br />ALTER TABLE app_users DROP COLUMN password;';
?>
