# ✅ Task Planner with Email Verification and Unsubscribe

A powerful PHP-based task management system with:

- ✔️ Task creation, completion, and deletion  
- 📬 Email subscription with OTP verification  
- 🔁 Hourly email reminders via CRON  
- 🚫 Unsubscribe functionality  
- 🧪 Fake mail testing using Mailpit + msmtp (no real emails sent)

---

## 📁 Project Structure

```
project/
├── index.php                # Main UI
├── functions.php            # Core logic
├── unsubscribe.php          # Handle unsubscribe links
├── verify.php               # Handle OTP verification via link
├── cron.php                 # Script for CRON to send reminders
├── testmail.php             # Test script for verifying mail setup
├── project_setup.sh         # One-click project setup
├── setup_cron.sh            # Cron automation
├── tasks.txt                # JSON-stored tasks
├── subscribers.txt          # Verified email list
├── pending_subscriptions.txt# Pending emails + OTPs
```

---

## 🚀 Features

- ✅ Add, complete, and delete tasks
- 🔄 CRON-based task reminder emails
- 🔐 Email verification via 6-digit OTP
- 💌 Mail delivered only to Mailpit inbox (local dev)
- ❌ One-click unsubscribe support

---

## 💡 How Email Works

1. User submits their email in the **subscription form**
2. A 6-digit OTP is generated and emailed (via Mailpit)
3. User either:
   - Enters OTP manually in the UI to verify  
   - Or clicks the **link in the email** (`verify.php`)
4. Once verified, they start receiving hourly task reminder emails
5. They can opt out anytime via:
   - Form on the UI
   - Or via the `unsubscribe.php` link in email

---

## 🛠️ Installation Instructions (Kali Linux)

### 1. Clone the Project

```bash
git clone https://github.com/yourname/task-planner.git
cd task-planner
```

### 2. Install Mailpit (Fake SMTP)

```bash
wget https://github.com/axllent/mailpit/releases/latest/download/mailpit-linux-amd64 -O mailpit
chmod +x mailpit
sudo mv mailpit /usr/local/bin/
```

### 3. Start Mailpit in New Terminal

```bash
mailpit
# Web Inbox → http://localhost:8025
# SMTP      → 127.0.0.1:1025
```

### 4. Install msmtp

```bash
sudo apt update
sudo apt install msmtp
```

### 5. Configure msmtp

```bash
nano ~/.msmtprc
```

Paste:

```
defaults
auth           off
tls            off
logfile        ~/.msmtp.log

account        local
host           127.0.0.1
port           1025
from           no-reply@example.com

account default : local
```

Then set permissions:

```bash
chmod 600 ~/.msmtprc
```

### 6. Connect PHP to msmtp

Edit your php.ini:

```bash
php --ini  # Locate loaded config
nano /etc/php/*/cli/php.ini  # or relevant path
```

Find `[mail function]` section and add:

```
sendmail_path = /usr/bin/msmtp -t
```

### 7. Run the Project

```bash
php -S localhost:8000
```
Visit → [http://localhost:8000](http://localhost:8000)

---

### ⚙️ Auto Setup (Optional)

Run all required setup and cron registration in one command:

```bash
./project_setup.sh
```

### ⏰ Enable CRON Job

This sends hourly reminder emails to all verified users.

```bash
./setup_cron.sh
```

---

### 🧪 Test Email from PHP

Create `testmail.php`:

```php
<?php
$to = 'test@example.com';
$subject = 'Mail Test';
$message = '<b>This is a test email</b>';
$headers = "From: no-reply@example.com\r\nContent-type: text/html\r\n";
mail($to, $subject, $message, $headers);
?>
```

Run:

```bash
php testmail.php
```

Check Mailpit inbox → [http://localhost:8025](http://localhost:8025)

---

## 🔗 Example Email Links

- **Email verification:**  
  `http://localhost:8000/verify.php?email=you@example.com&code=123456`

- **Unsubscribe:**  
  `http://localhost:8000/unsubscribe.php?email=you@example.com`

---

## 🧑‍🔬 Troubleshooting

| Issue                       | Fix                                                          |
|-----------------------------|--------------------------------------------------------------|
| Emails not in Mailpit       | Restart Mailpit, check `~/.msmtp.log`                        |
| msmtp not sending           | Confirm correct config in `.msmtprc`                         |
| PHP mail() silently failing | Ensure `sendmail_path` is configured and permissions correct |
| CRON not running            | Run `crontab -l` to check if cron job is listed              |

---

## 🙌 Credits

- [Mailpit](https://github.com/axllent/mailpit) — Lightweight dev SMTP server  
- [msmtp](https://marlam.de/msmtp/) — Sendmail replacement  
- PHP 8.2+

---

🎉 **You're All Set!**  
Open your browser → [http://localhost:8000](http://localhost:8000)  
And manage your tasks like a pro!
