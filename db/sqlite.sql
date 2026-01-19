-- SQLite Schema for User Management System
-- Converted from PostgreSQL schema.sql

-- Enable foreign key constraints in SQLite
PRAGMA foreign_keys = ON;

-- Drop tables in reverse dependency order if they exist
DROP TABLE IF EXISTS roles_permission;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS features;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

-- Create roles table
CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role_id INTEGER NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Create features table
CREATE TABLE features (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create permissions table
CREATE TABLE permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    feature_id INTEGER NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feature_id) REFERENCES features (id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Create roles_permission junction table
CREATE TABLE roles_permission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE(role_id, permission_id)
);

-- Create indexes for performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_is_active ON users(is_active);
CREATE INDEX idx_roles_name ON roles(name);
CREATE INDEX idx_features_name ON features(name);
CREATE INDEX idx_permissions_feature_id ON permissions(feature_id);
CREATE INDEX idx_permissions_name ON permissions(name);
CREATE INDEX idx_roles_permission_role_id ON roles_permission(role_id);
CREATE INDEX idx_roles_permission_permission_id ON roles_permission(permission_id);

-- Insert sample roles
INSERT INTO roles (name) VALUES 
    ('Admin'),
    ('Operator'),
    ('Cashier');

-- Insert features
INSERT INTO features (name, description) VALUES 
    ('User', 'User management operations'),
    ('Roles', 'Role and permission management'),
    ('Product', 'Product management operations');

-- Insert permissions for each feature
-- User management permissions
INSERT INTO permissions (feature_id, name, description) VALUES 
    (1, 'create', 'Create new users'),
    (1, 'read', 'View users and user details'),
    (1, 'update', 'Edit user information'),
    (1, 'delete', 'Remove users from system');

-- Roles management permissions
INSERT INTO permissions (feature_id, name, description) VALUES 
    (2, 'create', 'Create new roles'),
    (2, 'read', 'View roles and permissions'),
    (2, 'update', 'Edit role permissions'),
    (2, 'delete', 'Remove roles from system');

-- Product management permissions
INSERT INTO permissions (feature_id, name, description) VALUES 
    (3, 'create', 'Create new products'),
    (3, 'read', 'View products and inventory'),
    (3, 'update', 'Edit product information'),
    (3, 'delete', 'Remove products from system');

-- Assign permissions to roles
-- Admin gets all permissions (1-12)
INSERT INTO roles_permission (role_id, permission_id) VALUES 
    (1, 1), (1, 2), (1, 3), (1, 4),  -- User: create, read, update, delete
    (1, 5), (1, 6), (1, 7), (1, 8),  -- Roles: create, read, update, delete
    (1, 9), (1, 10), (1, 11), (1, 12); -- Product: create, read, update, delete

-- Operator gets user and product permissions (no role management)
INSERT INTO roles_permission (role_id, permission_id) VALUES 
    (2, 1), (2, 2), (2, 3), (2, 4),  -- User: create, read, update, delete
    (2, 9), (2, 10), (2, 11), (2, 12); -- Product: create, read, update, delete

-- Cashier gets read-only access to users and products
INSERT INTO roles_permission (role_id, permission_id) VALUES 
    (3, 2),   -- User: read only
    (3, 10),  -- Product: read only
    (3, 11);  -- Product: update (for inventory management)

-- Insert sample users
INSERT INTO users (name, email, role_id) VALUES 
    ('Kyaw Kyaw', 'kyaw@example.com', 1),    -- Admin user
    ('Aung Aung', 'aung@example.com', 2),    -- Operator user
    ('Ma Ma', 'mama@example.com', 3);        -- Cashier user

-- Create triggers for updated_at timestamp (SQLite doesn't have automatic UPDATE)
-- Trigger for users table
CREATE TRIGGER update_users_updated_at 
    AFTER UPDATE ON users
BEGIN
    UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Trigger for roles table
CREATE TRIGGER update_roles_updated_at 
    AFTER UPDATE ON roles
BEGIN
    UPDATE roles SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Trigger for features table
CREATE TRIGGER update_features_updated_at 
    AFTER UPDATE ON features
BEGIN
    UPDATE features SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Trigger for permissions table
CREATE TRIGGER update_permissions_updated_at 
    AFTER UPDATE ON permissions
BEGIN
    UPDATE permissions SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Verification queries (commented out for production)
/*
-- Verify the setup
SELECT 'Roles Count: ' || COUNT(*) FROM roles;
SELECT 'Features Count: ' || COUNT(*) FROM features; 
SELECT 'Permissions Count: ' || COUNT(*) FROM permissions;
SELECT 'Users Count: ' || COUNT(*) FROM users;
SELECT 'Role-Permission Mappings: ' || COUNT(*) FROM roles_permission;

-- Show role permissions
SELECT 
    r.name as role,
    f.name as feature, 
    p.name as permission
FROM roles r
JOIN roles_permission rp ON r.id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id  
JOIN features f ON p.feature_id = f.id
ORDER BY r.name, f.name, p.name;
*/