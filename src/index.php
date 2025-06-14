<?php
session_start();
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if (isset($_POST['submit_email'])) {
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $otp = generateVerificationCode();
            $_SESSION['veri_code'] = $otp;
            $_SESSION['email'] = $email;

            $message = sendVerificationEmail($email, $otp)
                ? 'Verification code sent!'
                : 'Failed to send email…';
        } else {
            $message = 'Enter a valid email address.';
        }
    }
    if (isset($_POST['submit_verification'])) {
        $code = $_POST['verification_code'] ?? '';

        if (
            isset($_SESSION['veri_code'], $_SESSION['email']) &&
            hash_equals($_SESSION['veri_code'], $code) &&
            hash_equals($_SESSION['email'], $email)
        ) {
            $message = registerEmail($email)
                ? 'Email registered!'
                : 'Failed / already subscribed.';
            unset($_SESSION['veri_code'], $_SESSION['email']);
        } else {
            $message = 'Failed to verify code…';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Git-Timeline Subscribe</title>
</head>

<body>
    <h2>Git-Timeline Subscribe</h2>
    <form method="POST" novalidate>
        <label>
            Email:
            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>
        </label>
        <button type="submit" name="submit_email" id="submit-email" <?= isset($_SESSION['veri_code']) ? 'disabled' : ''; ?>>Send Verification Code</button>
        <br>

        <label>
            Verification Code:
            <input type="text" name="verification_code" maxlength="6" <?= isset($_SESSION['veri_code']) ? '' : 'disabled'; ?> required>
        </label>
        <button type="submit" name="submit_verification" id="submit-verification" <?= isset($_SESSION['veri_code']) ? '' : 'disabled'; ?>>Verify & Subscribe</button>
        <br>

    </form>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>
</body>

</html>