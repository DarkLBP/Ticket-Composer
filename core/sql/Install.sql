CREATE TABLE departments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  surname VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(64) NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY email (email)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE users_departments (
  userId INT,
  departmentId INT,
  PRIMARY KEY (userId, departmentId),
  FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (departmentId) REFERENCES departments(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE users_sessions (
  id VARCHAR(64) NOT NULL,
  userId INT NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id, userId),
  FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE users_validation (
  id VARCHAR(64) NOT NULL,
  userId INT NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id, userId),
  FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE tickets_statuses (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE tickets (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(100) NOT NULL,
  createdBy INT NOT NULL,
  department INT,
  asignedTo INT,
  open BIT(1) NOT NULL DEFAULT 1,
  status INT,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (createdBy) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (department) REFERENCES departments(id) ON DELETE SET NULL,
  FOREIGN KEY (asignedTo) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (status) REFERENCES tickets_statuses(id) ON DELETE SET NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE tickets_posts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  ticketId INT NOT NULL,
  userId INT NOT NULL,
  content TEXT NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  modified TIMESTAMP NULL,
  FOREIGN KEY (ticketId) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE tickets_attachments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  postId INT NOT NULL,
  filePath VARCHAR(100) NOT NULL,
  uploaded TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (postId) REFERENCES tickets_posts(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;