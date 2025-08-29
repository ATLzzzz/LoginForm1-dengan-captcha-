<?php

// Get keys (bisa override via .env: RECAPTCHA_SITE_KEY & RECAPTCHA_SECRET_KEY)
$publicKey = getenv('RECAPTCHA_SITE_KEY') ?: '6LejlLYrAAAAAPps3GG9xxOqLq0T98RSnT6xS8Yc';
$secretKey = getenv('RECAPTCHA_SECRET_KEY') ?: '6LejlLYrAAAAAN_55I0Gidlouw4sSA7LH0g6d38W';

// Initialize message holder to prevent undefined variable notices
$msg = '';

// Load DB connection
require_once __DIR__ . '/config.php';

if (!empty($_POST['g-recaptcha-response']) && !empty($_POST['username']) && !empty($_POST['password'])) {
    $captchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($_POST['g-recaptcha-response']);
    $captchaRaw = @file_get_contents($captchaVerifyUrl);
    $captchaData = $captchaRaw ? json_decode($captchaRaw, true) : null;

    if (!empty($captchaData['success'])) {
        // Authenticate against database
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare('SELECT id, username, password_hash, password FROM app_users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            $isValid = false;
            if ($user) {
                if (!empty($user['password_hash'])) {
                    $isValid = password_verify($password, $user['password_hash']);
                }
                // Transitional fallback: if hash missing or verify failed, try plaintext (will be removed later)
                if (!$isValid && isset($user['password']) && $user['password'] !== null && $user['password'] !== '') {
                    // Use timing-safe compare
                    $isValid = hash_equals($user['password'], $password);
                }
            }
            if ($isValid) {
                // (Simple session start could be added) For now just include success page
                include('success.php');
                exit();
            } else {
                $msg = 'Username and/or password incorrect. Try again!';
            }
        } catch (Throwable $e) {
            // Avoid leaking details
            $msg = 'Login unavailable right now.';
        }
    } else {
        // Provide more detailed messages for debugging specific reCAPTCHA issues
        $errorMap = [
            'missing-input-secret' => 'Server misconfiguration: secret key is missing.',
            'invalid-input-secret' => 'Server misconfiguration: secret key is invalid.',
            'missing-input-response' => 'Please solve the reCAPTCHA.',
            'invalid-input-response' => 'The reCAPTCHA response is invalid or expired. Refresh and try again.',
            'bad-request' => 'Bad request to reCAPTCHA verification endpoint.',
            'timeout-or-duplicate' => 'reCAPTCHA expired or already used. Please try again.',
            'invalid-keys' => 'Invalid key type: ensure you generated a reCAPTCHA v2 Checkbox key pair.'
        ];

        if (isset($captchaData['error-codes']) && is_array($captchaData['error-codes']) && count($captchaData['error-codes'])) {
            $detailed = [];
            foreach ($captchaData['error-codes'] as $code) {
                $detailed[] = $errorMap[$code] ?? ('reCAPTCHA error: ' . htmlspecialchars($code));
            }
            $msg = implode('<br />', array_unique($detailed));
        } else {
            $msg = 'The reCAPTCHA is incorrect. Try again!';
        }
    }
} else if (!empty($_POST)) {
    if (empty($_POST['g-recaptcha-response'])) {
        $msg .= 'Please solve the reCAPTCHA<br />';
    }
    // Fix: second condition should check username
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $msg .= 'Username and password required';
    }
}

include('form.php');

?>