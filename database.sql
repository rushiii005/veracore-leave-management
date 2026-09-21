CREATE DATABASE IF NOT EXISTS veracore_leave CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE veracore_leave;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('employee','author') NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS leaves (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  reason VARCHAR(500) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  decided_at DATETIME NULL,
  CONSTRAINT fk_leave_employee FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS leave_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  leave_id INT NOT NULL,
  author_id INT NOT NULL,
  action ENUM('Approved','Rejected') NOT NULL,
  note VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_history_leave FOREIGN KEY (leave_id) REFERENCES leaves(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Demo accounts. Password for both: password123
INSERT INTO users(name,email,password_hash,role)
SELECT 'Rushi Employee','employee@veracore.com','$2y$12$5u3tZ9RNQhRFtko7lTuVbe5DFuTbulgLfWUd64z7RzyTtyr58XVpm','employee'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='employee@veracore.com');

INSERT INTO users(name,email,password_hash,role)
SELECT 'VERACORE Author','author@veracore.com','$2y$12$5u3tZ9RNQhRFtko7lTuVbe5DFuTbulgLfWUd64z7RzyTtyr58XVpm','author'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='author@veracore.com');
