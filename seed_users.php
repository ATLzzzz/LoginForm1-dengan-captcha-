<?php
// One-off seeding script. Visit once then delete this file.
require_once __DIR__ . '/config.php';

// Five intentionally weak & common (DO NOT use in production) - all different
$easyPasswords = [
    '12345',      // very short numeric
    'password',   // classic weak
    'qwerty',     // keyboard pattern
    'abc123',     // simple alphanumeric
    '111111',     // repeated digits
];

// Five strong unique passwords (sample) - all different
$hardPasswords = [
    'V3ry$trongPassw0rd!2025',
    'Str0ng!Pass#Alpha',
    '9u^Hk3!zQp@1',
    '!SecurePass2025#',
    'Xy7#Lm!93$Aa',
];

$users = [
    ['alice', $easyPasswords[0]],
    ['bob', $easyPasswords[1]],
    ['charlie', $easyPasswords[2]],
    ['diana', $easyPasswords[3]],
    ['eric', $easyPasswords[4]],
    ['frank', $hardPasswords[0]],
    ['grace', $hardPasswords[1]],
    ['heidi', $hardPasswords[2]],
    ['ivan', $hardPasswords[3]],
    ['judy', $hardPasswords[4]],
];

$inserted = 0;
$skipped = 0;
$errors = 0;
$stmtSelect = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
$stmtInsert = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');

foreach ($users as [$u, $plain]) {
    $stmtSelect->execute([$u]);
    if ($stmtSelect->fetch()) {
        $skipped++;
        continue;
    }
    $hash = password_hash($plain, PASSWORD_DEFAULT);
    try {
        $stmtInsert->execute([$u, $hash]);
        $inserted++;
    } catch (Throwable $e) {
        $errors++;
    }
}

echo "Inserted: {$inserted} Skipped(existing): {$skipped} Errors: {$errors}";
echo '<br /><strong>User -> Password (for testing only):</strong><br />';
foreach ($users as [$u, $p]) {
    echo htmlspecialchars($u) . ' =&gt; ' . htmlspecialchars($p) . '<br />';
}
echo '<br /><em>Warning:</em> Easy passwords are intentionally weak for demonstration. Remove or change before production.';
echo '<br />Delete seed_users.php after successful run for security.';
?>