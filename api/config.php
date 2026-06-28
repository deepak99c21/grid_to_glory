<?php
// Grid to Glory / Sales Quarter Dashboard - PHP + MySQL configuration
// For XAMPP default MySQL, usually: localhost, root, empty password.
// For Hostinger, replace these values with the database details from hPanel.

define('DB_HOST', 'mysql-34398923-deepakcell82-5599.l.aivencloud.com');
define('DB_PORT', '12095');
define('DB_NAME', 'grid_to_glory');
define('DB_USER', 'avnadmin');
define('DB_PASS', 'AVNS_Mk_w3CKq8jGpvrNSnIE');

// SSL / TLS settings for connecting to cloud MySQL/MariaDB
// Path to CA certificate used to verify the server certificate. Change
// this to the full path of your CA file on the host running the app.
define('DB_USE_SSL', true);
define('DB_SSL_CA', 'ca.pem');
// If your cloud provider requires client certs, set these as well (optional)
define('DB_SSL_CERT', '');
define('DB_SSL_KEY', '');

// Admin password/key used on Admin Login screen.
define('ADMIN_SETUP_KEY', 'admin1234#');
define('ADMIN_MIN_KEY_LENGTH', 8);

// Keep this long and private before final online deployment.
define('TOKEN_SECRET', 'change-this-to-a-long-random-secret-before-hostinger-deployment');

// Upload protection
define('UPLOAD_MAX_MB', 5);
define('APP_ENV', 'php_mysql_xampp_hostinger');

/**
 * Returns a configured PDO instance using config constants.
 * If DB_USE_SSL is true and CA path is provided, PDO will be configured
 * to use the CA certificate for an encrypted connection.
 */
function get_db_pdo()
{
	$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	];

	if (defined('DB_USE_SSL') && DB_USE_SSL) {
		// PDO MySQL SSL attributes (only set if non-empty)
		if (!empty(DB_SSL_CA)) {
			$options[PDO::MYSQL_ATTR_SSL_CA] = DB_SSL_CA;
		}
		if (!empty(DB_SSL_CERT)) {
			$options[PDO::MYSQL_ATTR_SSL_CERT] = DB_SSL_CERT;
		}
		if (!empty(DB_SSL_KEY)) {
			$options[PDO::MYSQL_ATTR_SSL_KEY] = DB_SSL_KEY;
		}
	}

	return new PDO($dsn, DB_USER, DB_PASS, $options);
}
?>
