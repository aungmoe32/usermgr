-- Active: 1738649270477@@127.0.0.1@5432@usermgr
-- 1. Create Tables

CREATE TABLE roles (
    id INT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE features (
    id INT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE permissions (
    id INT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    features_id INT,
    FOREIGN KEY (features_id) REFERENCES features (id)
);

CREATE TABLE roles_permission (
    id INT PRIMARY KEY,
    role_id INT,
    permission_id INT,
    FOREIGN KEY (role_id) REFERENCES roles (id),
    FOREIGN KEY (permission_id) REFERENCES permissions (id)
);

CREATE TABLE users (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles (id)
);

-- 2. Insert Reference Data

INSERT INTO
    roles (id, name)
VALUES (1, 'admin'),
    (2, 'operator'),
    (3, 'Cashier');

INSERT INTO
    features (id, name)
VALUES (1, 'user'),
    (2, 'roles'),
    (3, 'product');

INSERT INTO
    permissions (id, name, features_id)
VALUES (1, 'create', 1),
    (2, 'read', 1),
    (3, 'update', 1),
    (4, 'delete', 1),
    (5, 'create', 2),
    (6, 'read', 2),
    (7, 'update', 2),
    (8, 'delete', 2);

-- 3. Insert Mapping Data (Roles to Permissions)

-- Admin: Permissions 1-8
INSERT INTO
    roles_permission (id, role_id, permission_id)
VALUES (1, 1, 1),
    (2, 1, 2),
    (3, 1, 3),
    (4, 1, 4),
    (5, 1, 5),
    (6, 1, 6),
    (7, 1, 7),
    (8, 1, 8);

-- Operator: Mixed Permissions
INSERT INTO
    roles_permission (id, role_id, permission_id)
VALUES (9, 2, 1),
    (10, 2, 2),
    (11, 2, 3),
    (12, 2, 5),
    (13, 2, 6),
    (14, 2, 7);

-- Cashier: Limited Permissions
INSERT INTO
    roles_permission (id, role_id, permission_id)
VALUES (15, 3, 1),
    (16, 3, 2),
    (17, 3, 3);

-- 4. Insert User Data

INSERT INTO
    users (id, name, role_id)
VALUES (1, 'Kyaw Kyaw', 1),
    (2, 'Aung Aung', 2);