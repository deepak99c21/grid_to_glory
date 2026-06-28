<?php
require_once __DIR__ . '/config.php';

header('X-Content-Type-Options: nosniff');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Vary: Origin');
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$CATEGORY_DEFS = json_decode(<<<'JSON'
[
  {
    "category": "Press Release with Clients",
    "points": 100,
    "details": "Collaborate with clients to feature them in a press release, showcasing their partnership and mutual achievements.",
    "bonus": "10 points for doing more than 1 advocacy asset at a time"
  },
  {
    "category": "Client testimonial / case study",
    "points": 80,
    "details": "Named client testimonial or case study published publicly – Infosys or external site (e.g. WSJ, FT etc.)",
    "bonus": "10 points for doing more than 1 advocacy asset at a time"
  },
  {
    "category": "Quarterly results quote or Feature Note with client or Strategic Partner",
    "points": 50,
    "details": "Quotes from clients, which get published during quarterly results (IFRS document) on our website OR a Feature Note on our website.",
    "bonus": "10 points for C-level execs"
  },
  {
    "category": "Client Speaker at Events",
    "points": 50,
    "details": "Client speaker at event. Bonus can be earned if event is public, involves endorsement and exec is C-level.",
    "bonus": "10 points for endorsing Infosys + 10 points for public event + 10 points for C-level speaker"
  },
  {
    "category": "Thought Leadership with Clients",
    "points": 50,
    "details": "Externally published thought leadership content with clients e.g. Fireside chat, Interview, POV, etc.",
    "bonus": "10 points for endorsement, 10 points for C-level exec, 10 points for endorsement mentioning Topaz, Cobalt or Aster name"
  },
  {
    "category": "Client Attendee/meeting at Events",
    "points": 30,
    "details": "Secure client attendance or meetings at events.",
    "bonus": "10 points for C-level execs"
  },
  {
    "category": "Analyst or deal advisor references by clients",
    "points": 30,
    "details": "Named client testimonial or case study published publicly – Infosys or external site (e.g. WSJ, FT etc.)",
    "bonus": "10 points for C-level execs"
  },
  {
    "category": "MSA clause for Marketing",
    "points": 30,
    "details": "Signed MSA with Marketing clause to do public advocacy (new deal signing or renewal).",
    "bonus": "10 points if commitment on number of assets per year"
  },
  {
    "category": "MSD opportunity Tagging",
    "points": 2,
    "details": "Points awarded on opportunity tagged: 2 points - $1 Mn to $5 Mn; 5 points - $5 Mn to $20 Mn; 10 points - $20 Mn+ to $50 Mn; 20 points - Above $50 Mn.",
    "bonus": "Bonus points for MKTG originated tagging: 2 points - $1 Mn to $5 Mn; 5 points - $5 Mn to $20 Mn; 10 points - $20 Mn+ to $50 Mn; 20 points - Above $50 Mn"
  },
  {
    "category": "Social Media Tagging Clients & Endorsing Infosys",
    "points": 20,
    "details": "Encourage clients to mention Infosys on social media platforms. Bonus for client tagging Infosys.",
    "bonus": "10 points if clients posts from their handle, endorsing Infosys; 10 points for endorsing Aster, Topaz, Cobalt"
  },
  {
    "category": "Insurance Advisory Council-Membership",
    "points": 50,
    "details": "Points are earned by MCOs when a new member is added in the council.",
    "bonus": "NA"
  },
  {
    "category": "Social Media Tagging Strategic Partners & Endorsing Infosys",
    "points": 10,
    "details": "Encourage clients to mention Infosys on social media platforms. Bonus for client tagging Infosys.",
    "bonus": "10 points if partners posts from their handle, endorsing Infosys"
  }
]
JSON, true);
$SEED_DATA = json_decode(<<<'JSON'
{
  "quarters": [
    {
      "id": 1,
      "code": "Q1",
      "title": "Q1",
      "is_unlocked": 1,
      "background_image": ""
    },
    {
      "id": 2,
      "code": "Q2",
      "title": "Q2",
      "is_unlocked": 0,
      "background_image": ""
    },
    {
      "id": 3,
      "code": "Q3",
      "title": "Q3",
      "is_unlocked": 0,
      "background_image": ""
    },
    {
      "id": 4,
      "code": "Q4",
      "title": "Q4",
      "is_unlocked": 0,
      "background_image": ""
    }
  ],
  "teams": [
    {
      "id": 1,
      "name": "TEAM RAJESH",
      "leader_name": "Rajesh",
      "leader_image": "uploads/asset_1781786721920_353c8059.png",
      "sort_order": 1,
      "subtitle": "Mercedes",
      "card_color": "#00b8b0",
      "icon_image": "",
      "leaderboard_name": "Rajesh"
    },
    {
      "id": 2,
      "name": "TEAM HEMA",
      "leader_name": "Hema",
      "leader_image": "uploads/asset_1781801487719_0de0c3a3.png",
      "sort_order": 2,
      "subtitle": "Redbull Racing",
      "card_color": "#2362ad",
      "icon_image": "",
      "leaderboard_name": "Hema"
    },
    {
      "id": 3,
      "name": "TEAM SENTHIL",
      "leader_name": "Senthil",
      "leader_image": null,
      "sort_order": 3,
      "subtitle": "Williams Racing",
      "card_color": "#1558b5",
      "icon_image": "",
      "leaderboard_name": ""
    },
    {
      "id": 4,
      "name": "TEAM ATUL",
      "leader_name": "Atul",
      "leader_image": null,
      "sort_order": 4,
      "subtitle": "Ferrari",
      "card_color": "#c9002b",
      "icon_image": "",
      "leaderboard_name": ""
    },
    {
      "id": 5,
      "name": "TEAM BK",
      "leader_name": "BK",
      "leader_image": null,
      "sort_order": 5,
      "subtitle": "Haas",
      "card_color": "#9ca3a6",
      "icon_image": "",
      "leaderboard_name": ""
    },
    {
      "id": 6,
      "name": "TEAM DEEPAK",
      "leader_name": "Deepak",
      "leader_image": null,
      "sort_order": 6,
      "subtitle": "Aston Martin",
      "card_color": "#07845f",
      "icon_image": "",
      "leaderboard_name": ""
    },
    {
      "id": 7,
      "name": "TEAM DEB",
      "leader_name": "Deb",
      "leader_image": null,
      "sort_order": 7,
      "subtitle": "McLaren",
      "card_color": "#f47b00",
      "icon_image": "",
      "leaderboard_name": ""
    }
  ],
  "team_members": [
    {
      "id": 1,
      "team_id": 1,
      "name": "Rajesh",
      "role": "Sales Lead"
    },
    {
      "id": 2,
      "team_id": 2,
      "name": "Hema",
      "role": "Sales Lead"
    },
    {
      "id": 3,
      "team_id": 3,
      "name": "Senthil",
      "role": "Sales Lead"
    },
    {
      "id": 4,
      "team_id": 4,
      "name": "Atul",
      "role": "Sales Lead"
    },
    {
      "id": 5,
      "team_id": 5,
      "name": "BK",
      "role": "Sales Lead"
    },
    {
      "id": 6,
      "team_id": 6,
      "name": "Deepak",
      "role": "Sales Lead"
    },
    {
      "id": 7,
      "team_id": 7,
      "name": "Deb",
      "role": "Sales Lead"
    }
  ],
  "results": [
    {
      "id": 1,
      "quarter_id": 1,
      "team_id": 1,
      "member_id": 1,
      "category": "Press Release with Clients",
      "points": 70.0,
      "quantity": 1.0,
      "bonus": 0.0,
      "total": 70.0
    },
    {
      "id": 2,
      "quarter_id": 1,
      "team_id": 2,
      "member_id": 2,
      "category": "Client testimonial / case study",
      "points": 80.0,
      "quantity": 1.0,
      "bonus": 10.0,
      "total": 90.0
    },
    {
      "id": 3,
      "quarter_id": 1,
      "team_id": 3,
      "member_id": 3,
      "category": "Quarterly results quote or Feature Note with client or Strategic Partner",
      "points": 90.0,
      "quantity": 1.0,
      "bonus": 0.0,
      "total": 90.0
    },
    {
      "id": 4,
      "quarter_id": 1,
      "team_id": 4,
      "member_id": 4,
      "category": "Client Speaker at Events",
      "points": 50.0,
      "quantity": 1.0,
      "bonus": 10.0,
      "total": 60.0
    }
  ],
  "admin_settings": [
    {
      "key": "admin_key_hash",
      "value": "8f1076eb0208c5292668dbd0a16fa0f7b03f3af9aed7eeca1c50cde0adafd91f"
    },
    {
      "key": "home_title",
      "value": "GRID TO\nGLORY"
    },
    {
      "key": "home_tagline",
      "value": "A GAMIFIED FRIENDLY COMPETITION TO #DRIVEIMPACT"
    },
    {
      "key": "leaderboard_date",
      "value": "AS OF JUNE 26"
    },
    {
      "key": "hero_image",
      "value": ""
    },
    {
      "key": "team_page_subtitle",
      "value": "CHOOSE A NAME FOR YOUR TEAM, CHOOSE A TEAM COLOR"
    },
    {
      "key": "button_gradient_start",
      "value": "#e91e63"
    },
    {
      "key": "button_gradient_end",
      "value": "#00aeea"
    },
    {
      "key": "button_opacity",
      "value": "1"
    },
    {
      "key": "button_radius",
      "value": "8"
    },
    {
      "key": "leader_gradient_start",
      "value": "#229ee9"
    },
    {
      "key": "leader_gradient_end",
      "value": "#f80059"
    },
    {
      "key": "leader_row_opacity",
      "value": "0.08"
    },
    {
      "key": "leader_table_scale",
      "value": "100"
    },
    {
      "key": "quarter_card_width",
      "value": "150"
    },
    {
      "key": "quarter_card_height",
      "value": "132"
    },
    {
      "key": "quarter_card_opacity",
      "value": "0.70"
    },
    {
      "key": "q1_x",
      "value": "5.2"
    },
    {
      "key": "q1_y",
      "value": "76"
    },
    {
      "key": "q2_x",
      "value": "17"
    },
    {
      "key": "q2_y",
      "value": "76"
    },
    {
      "key": "q3_x",
      "value": "28.8"
    },
    {
      "key": "q3_y",
      "value": "76"
    },
    {
      "key": "q4_x",
      "value": "40.6"
    },
    {
      "key": "q4_y",
      "value": "76"
    },
    {
      "key": "admin_button_bg",
      "value": "#ffffff"
    },
    {
      "key": "admin_button_text",
      "value": "#111111"
    },
    {
      "key": "admin_button_top",
      "value": "18"
    },
    {
      "key": "admin_button_right",
      "value": "22"
    },
    {
      "key": "admin_button_width",
      "value": "92"
    },
    {
      "key": "admin_button_height",
      "value": "38"
    },
    {
      "key": "admin_button_font_size",
      "value": "13"
    },
    {
      "key": "home_button_bg",
      "value": "#ffffff"
    },
    {
      "key": "home_button_text",
      "value": "#111111"
    },
    {
      "key": "home_button_x",
      "value": "0"
    },
    {
      "key": "home_button_y",
      "value": "0"
    },
    {
      "key": "home_button_width",
      "value": "92"
    },
    {
      "key": "home_button_height",
      "value": "38"
    },
    {
      "key": "home_button_font_size",
      "value": "13"
    },
    {
      "key": "logout_button_bg",
      "value": "#ffe8ee"
    },
    {
      "key": "logout_button_text",
      "value": "#b0002f"
    },
    {
      "key": "logout_button_x",
      "value": "0"
    },
    {
      "key": "logout_button_y",
      "value": "0"
    },
    {
      "key": "logout_button_width",
      "value": "105"
    },
    {
      "key": "logout_button_height",
      "value": "38"
    },
    {
      "key": "logout_button_font_size",
      "value": "13"
    },
    {
      "key": "back_button_bg",
      "value": "#ffffff"
    },
    {
      "key": "back_button_text",
      "value": "#111111"
    },
    {
      "key": "back_button_top",
      "value": "18"
    },
    {
      "key": "back_button_right",
      "value": "7.5"
    },
    {
      "key": "back_button_width",
      "value": "92"
    },
    {
      "key": "back_button_height",
      "value": "38"
    },
    {
      "key": "back_button_font_size",
      "value": "13"
    }
  ]
}
JSON, true);

