<?php
session_start();
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'unsubscribe_email', FILTER_SANITIZE_EMAIL);

    if (isset($_POST['submit_unsubscribe'])) {
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $otp = generateVerificationCode();
            $_SESSION['veri_code'] = $otp;
            $_SESSION['email'] = $email;

            $message = sendUnsubscribeVerificationEmail($email, $otp)
                ? 'Verification code sent!'
                : 'Failed to send email…';
        } else {
            $message = 'Please enter a valid email address.';
        }
    }
    if (isset($_POST['verify_unsubscribe'])) {
        $code = $_POST['unsubscribe_verification_code'] ?? '';

        if (
            isset($_SESSION['veri_code'], $_SESSION['email']) &&
            hash_equals($_SESSION['veri_code'], $code) &&
            hash_equals($_SESSION['email'], $email)
        ) {
            $message = unsubscribeEmail($email)
                ? 'Unsubscribed!'
                : 'Failed to unsubscribe / not found.';
            unset($_SESSION['veri_code'], $_SESSION['email']);
        } else {
            $message = 'Failed to verify code…';
        }
    }
}
// TODO: Implement the form and logic for email subscription.

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Git-Timeline Unsubscribe</title>
</head>

<body>
    <h2>Git-Timeline Unsubscribe</h2>

    <form method="POST" novalidate>
        <label>
            Email:
            <input type="email" name="unsubscribe_email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
                required>
        </label>
        <button type="submit" name="submit_unsubscribe" id="submit-unsubscribe" <?= isset($_SESSION['veri_code']) ? 'disabled' : ''; ?>>Unsubscribe</button>
        <br>
        <label>
            Verification Code:
            <input type="text" name="unsubscribe_verification_code" maxlength="6" <?= isset($_SESSION['veri_code']) ? '' : 'disabled'; ?>required>
        </label>
        <button type="submit" name="verify_unsubscribe" id="verify-unsubscribe" <?= isset($_SESSION['veri_code']) ? '' : 'disabled'; ?>>Verify</button>
        <br>
    </form>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>
</body>

</html>