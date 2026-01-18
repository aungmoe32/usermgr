-- User Management System Database Schema (PostgreSQL)
-- 1. Drop existing tables if they exist (for clean setup)

DROP TABLE IF EXISTS roles_permission CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS permissions CASCADE;
DROP TABLE IF EXISTS features CASCADE;
DROP TABLE IF EXISTS roles CASCADE;

-- 2. Create Tables with proper constraints

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE features (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE permissions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    feature_id INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feature_id) REFERENCES features (id) ON DELETE CASCADE,
    UNIQUE (name, feature_id)
);

CREATE TABLE roles_permission (
    id SERIAL PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE,
    UNIQUE (role_id, permission_id)
);

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE RESTRICT
);

-- 3. Create indexes for better performance
CREATE INDEX idx_users_role_id ON users (role_id);
CREATE INDEX idx_users_name ON users (name);
CREATE INDEX idx_permissions_feature_id ON permissions (feature_id);
CREATE INDEX idx_roles_permission_role_id ON roles_permission (role_id);
CREATE INDEX idx_roles_permission_permission_id ON roles_permission (permission_id);

-- 3. Insert Reference Data

INSERT INTO roles (name) VALUES 
    ('admin'),
    ('operator'), 
    ('cashier');

INSERT INTO features (name, description) VALUES 
    ('user', 'User management functionality'),
    ('roles', 'Role and permission management'),
    ('product', 'Product management functionality');

INSERT INTO permissions (name, feature_id, description) VALUES 
    ('create', 1, 'Create new users'),
    ('read', 1, 'View user information'),
    ('update', 1, 'Edit user information'),
    ('delete', 1, 'Delete users'),
    ('create', 2, 'Create new roles'),
    ('read', 2, 'View roles and permissions'),
    ('update', 2, 'Edit roles and permissions'),
    ('delete', 2, 'Delete roles'),
    ('create', 3, 'Create new products'),
    ('read', 3, 'View products'),
    ('update', 3, 'Edit products'),
    ('delete', 3, 'Delete products');

-- 4. Insert Role-Permission Mappings

-- Admin: Full permissions (all features)
INSERT INTO roles_permission (role_id, permission_id) VALUES 
    (1, 1), (1, 2), (1, 3), (1, 4),  -- User permissions
    (1, 5), (1, 6), (1, 7), (1, 8),  -- Role permissions
    (1, 9), (1, 10), (1, 11), (1, 12); -- Product permissions

-- Operator: User and product management, read-only roles
INSERT INTO roles_permission (role_id, permission_id) VALUES 
    (2, 1), (2, 2), (2, 3),          -- User: create, read, update
    (2, 6),                          -- Roles: read only
    (2, 9), (2, 10), (2, 11), (2, 12); -- Product: full access

-- Cashier: Limited to user read and product read
INSERT INTO roles_permission (role_id, permission_id) VALUES 
    (3, 2),                          -- User: read only
    (3, 10);                         -- Product: read only

-- 5. Insert Sample User Data

INSERT INTO users (name, role_id) VALUES 
    ('Kyaw Kyaw', 1),    -- Admin user
    ('Aung Aung', 2),    -- Operator user
    ('Ma Ma', 3);        -- Cashier user