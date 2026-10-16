-- ============================================================
-- setup.sql - SQL Injection Lab (Level 1) database setup
-- ============================================================
-- Run automatically by the MySQL container on its FIRST start
-- (mounted into /docker-entrypoint-initdb.d/).
-- To re-run it, reset the lab:  docker compose down -v && docker compose up -d
-- ============================================================

CREATE DATABASE IF NOT EXISTS sqli_lab
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sqli_lab;

-- ------------------------------------------------------------
-- products: the table the search page legitimately queries.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100)  NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

INSERT INTO products (name, price) VALUES
    ('Wireless Mouse',              19.99),
    ('Mechanical Keyboard',         59.50),
    ('USB-C Hub',                   24.00),
    ('27-inch Monitor',            189.99),
    ('Laptop Stand',                29.95),
    ('Noise-Cancelling Headphones', 89.00);

-- ------------------------------------------------------------
-- users: "secret" data the page never shows on purpose.
-- Students extract it with UNION-based injection.
-- It has 3 columns to match products (id, name, price) so a
-- UNION SELECT lines up without extra padding.
-- Passwords are stored in plain text deliberately - never do
-- this in real applications.
-- ------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50)  NOT NULL,
    password VARCHAR(100) NOT NULL
);

INSERT INTO users (username, password) VALUES
    ('admin',   'SuperSecret123'),
    ('alice',   'alice2024!'),
    ('bob',     'qwerty'),
    ('guest',   'guestpass');
