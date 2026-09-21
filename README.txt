VERACORE LEAVE MANAGEMENT PORTAL
================================

TECHNOLOGY
- PHP 8+
- MySQL/MariaDB
- HTML5/CSS3
- Vanilla JavaScript + Fetch API
- Session authentication
- AJAX polling every 3 seconds for live approval updates

SETUP IN XAMPP
1. Copy the "veracore_leave" folder to:
   C:\xampp\htdocs\

2. Start XAMPP.
   Start Apache.
   Start MySQL.

3. Open:
   http://localhost/phpmyadmin

4. Click Import and select:
   database.sql

   The SQL creates database "veracore_leave", tables and two demo users.

5. If your MySQL root account has a password, open db.php and change:
   $pass = '';

6. Open:
   http://localhost/veracore_leave/

DEMO LOGIN
Employee:
employee@veracore.com
password123

Author:
author@veracore.com
password123

WORKFLOW
1. Landing page shows VERACORE.
2. Login opens Employee / Author choices.
3. Employee signs in and submits a leave request.
4. Request starts as Pending.
5. Author signs in and sees the request in the live queue.
6. Author can Approve or Reject and add a note.
7. Employee dashboard polls the server every 3 seconds and updates the status/history.
8. Inbox shows the same approval activity and who acted.

IMPORTANT
This is a college/demo-ready implementation. For production deployment, add CSRF protection, rate limiting, HTTPS, stronger password/account management, audit controls and server-side authorization hardening.
