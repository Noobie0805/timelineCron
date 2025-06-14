<?php

function generateVerificationCode(): string
{
  return (string) random_int(100000, 999999);
}


function sendVerificationEmail(string $email, string $code): bool
{
  $subject = 'Your Verification Code';
  $body = "<p>Your verification code is: <strong>$code</strong></p>";
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=UTF-8\r\n";
  $headers .= "From: no-reply@example.com\r\n";

  return mail($email, $subject, $body, $headers);
}



function registerEmail(string $email): bool
{
  $file = __DIR__ . '/registered_emails.txt';
  if (file_exists($file)) {
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (in_array($email, $emails, true)) {
      return false; // Email already registered
    }
  } else {
    touch($file);
  }

  file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX);
  return true;
}



function unsubscribeEmail(string $email): bool
{
  $file = __DIR__ . '/registered_emails.txt';

  $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $idx = array_search($email, $emails, true);

  if ($idx === false) {
    return false;
  }

  unset($emails[$idx]);
  return file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL, LOCK_EX) !== false;
}


function fetchGitHubTimeline()
{
  $url = 'https://api.github.com/events';
  $opts = [
    'http' => [
      'method' => 'GET',
      'header' => "User-Agent: PHP\r\nAccept: application/vnd.github+json\r\n",
      'timeout' => 10
    ]
  ];
  $json = @file_get_contents($url, false, stream_context_create($opts));

  return $json === false ? false : json_decode($json, true);
}


function formatGitHubData(array $data, string $unsubscribeUrl = "#"): string
{
  $html = '<h2>GitHub Timeline Updates</h2>';
  $html .= '<table border="1"><tr><th>Event</th><th>User</th></tr>';

  foreach (array_slice($data, 0, 20) as $event) {
    $type = htmlspecialchars($event['type'] ?? 'N/A');
    $user = htmlspecialchars($event['actor']['login'] ?? 'N/A');
    $html .= "<tr><td>$type</td><td>$user</td></tr>";
  }

  $html .= '</table>';
  $html .= '<p><a href="' . htmlspecialchars($unsubscribeUrl) . '" id="unsubscribe-button">Unsubscribe</a></p>';

  return $html;
}

function sendGitHubUpdatesToSubscribers(): void
{
  $file = __DIR__ . '/registered_emails.txt';
  if (!file_exists($file)) {
    return;
  }

  $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!$emails) {
    return;
  }

  $data = fetchGitHubTimeline();
  if ($data === false) {
    return;
  }

  foreach ($emails as $email) {
    $body = formatGitHubData($data, getUnsubscribeUrl($email));
    $subject = 'Latest GitHub Updates';
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    mail($email, $subject, $body, $headers);
  }

}

function getUnsubscribeUrl(string $email): string
{
  $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'example.com';
  $path = dirname($_SERVER['PHP_SELF'] ?? '/');

  return $scheme . '://' . $host . $path . '/unsubscribe.php?email=' . urlencode($email);
}

function sendUnsubscribeVerificationEmail(string $email, string $code): bool
{
  $subject = 'Confirm Unsubscription';
  $body = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=UTF-8\r\n";
  $headers .= "From: no-reply@example.com\r\n";

  return mail($email, $subject, $body, $headers);
}