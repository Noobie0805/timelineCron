# GH-timeline

This project is a PHP-based email verification and subscription system for GitHub timeline updates. Users can register with their email, receive a verification code, and subscribe to receive the latest GitHub timeline events. An automated CRON job fetches the latest GitHub timeline every 5 minutes and sends formatted HTML updates to all registered users via email.

---

## 🚀 Features

- Email verification with a 6-digit code
- Subscribe to GitHub timeline updates
- Unsubscribe via secure email confirmation
- All emails sent in HTML format
- Automated CRON job for periodic updates (every 5 minutes)
- No database required—uses a flat file for email storage

---


## 📁 Project Structure

```
src/
├── cron.php
├── functions.php
├── index.php
├── unsubscribe.php
├── registered_emails.txt
├── setup_cron.sh
└── ...
```

---

## 📬 Usage

- **Subscribe:**  
  Enter your email on the main page, verify with the code sent to your inbox, and you'll start receiving GitHub timeline updates.

- **Unsubscribe:**  
  Use the unsubscribe link in any update email, verify with the code sent to your inbox, and you'll be removed from the mailing list.