function pdo() {
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}
function json_response($data, $status = 200) { http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }
function fail($msg, $status = 400) { json_response(['error' => $msg], $status); }
function body_json() { $raw = file_get_contents('php://input'); $d = json_decode($raw ?: '{}', true); return is_array($d) ? $d : []; }
function q($sql, $params = []) { $st = pdo()->prepare($sql); $st->execute($params); return $st; }
function all($sql, $params = []) { return q($sql, $params)->fetchAll(); }
function one($sql, $params = []) { $r = q($sql, $params)->fetch(); return $r ?: null; }
function exec_sql($sql) { pdo()->exec($sql); }
function setting_object() { $rows = all('SELECT `key`, `value` FROM admin_settings'); $o = []; foreach ($rows as $r) $o[$r['key']] = $r['value']; return $o; }
function set_default_setting($key, $value) { if (!one('SELECT `value` FROM admin_settings WHERE `key`=?', [$key])) q('INSERT INTO admin_settings(`key`,`value`) VALUES (?,?)', [$key, $value]); }
function hash_key($key) { return hash('sha256', (string)$key); }
function b64url($s) { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }
function make_token() { $payload = json_encode(['role'=>'admin', 'exp'=>time()+8*60*60]); $b64 = b64url($payload); $sig = b64url(hash_hmac('sha256', $b64, TOKEN_SECRET, true)); return $b64 . '.' . $sig; }
function verify_token($token) { if (!$token || strpos($token, '.') === false) return false; [$b64, $sig] = explode('.', $token, 2); $expected = b64url(hash_hmac('sha256', $b64, TOKEN_SECRET, true)); if (!hash_equals($expected, $sig)) return false; $json = base64_decode(strtr($b64, '-_', '+/')); $p = json_decode($json, true); return is_array($p) && ($p['role'] ?? '') === 'admin' && ($p['exp'] ?? 0) > time(); }
function require_admin() { $h = $_SERVER['HTTP_AUTHORIZATION'] ?? ''; if (!$h && function_exists('apache_request_headers')) { $headers = apache_request_headers(); $h = $headers['Authorization'] ?? $headers['authorization'] ?? ''; } $token = stripos($h, 'Bearer ') === 0 ? substr($h, 7) : ''; if (!verify_token($token)) fail('Admin login required', 401); }
function audit($action, $details='') { try { q('INSERT INTO audit_logs(action,details) VALUES (?,?)', [$action, substr((string)$details,0,1000)]); } catch (Throwable $e) {} }
function calc_result($b) { $points = floatval($b['points'] ?? 0); $qty = floatval($b['quantity'] ?? 0); $bonus = floatval($b['bonus'] ?? 0); return round(($points * $qty) + $bonus, 2); }
function csv_escape($v) { $s = (string)($v ?? ''); return preg_match('/[",

]/', $s) ? '"' . str_replace('"', '""', $s) . '"' : $s; }
function upload_dir() { $dir = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads'; if (!is_dir($dir)) mkdir($dir, 0775, true); return $dir; }
function save_upload($field) {
    if (!isset($_FILES[$field]) || !is_uploaded_file($_FILES[$field]['tmp_name'])) return '';
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) fail('Upload failed.');
    if ($_FILES[$field]['size'] > UPLOAD_MAX_MB * 1024 * 1024) fail('Upload too large. Maximum allowed size is ' . UPLOAD_MAX_MB . ' MB.');
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png','jpg','jpeg','webp'], true)) fail('Only PNG, JPG, JPEG and WEBP image files are allowed.');
    $finfo = finfo_open(FILEINFO_MIME_TYPE); $mime = finfo_file($finfo, $_FILES[$field]['tmp_name']); finfo_close($finfo);
    if (!in_array($mime, ['image/png','image/jpeg','image/webp'], true)) fail('Only PNG, JPG, JPEG and WEBP image files are allowed.');
    $name = 'asset_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = upload_dir() . DIRECTORY_SEPARATOR . $name;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) fail('Could not save uploaded file. Check uploads folder permission.');
    return 'uploads/' . $name;
}
function init_db() {
    global $SEED_DATA;
    exec_sql("CREATE TABLE IF NOT EXISTS quarters (id INT PRIMARY KEY, code VARCHAR(20) UNIQUE NOT NULL, title VARCHAR(255) NOT NULL, is_unlocked TINYINT DEFAULT 0, background_image VARCHAR(500) DEFAULT '') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    exec_sql("CREATE TABLE IF NOT EXISTS teams (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, leader_name VARCHAR(255) NOT NULL, leader_image VARCHAR(500) DEFAULT '', sort_order INT DEFAULT 0, subtitle VARCHAR(255) DEFAULT '', card_color VARCHAR(50) DEFAULT '#00b8b0', icon_image VARCHAR(500) DEFAULT '', leaderboard_name VARCHAR(255) DEFAULT '') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    exec_sql("CREATE TABLE IF NOT EXISTS team_members (id INT AUTO_INCREMENT PRIMARY KEY, team_id INT NOT NULL, name VARCHAR(255) NOT NULL, role VARCHAR(255) DEFAULT '', INDEX(team_id), CONSTRAINT fk_members_team FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    exec_sql("CREATE TABLE IF NOT EXISTS results (id INT AUTO_INCREMENT PRIMARY KEY, quarter_id INT NOT NULL, team_id INT NOT NULL, member_id INT NOT NULL, category VARCHAR(500) DEFAULT '', points DECIMAL(12,2) DEFAULT 0, quantity DECIMAL(12,2) DEFAULT 0, bonus DECIMAL(12,2) DEFAULT 0, total DECIMAL(12,2) DEFAULT 0, UNIQUE KEY uq_result (quarter_id, member_id, category), INDEX(team_id), INDEX(member_id), CONSTRAINT fk_results_quarter FOREIGN KEY(quarter_id) REFERENCES quarters(id), CONSTRAINT fk_results_team FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE, CONSTRAINT fk_results_member FOREIGN KEY(member_id) REFERENCES team_members(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    exec_sql("CREATE TABLE IF NOT EXISTS admin_settings (`key` VARCHAR(100) PRIMARY KEY, `value` TEXT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    exec_sql("CREATE TABLE IF NOT EXISTS audit_logs (id INT AUTO_INCREMENT PRIMARY KEY, action VARCHAR(255) NOT NULL, details TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $qCount = one('SELECT COUNT(*) c FROM quarters')['c'] ?? 0;
    if (!$qCount) { foreach ($SEED_DATA['quarters'] as $r) q('INSERT INTO quarters(id,code,title,is_unlocked,background_image) VALUES (?,?,?,?,?)', [$r['id'],$r['code'],$r['title'],$r['is_unlocked'],$r['background_image'] ?? '']); }
    $tCount = one('SELECT COUNT(*) c FROM teams')['c'] ?? 0;
    if (!$tCount) { foreach ($SEED_DATA['teams'] as $r) q('INSERT INTO teams(id,name,leader_name,leader_image,sort_order,subtitle,card_color,icon_image,leaderboard_name) VALUES (?,?,?,?,?,?,?,?,?)', [$r['id'],$r['name'],$r['leader_name'],$r['leader_image'] ?? '',$r['sort_order'] ?? 0,$r['subtitle'] ?? '',$r['card_color'] ?? '#00b8b0',$r['icon_image'] ?? '',$r['leaderboard_name'] ?? '']); }
    $mCount = one('SELECT COUNT(*) c FROM team_members')['c'] ?? 0;
    if (!$mCount) { foreach ($SEED_DATA['team_members'] as $r) q('INSERT INTO team_members(id,team_id,name,role) VALUES (?,?,?,?)', [$r['id'],$r['team_id'],$r['name'],$r['role'] ?? '']); }
    $rCount = one('SELECT COUNT(*) c FROM results')['c'] ?? 0;
    if (!$rCount) { foreach ($SEED_DATA['results'] as $r) q('INSERT INTO results(id,quarter_id,team_id,member_id,category,points,quantity,bonus,total) VALUES (?,?,?,?,?,?,?,?,?)', [$r['id'],$r['quarter_id'],$r['team_id'],$r['member_id'],$r['category'] ?? '',$r['points'] ?? 0,$r['quantity'] ?? 0,$r['bonus'] ?? 0,$r['total'] ?? 0]); }
    $sCount = one('SELECT COUNT(*) c FROM admin_settings')['c'] ?? 0;
    if (!$sCount) { foreach ($SEED_DATA['admin_settings'] as $r) q('INSERT INTO admin_settings(`key`,`value`) VALUES (?,?)', [$r['key'],$r['value']]); }
    $envHash = hash_key(ADMIN_SETUP_KEY);
    // Use parameterized queries to avoid SQL mode / quoting issues (e.g. ANSI_QUOTES)
    $admin = one('SELECT `value` FROM admin_settings WHERE `key`=?', ['admin_key_hash']);
    if (!$admin) q('INSERT INTO admin_settings(`key`,`value`) VALUES (?,?)', ['admin_key_hash', $envHash]);
    else if (($admin['value'] ?? '') !== $envHash) q('UPDATE admin_settings SET `value`=? WHERE `key`=?', [$envHash, 'admin_key_hash']);
}
function dump_sql() {
    $tables = ['quarters','teams','team_members','results','admin_settings','audit_logs'];
    $out = "-- Grid to Glory MySQL backup
-- Created: " . date('c') . "
SET FOREIGN_KEY_CHECKS=0;
";
    foreach ($tables as $t) { $rows = all("SELECT * FROM `$t`"); foreach ($rows as $row) { $cols = array_keys($row); $vals = array_map(fn($v)=> $v === null ? 'NULL' : pdo()->quote((string)$v), array_values($row)); $out .= "INSERT INTO `$t` (`" . implode('`,`',$cols) . "`) VALUES (" . implode(',', $vals) . ");
"; } }
    return $out . "SET FOREIGN_KEY_CHECKS=1;
";
}

try { init_db(); } catch (Throwable $e) { fail('Database connection/setup failed: ' . $e->getMessage(), 500); }

if (!empty($_GET['path'])) {
    $path = '/' . trim((string)$_GET['path'], '/');
} else {
    $path = $_SERVER['PATH_INFO'] ?? '';
    if (!$path) $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $script = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    if ($script && $script !== '/' && $script !== '\\' && strpos($path, $script) === 0) {
        $path = substr($path, strlen($script));
    }
    $path = '/' . trim($path, '/');
    if ($path !== '/api' && strpos($path, '/api/') !== 0 && preg_match('#^/(admin|public)(/|$)#', $path)) {
        $path = '/api' . $path;
    }
}
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) $method = strtoupper($_POST['_method']);

try {
    global $CATEGORY_DEFS;
    if ($path === '/api/public/settings' && $method === 'GET') json_response(setting_object());
    if ($path === '/api/public/categories' && $method === 'GET') json_response($CATEGORY_DEFS);
    if ($path === '/api/public/quarters' && $method === 'GET') json_response(all('SELECT * FROM quarters ORDER BY id'));
    if ($path === '/api/public/teams' && $method === 'GET') json_response(all('SELECT * FROM teams ORDER BY sort_order, id'));
    if (preg_match('#^/api/public/quarters/(\d+)/teams$#', $path, $m) && $method === 'GET') { $qid=$m[1]; $q=one('SELECT * FROM quarters WHERE id=?',[$qid]); if(!$q || !$q['is_unlocked']) fail('This quarter is locked',403); $teams=all('SELECT t.*, COALESCE(ROUND(SUM(r.total),2),0) AS team_total FROM teams t LEFT JOIN results r ON r.team_id=t.id AND r.quarter_id=? GROUP BY t.id ORDER BY t.sort_order,t.id',[$qid]); json_response(['quarter'=>$q,'teams'=>$teams]); }
    if (preg_match('#^/api/public/quarters/(\d+)/teams/(\d+)/results$#', $path, $m) && $method === 'GET') { $qid=$m[1]; $tid=$m[2]; $q=one('SELECT * FROM quarters WHERE id=?',[$qid]); if(!$q || !$q['is_unlocked']) fail('This quarter is locked',403); $team=one('SELECT * FROM teams WHERE id=?',[$tid]); if(!$team) fail('Team not found',404); $rows=all("SELECT m.id AS member_id,m.name,m.role,COALESCE(r.category,'') category,COALESCE(r.points,0) points,COALESCE(r.quantity,0) quantity,COALESCE(r.bonus,0) bonus,COALESCE(r.total,0) total FROM team_members m LEFT JOIN results r ON r.member_id=m.id AND r.quarter_id=? WHERE m.team_id=? ORDER BY m.id",[$qid,$tid]); $total=0; foreach($rows as $r) $total += floatval($r['total']); json_response(['quarter'=>$q,'team'=>$team,'rows'=>$rows,'teamTotal'=>round($total,2)]); }
    if (preg_match('#^/api/public/quarters/(\d+)/leaderboard$#', $path, $m) && $method === 'GET') { $qid=$m[1]; $q=one('SELECT * FROM quarters WHERE id=?',[$qid]); if(!$q || !$q['is_unlocked']) fail('This quarter is locked',403); $teams=all('SELECT * FROM teams ORDER BY sort_order,id'); $rows=all("SELECT COALESCE(r.category,'') AS category, t.id AS team_id, ROUND(SUM(COALESCE(r.total,0)),2) AS total FROM results r JOIN teams t ON t.id=r.team_id WHERE r.quarter_id=? AND COALESCE(r.category,'')<>'' GROUP BY r.category,t.id ORDER BY r.category",[$qid]); $standard=array_map(fn($c)=>$c['category'],$CATEGORY_DEFS); $extra=[]; foreach($rows as $r) if($r['category'] && !in_array($r['category'],$standard,true) && !in_array($r['category'],$extra,true)) $extra[]=$r['category']; $cats=array_merge($standard,$extra); $matrix=[]; foreach($cats as $cat) { $vals=[]; foreach($teams as $t) $vals[$t['id']]=0; foreach($rows as $rr) if($rr['category']===$cat) $vals[$rr['team_id']]=$rr['total'] ?: 0; $matrix[]=['category'=>$cat,'values'=>$vals]; } $totals=[]; foreach($teams as $t) { $sum=0; foreach($rows as $rr) if($rr['team_id']==$t['id']) $sum+=floatval($rr['total']); $totals[$t['id']]=round($sum,2); } json_response(['quarter'=>$q,'teams'=>$teams,'matrix'=>$matrix,'totals'=>$totals,'settings'=>setting_object()]); }

    if ($path === '/api/admin/login' && $method === 'POST') { $b=body_json(); $row=one('SELECT `value` FROM admin_settings WHERE `key`=?', ['admin_key_hash']); if(!$row || hash_key($b['adminKey'] ?? '') !== $row['value']) { audit('admin_login_failed'); fail('Invalid admin key',401); } audit('admin_login_success'); json_response(['token'=>make_token()]); }
    if ($path === '/api/admin/change-key' && $method === 'POST') { require_admin(); $b=body_json(); if(!($b['newKey'] ?? '') || strlen((string)$b['newKey']) < ADMIN_MIN_KEY_LENGTH) fail('New key must be at least ' . ADMIN_MIN_KEY_LENGTH . ' characters'); q('UPDATE admin_settings SET `value`=? WHERE `key`=?',[hash_key($b['newKey']), 'admin_key_hash']); audit('admin_key_changed'); json_response(['ok'=>true]); }
    if ($path === '/api/admin/dashboard' && $method === 'GET') { require_admin(); json_response(['quarters'=>all('SELECT * FROM quarters ORDER BY id'), 'teams'=>all('SELECT * FROM teams ORDER BY sort_order,id'), 'members'=>all('SELECT * FROM team_members ORDER BY team_id,id'), 'settings'=>setting_object()]); }
    if (preg_match('#^/api/admin/quarters/(\d+)$#',$path,$m) && $method === 'PUT') { require_admin(); $id=$m[1]; $cur=one('SELECT * FROM quarters WHERE id=?',[$id]); if(!$cur) fail('Quarter not found',404); $bg=save_upload('backgroundImage') ?: ($cur['background_image'] ?? ''); q('UPDATE quarters SET title=?, is_unlocked=?, background_image=? WHERE id=?',[$_POST['title'] ?? $cur['title'], (($_POST['is_unlocked'] ?? '')==='1')?1:0, $bg, $id]); json_response(['ok'=>true]); }
    if ($path === '/api/admin/settings' && $method === 'POST') { require_admin(); $pairs=$_POST; unset($pairs['_method']); foreach($pairs as $k=>$v) q('INSERT INTO admin_settings(`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)',[$k,$v]); $hero=save_upload('heroImage'); if($hero) q('INSERT INTO admin_settings(`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)',['hero_image',$hero]); audit('design_settings_saved'); json_response(['ok'=>true]); }
    if ($path === '/api/admin/teams' && $method === 'POST') { require_admin(); $leader=save_upload('leaderImage'); $icon=save_upload('iconImage'); q('INSERT INTO teams(name,leader_name,subtitle,card_color,leader_image,icon_image,leaderboard_name,sort_order) VALUES (?,?,?,?,?,?,?,?)',[$_POST['name'] ?? '', $_POST['leader_name'] ?? '', $_POST['subtitle'] ?? '', $_POST['card_color'] ?? '#00b8b0', $leader, $icon, $_POST['leaderboard_name'] ?? ($_POST['leader_name'] ?? $_POST['name'] ?? ''), intval($_POST['sort_order'] ?? 0)]); json_response(['id'=>pdo()->lastInsertId(),'ok'=>true]); }
    if (preg_match('#^/api/admin/teams/(\d+)$#',$path,$m) && $method === 'PUT') { require_admin(); $id=$m[1]; $cur=one('SELECT * FROM teams WHERE id=?',[$id]); if(!$cur) fail('Team not found',404); $leader=save_upload('leaderImage') ?: ($cur['leader_image'] ?? ''); $icon=save_upload('iconImage') ?: ($cur['icon_image'] ?? ''); q('UPDATE teams SET name=?,leader_name=?,subtitle=?,card_color=?,leader_image=?,icon_image=?,leaderboard_name=?,sort_order=? WHERE id=?',[$_POST['name'] ?? $cur['name'], $_POST['leader_name'] ?? $cur['leader_name'], $_POST['subtitle'] ?? '', $_POST['card_color'] ?? ($cur['card_color'] ?? '#00b8b0'), $leader, $icon, $_POST['leaderboard_name'] ?? ($cur['leaderboard_name'] ?? $_POST['leader_name'] ?? $_POST['name'] ?? ''), intval($_POST['sort_order'] ?? 0), $id]); json_response(['ok'=>true]); }
    if (preg_match('#^/api/admin/teams/(\d+)$#',$path,$m) && $method === 'DELETE') { require_admin(); q('DELETE FROM teams WHERE id=?',[$m[1]]); json_response(['ok'=>true]); }
    if ($path === '/api/admin/members' && $method === 'POST') { require_admin(); $b=body_json(); q('INSERT INTO team_members(team_id,name,role) VALUES (?,?,?)',[$b['team_id'] ?? 0,$b['name'] ?? '',$b['role'] ?? 'Sales Lead']); json_response(['id'=>pdo()->lastInsertId(),'ok'=>true]); }
    if (preg_match('#^/api/admin/members/(\d+)$#',$path,$m) && $method === 'PUT') { require_admin(); $b=body_json(); q('UPDATE team_members SET team_id=?,name=?,role=? WHERE id=?',[$b['team_id'] ?? 0,$b['name'] ?? '',$b['role'] ?? '',$m[1]]); json_response(['ok'=>true]); }
    if (preg_match('#^/api/admin/members/(\d+)$#',$path,$m) && $method === 'DELETE') { require_admin(); q('DELETE FROM team_members WHERE id=?',[$m[1]]); json_response(['ok'=>true]); }
    if ($path === '/api/admin/results' && $method === 'POST') { require_admin(); $b=body_json(); $member=one('SELECT * FROM team_members WHERE id=?',[$b['member_id'] ?? 0]); if(!$member) fail('Member not found',404); $total=calc_result($b); q('INSERT INTO results(quarter_id,team_id,member_id,category,points,quantity,bonus,total) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE team_id=VALUES(team_id), points=VALUES(points), quantity=VALUES(quantity), bonus=VALUES(bonus), total=VALUES(total)',[$b['quarter_id'] ?? 0,$member['team_id'],$b['member_id'] ?? 0,$b['category'] ?? '',$b['points'] ?? 0,$b['quantity'] ?? 0,$b['bonus'] ?? 0,$total]); json_response(['ok'=>true,'calc'=>['total'=>$total]]); }
    if ($path === '/api/admin/results' && $method === 'GET') { require_admin(); $rows=all("SELECT q.code AS quarter,t.name AS team_name,t.leader_name,m.name AS member_name,m.role,COALESCE(r.category,'') category,COALESCE(r.points,0) points,COALESCE(r.quantity,0) quantity,COALESCE(r.bonus,0) bonus,COALESCE(r.total,0) total FROM team_members m JOIN teams t ON t.id=m.team_id CROSS JOIN quarters q LEFT JOIN results r ON r.member_id=m.id AND r.quarter_id=q.id ORDER BY q.id,t.sort_order,t.id,m.id"); json_response(['rows'=>$rows]); }
    if ($path === '/api/admin/export/results.csv' && $method === 'GET') { require_admin(); $rows=all("SELECT q.code AS Quarter,t.name AS Team_Name,t.leader_name AS Team_Leader,m.name AS Member_Name,m.role AS Role,COALESCE(r.category,'') AS Category,COALESCE(r.points,0) AS Points,COALESCE(r.quantity,0) AS Quantity,COALESCE(r.bonus,0) AS Bonus,COALESCE(r.total,0) AS Total FROM team_members m JOIN teams t ON t.id=m.team_id CROSS JOIN quarters q LEFT JOIN results r ON r.member_id=m.id AND r.quarter_id=q.id ORDER BY q.id,t.sort_order,t.id,m.id"); $headers=['Quarter','Team_Name','Team_Leader','Member_Name','Role','Category','Points','Quantity','Bonus','Total']; $csv=implode(',', $headers)."
"; foreach($rows as $row) $csv .= implode(',', array_map(fn($h)=>csv_escape($row[$h] ?? ''), $headers))."
"; header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="sales_quarter_results.csv"'); echo "ï»¿".$csv; exit; }
    if ($path === '/api/admin/backup/database' && $method === 'GET') { require_admin(); audit('mysql_backup_downloaded'); header('Content-Type: application/sql; charset=utf-8'); header('Content-Disposition: attachment; filename="grid_to_glory_mysql_backup_'.date('Ymd_His').'.sql"'); echo dump_sql(); exit; }
    if ($path === '/api/admin/backup/full.zip' && $method === 'GET') { require_admin(); if(!class_exists('ZipArchive')) fail('ZipArchive is not enabled in this PHP installation. Use database backup instead.'); $tmp=tempnam(sys_get_temp_dir(),'gtgzip'); $zip=new ZipArchive(); $zip->open($tmp, ZipArchive::OVERWRITE); $zip->addFromString('database_backup.sql', dump_sql()); $root=realpath(__DIR__.'/..'); $up=$root.'/uploads'; if(is_dir($up)) { foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($up, FilesystemIterator::SKIP_DOTS)) as $file) { if($file->isFile()) $zip->addFile($file->getPathname(), 'uploads/'.basename($file->getPathname())); } } if(file_exists($root.'/custom-alignment.css')) $zip->addFile($root.'/custom-alignment.css','custom-alignment.css'); $zip->addFromString('BACKUP_README.txt', 'Grid to Glory PHP/MySQL backup created '.date('c')); $zip->close(); audit('full_backup_zip_downloaded'); header('Content-Type: application/zip'); header('Content-Disposition: attachment; filename="grid_to_glory_full_backup_'.date('Ymd_His').'.zip"'); readfile($tmp); unlink($tmp); exit; }
    if ($path === '/api/admin/system-info' && $method === 'GET') { require_admin(); json_response(['node_env'=>APP_ENV, 'port'=>'Apache/XAMPP', 'upload_max_mb'=>UPLOAD_MAX_MB, 'allowed_uploads'=>['png','jpg','jpeg','webp']]); }
    fail('API endpoint not found: ' . $path, 404);
} catch (Throwable $e) { fail('Server error: ' . $e->getMessage(), 500); }
?>
