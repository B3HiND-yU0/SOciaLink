-- ============================================================
-- Barkada Mini Social Network  |  social_app.sql
-- Import this file in phpMyAdmin (or: mysql -u root < social_app.sql)
-- Sample accounts: maria / juan / ana   (password for all: password123)
-- ============================================================

CREATE DATABASE IF NOT EXISTS social_app
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE social_app;

DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

-- 1. USERS -----------------------------------------------------
CREATE TABLE users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username      VARCHAR(30)  NOT NULL,
  password      VARCHAR(255) NOT NULL,           -- password_hash() output
  full_name     VARCHAR(100) NOT NULL,
  bio           VARCHAR(300) NULL,
  profile_image VARCHAR(255) NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB;

-- 2. POSTS -----------------------------------------------------
CREATE TABLE posts (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  content    TEXT         NOT NULL,
  image      VARCHAR(255) NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_posts_user (user_id),
  KEY idx_posts_created (created_at),
  CONSTRAINT fk_posts_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. COMMENTS --------------------------------------------------
CREATE TABLE comments (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id    INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  content    VARCHAR(1000) NOT NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_comments_post (post_id),
  CONSTRAINT fk_comments_post FOREIGN KEY (post_id)
    REFERENCES posts (id) ON DELETE CASCADE,
  CONSTRAINT fk_comments_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. LIKES (one like per user per post) -------------------------
CREATE TABLE likes (
  id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  reaction_type VARCHAR(10) NOT NULL DEFAULT 'like',
  PRIMARY KEY (id),
  UNIQUE KEY uq_like (post_id, user_id),
  CONSTRAINT fk_likes_post FOREIGN KEY (post_id)
    REFERENCES posts (id) ON DELETE CASCADE,
  CONSTRAINT fk_likes_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. COMMENT LIKES ---------------------------------------------
CREATE TABLE comment_likes (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  comment_id INT UNSIGNED NOT NULL,
  user_id    INT UNSIGNED NOT NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_comment_user (comment_id, user_id),
  CONSTRAINT fk_cl_comment FOREIGN KEY (comment_id)
    REFERENCES comments (id) ON DELETE CASCADE,
  CONSTRAINT fk_cl_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- SAMPLE DATA ---------------------------------------------------
INSERT INTO users (id, username, password, full_name, bio) VALUES
(1, 'maria', '$2y$12$An8gNiF28JCQ.unZ5KWM5utz3VO1xCf6mqq7qOxJRDNu2Hz79RvIu', 'Maria Santos',  'BSIT student from Nasipit. Coffee, code, and sunsets by the bay.'),
(2, 'juan',  '$2y$12$An8gNiF28JCQ.unZ5KWM5utz3VO1xCf6mqq7qOxJRDNu2Hz79RvIu', 'Juan dela Cruz', 'Learning PHP the hard way. Ask me about my bugs.'),
(3, 'ana',   '$2y$12$An8gNiF28JCQ.unZ5KWM5utz3VO1xCf6mqq7qOxJRDNu2Hz79RvIu', 'Ana Reyes',     'UI tinkerer. Dark mode forever.');

INSERT INTO posts (id, user_id, content, created_at) VALUES
(1, 1, 'Welcome to Barkada! First post on our little campus network. Say hi below.', NOW() - INTERVAL 3 DAY),
(2, 2, 'Finally got my MVC router working. Only broke the login page twice.',          NOW() - INTERVAL 2 DAY),
(3, 3, 'Hot take: every app needs a dark mode toggle. Try the moon icon on the navbar.', NOW() - INTERVAL 1 DAY),
(4, 1, 'Reminder: final project demo is 5-10 minutes. Practice your CRUD walkthrough!',  NOW() - INTERVAL 3 HOUR);

INSERT INTO comments (post_id, user_id, content, created_at) VALUES
(1, 2, 'Hi Maria! Nice to be here.',               NOW() - INTERVAL 70 HOUR),
(1, 3, 'Hello hello!',                              NOW() - INTERVAL 69 HOUR),
(2, 1, 'Twice is a good score. Mine was five.',     NOW() - INTERVAL 40 HOUR),
(3, 2, 'Moon icon is a nice touch.',                NOW() - INTERVAL 20 HOUR),
(4, 3, 'Thanks for the reminder!',                  NOW() - INTERVAL 2 HOUR);

INSERT INTO likes (post_id, user_id) VALUES
(1, 2), (1, 3), (2, 1), (2, 3), (3, 1), (4, 2);
