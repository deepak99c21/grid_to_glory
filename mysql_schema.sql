CREATE DATABASE IF NOT EXISTS grid_to_glory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE grid_to_glory;

CREATE TABLE IF NOT EXISTS quarters (
  id INT PRIMARY KEY,
  code VARCHAR(20) UNIQUE NOT NULL,
  title VARCHAR(255) NOT NULL,
  is_unlocked TINYINT DEFAULT 0,
  background_image VARCHAR(500) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS teams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  leader_name VARCHAR(255) NOT NULL,
  leader_image VARCHAR(500) DEFAULT '',
  sort_order INT DEFAULT 0,
  subtitle VARCHAR(255) DEFAULT '',
  card_color VARCHAR(50) DEFAULT '#00b8b0',
  icon_image VARCHAR(500) DEFAULT '',
  leaderboard_name VARCHAR(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS team_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  team_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  role VARCHAR(255) DEFAULT '',
  INDEX(team_id),
  CONSTRAINT fk_members_team FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  quarter_id INT NOT NULL,
  team_id INT NOT NULL,
  member_id INT NOT NULL,
  category VARCHAR(500) DEFAULT '',
  points DECIMAL(12,2) DEFAULT 0,
  quantity DECIMAL(12,2) DEFAULT 0,
  bonus DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  UNIQUE KEY uq_result (quarter_id, member_id, category),
  INDEX(team_id),
  INDEX(member_id),
  CONSTRAINT fk_results_quarter FOREIGN KEY(quarter_id) REFERENCES quarters(id),
  CONSTRAINT fk_results_team FOREIGN KEY(team_id) REFERENCES teams(id) ON DELETE CASCADE,
  CONSTRAINT fk_results_member FOREIGN KEY(member_id) REFERENCES team_members(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_settings (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  action VARCHAR(255) NOT NULL,
  details TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
