-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Store hashed password
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user' -- No 'candidate' here
);

-- Candidates table (Separate from users)
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Store hashed password
    image VARCHAR(255) DEFAULT NULL, -- Store image filename
    votes INT DEFAULT 0
);
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id),
    UNIQUE (user_id) -- Ensures a user can vote only once
);
-- Insert default admin user (Use hashed password)
INSERT INTO users (username, password, role) 
VALUES ('admin', '$2y$10$ZMaFykEC0DQQJvCiUaOYjeIhldfjqYBSSHgxukt5r1Ba4fXijfLkS', 'admin');

-- Insert sample candidates with hashed passwords
INSERT INTO candidates (username, password, image) 
VALUES 
('candidate1', '$2y$10$HashedPasswordHere', 'candidate1.jpg'),

