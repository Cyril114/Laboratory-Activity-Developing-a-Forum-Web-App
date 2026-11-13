DROP DATABASE IF EXISTS forum_lab;
CREATE DATABASE forum_lab;
USE forum_lab;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30) NOT NULL UNIQUE,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE discussions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  discussion_id INT UNSIGNED NOT NULL,
  parent_id INT UNSIGNED NULL,
  user_id INT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (discussion_id, id),
  FOREIGN KEY (discussion_id) REFERENCES discussions(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (discussion_id, parent_id)
    REFERENCES comments(discussion_id, id)
);

INSERT INTO users (id, username, email) VALUES
(1,'Alice','alice@example.test'),
(2,'Ben','ben@example.test'),
(3,'Cara','cara@example.test'),
(4,'Dee','dee@example.test'),
(5,'Eli','eli@example.test');

INSERT INTO discussions (id, user_id, title, body) VALUES
(1, 1, 'How do I learn PHP?', 'Share your learning tips.');

INSERT INTO comments (id, discussion_id, parent_id, user_id, body) VALUES
(1,1,NULL,1,'Start with the PHP manual.'),
(2,1,1,2,'Try the examples as you read.'),
(3,1,2,3,'Build a small project too.'),
(4,1,1,4,'Which manual section first?'),
(5,1,NULL,5,'Practice SQL as well.'),
(6,1,3,2,'Start with a forum.'),
(7,1,6,5,'Add replies next.');