Sales Quarter Dashboard / Grid to Glory - V25 PHP + MySQL Version

This version is converted from the working V23 design to:
- HTML
- CSS
- JavaScript
- PHP backend
- MySQL database

No Node.js, no SQLite, and no node_modules are required.

XAMPP local setup:
1. Copy this folder to: C:\xampp\htdocs\sales_quarter_dashboard_v25
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open phpMyAdmin and create a database named: grid_to_glory
   You may also import mysql_schema.sql, but the app can create the tables automatically on first run.
4. Open: http://localhost/sales_quarter_dashboard_v25/
5. Admin panel: http://localhost/sales_quarter_dashboard_v25/admin.html
6. Default admin key: admin1234#

Database settings:
Edit api/config.php if your MySQL username/password/database is different.
For default XAMPP, these are usually correct:
DB_HOST = localhost
DB_NAME = grid_to_glory
DB_USER = root
DB_PASS = empty

Hostinger setup:
1. Upload the contents of this folder to public_html or a subfolder.
2. Create a MySQL database in Hostinger hPanel.
3. Edit api/config.php with Hostinger DB_HOST, DB_NAME, DB_USER, DB_PASS.
4. Visit the site URL. The app will create the required tables automatically if the DB user has permission.
5. Change TOKEN_SECRET and ADMIN_SETUP_KEY before final public use.

Important:
- Design is kept same as V23.
- Uploaded images are stored in the uploads folder. Make sure it is writable on the server.
- This is the first PHP/MySQL test conversion version. Please test admin login, design settings, quarters, teams, members, result entry, CSV export, and image upload.
