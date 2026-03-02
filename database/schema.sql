-- StoreHub SaaS schema (MySQL only)
-- Run this in phpMyAdmin on Hostinger.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS custom_domains;
DROP TABLE IF EXISTS stores;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id VARCHAR(100) NOT NULL UNIQUE,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  know_about_us VARCHAR(255) NULL,
  terms_accepted TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  referral_name VARCHAR(150) NULL,
  current_tab_id INT NULL,
  last_active DATETIME NULL,
  payment_status VARCHAR(50) DEFAULT 'pending',
  payment_date DATETIME NULL,
  plan_id INT NULL,
  active_theme VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE custom_domains (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id VARCHAR(100) NOT NULL,
  domain_name VARCHAR(255) NOT NULL UNIQUE,
  status ENUM('pending','verified','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  verified_at DATETIME NULL,
  CONSTRAINT fk_custom_domains_user_tenant
    FOREIGN KEY (tenant_id) REFERENCES users(tenant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE stores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id VARCHAR(100) NOT NULL,
  store_name VARCHAR(150) NOT NULL,
  description TEXT,
  theme_color VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_stores_user_tenant
    FOREIGN KEY (tenant_id) REFERENCES users(tenant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
