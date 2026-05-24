-- ElWarsha MVP+ Database Schema
-- MySQL 8.x compatible
-- Generated for MySQL Workbench import / reverse engineering

CREATE DATABASE IF NOT EXISTS elwarsha
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE elwarsha;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS complaint_media;
DROP TABLE IF EXISTS complaints;
DROP TABLE IF EXISTS whatsapp_messages;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS device_tokens;
DROP TABLE IF EXISTS featured_placements;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS plans;
DROP TABLE IF EXISTS workshop_analytics_events;
DROP TABLE IF EXISTS lead_status_logs;
DROP TABLE IF EXISTS lead_notes;
DROP TABLE IF EXISTS leads;
DROP TABLE IF EXISTS service_ledger_media;
DROP TABLE IF EXISTS service_ledgers;
DROP TABLE IF EXISTS vehicle_maintenance_reminders;
DROP TABLE IF EXISTS maintenance_items;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS emergency_guidance_requests;
DROP TABLE IF EXISTS sos_request_logs;
DROP TABLE IF EXISTS sos_requests;
DROP TABLE IF EXISTS sos_provider_services;
DROP TABLE IF EXISTS sos_providers;
DROP TABLE IF EXISTS sos_service_types;
DROP TABLE IF EXISTS booking_status_logs;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS diagnosis_workshop_suggestions;
DROP TABLE IF EXISTS diagnosis_media;
DROP TABLE IF EXISTS diagnoses;
DROP TABLE IF EXISTS symptoms;
DROP TABLE IF EXISTS workshop_verifications;
DROP TABLE IF EXISTS workshop_working_hours;
DROP TABLE IF EXISTS workshop_images;
DROP TABLE IF EXISTS workshop_brands;
DROP TABLE IF EXISTS workshop_services;
DROP TABLE IF EXISTS workshops;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS service_categories;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS car_models;
DROP TABLE IF EXISTS car_brands;
DROP TABLE IF EXISTS user_consents;
DROP TABLE IF EXISTS otp_codes;
DROP TABLE IF EXISTS static_pages;
DROP TABLE IF EXISTS app_settings;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 1. Users & Auth
-- =====================================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(255) NULL UNIQUE,
    password VARCHAR(255) NULL,
    role ENUM('customer','workshop_owner','provider','admin','super_admin') NOT NULL DEFAULT 'customer',
    avatar VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    area VARCHAR(100) NULL,
    status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active',
    phone_verified_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    INDEX idx_users_role (role),
    INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE otp_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(30) NOT NULL,
    code VARCHAR(10) NOT NULL,
    purpose ENUM('register','login','reset_password') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    INDEX idx_otp_phone (phone),
    INDEX idx_otp_code (code),
    INDEX idx_otp_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_consents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('terms','privacy','diagnosis_disclaimer','safety_disclaimer') NOT NULL,
    version VARCHAR(50) NOT NULL,
    accepted_at TIMESTAMP NOT NULL,
    ip_address VARCHAR(100) NULL,
    user_agent TEXT NULL,
    CONSTRAINT fk_user_consents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_consents_user_id (user_id),
    INDEX idx_user_consents_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. Cars / Garage
-- =====================================================

CREATE TABLE car_brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_car_brands_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE car_models (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_brand_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_car_models_brand FOREIGN KEY (car_brand_id) REFERENCES car_brands(id) ON DELETE CASCADE,
    INDEX idx_car_models_brand_id (car_brand_id),
    INDEX idx_car_models_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    car_brand_id BIGINT UNSIGNED NOT NULL,
    car_model_id BIGINT UNSIGNED NOT NULL,
    year YEAR NULL,
    mileage_km INT NULL,
    plate_number VARCHAR(50) NULL,
    vin VARCHAR(100) NULL,
    color VARCHAR(50) NULL,
    image VARCHAR(255) NULL,
    notes TEXT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_vehicles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_vehicles_brand FOREIGN KEY (car_brand_id) REFERENCES car_brands(id),
    CONSTRAINT fk_vehicles_model FOREIGN KEY (car_model_id) REFERENCES car_models(id),
    INDEX idx_vehicles_user_id (user_id),
    INDEX idx_vehicles_brand_id (car_brand_id),
    INDEX idx_vehicles_model_id (car_model_id),
    INDEX idx_vehicles_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. Services & Categories
-- =====================================================

CREATE TABLE service_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(255) NULL,
    description TEXT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_service_categories_status (status),
    INDEX idx_service_categories_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_services_category FOREIGN KEY (service_category_id) REFERENCES service_categories(id) ON DELETE CASCADE,
    INDEX idx_services_category_id (service_category_id),
    INDEX idx_services_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. Workshops
-- =====================================================

CREATE TABLE workshops (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    phone VARCHAR(30) NOT NULL,
    whatsapp VARCHAR(30) NULL,
    email VARCHAR(255) NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    area VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    google_maps_url TEXT NULL,
    accepts_booking BOOLEAN NOT NULL DEFAULT TRUE,
    accepts_sos BOOLEAN NOT NULL DEFAULT FALSE,
    is_verified BOOLEAN NOT NULL DEFAULT FALSE,
    rating_avg DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    reviews_count INT NOT NULL DEFAULT 0,
    status ENUM('pending','approved','rejected','suspended') NOT NULL DEFAULT 'pending',
    subscription_status ENUM('free','active','expired','cancelled') NOT NULL DEFAULT 'free',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_workshops_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_workshops_owner_id (owner_id),
    INDEX idx_workshops_status (status),
    INDEX idx_workshops_city (city),
    INDEX idx_workshops_area (area),
    INDEX idx_workshops_lat_lng (latitude, longitude),
    INDEX idx_workshops_verified (is_verified),
    INDEX idx_workshops_accepts_booking (accepts_booking),
    INDEX idx_workshops_accepts_sos (accepts_sos),
    INDEX idx_workshops_rating_avg (rating_avg)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workshop_services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_workshop_services_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_workshop_services_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    UNIQUE KEY uq_workshop_service (workshop_id, service_id),
    INDEX idx_workshop_services_workshop_id (workshop_id),
    INDEX idx_workshop_services_service_id (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workshop_brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    car_brand_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_workshop_brands_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_workshop_brands_brand FOREIGN KEY (car_brand_id) REFERENCES car_brands(id) ON DELETE CASCADE,
    UNIQUE KEY uq_workshop_brand (workshop_id, car_brand_id),
    INDEX idx_workshop_brands_workshop_id (workshop_id),
    INDEX idx_workshop_brands_brand_id (car_brand_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workshop_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    type ENUM('workshop','before_after','logo','cover') NOT NULL DEFAULT 'workshop',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_workshop_images_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    INDEX idx_workshop_images_workshop_id (workshop_id),
    INDEX idx_workshop_images_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workshop_working_hours (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    day_of_week ENUM('saturday','sunday','monday','tuesday','wednesday','thursday','friday') NOT NULL,
    opens_at TIME NULL,
    closes_at TIME NULL,
    is_closed BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_workshop_working_hours_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    UNIQUE KEY uq_workshop_day (workshop_id, day_of_week),
    INDEX idx_workshop_working_hours_workshop_id (workshop_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workshop_verifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    commercial_register VARCHAR(255) NULL,
    tax_card VARCHAR(255) NULL,
    owner_id_image VARCHAR(255) NULL,
    workshop_license VARCHAR(255) NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_workshop_verifications_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_workshop_verifications_admin FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_workshop_verifications_workshop_id (workshop_id),
    INDEX idx_workshop_verifications_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. AI Fault Diagnosis
-- =====================================================

CREATE TABLE symptoms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_symptoms_status (status),
    INDEX idx_symptoms_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE diagnoses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    symptoms_json JSON NULL,
    ai_response JSON NULL,
    diagnosis_text TEXT NULL,
    confidence ENUM('low','medium','high') NULL,
    urgency ENUM('low','medium','high') NULL,
    affected_category_id BIGINT UNSIGNED NULL,
    recommend_professional BOOLEAN NOT NULL DEFAULT TRUE,
    status ENUM('pending','completed','failed','manual_review') NOT NULL DEFAULT 'pending',
    disclaimer_accepted BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_diagnoses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_diagnoses_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_diagnoses_affected_category FOREIGN KEY (affected_category_id) REFERENCES service_categories(id) ON DELETE SET NULL,
    INDEX idx_diagnoses_user_id (user_id),
    INDEX idx_diagnoses_vehicle_id (vehicle_id),
    INDEX idx_diagnoses_status (status),
    INDEX idx_diagnoses_urgency (urgency),
    INDEX idx_diagnoses_affected_category_id (affected_category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE diagnosis_media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diagnosis_id BIGINT UNSIGNED NOT NULL,
    media_type ENUM('image','audio','video') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_diagnosis_media_diagnosis FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(id) ON DELETE CASCADE,
    INDEX idx_diagnosis_media_diagnosis_id (diagnosis_id),
    INDEX idx_diagnosis_media_type (media_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE diagnosis_workshop_suggestions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diagnosis_id BIGINT UNSIGNED NOT NULL,
    workshop_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    reason TEXT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_diagnosis_suggestions_diagnosis FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(id) ON DELETE CASCADE,
    CONSTRAINT fk_diagnosis_suggestions_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    INDEX idx_diagnosis_suggestions_diagnosis_id (diagnosis_id),
    INDEX idx_diagnosis_suggestions_workshop_id (workshop_id),
    INDEX idx_diagnosis_suggestions_score (score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. Bookings
-- =====================================================

CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    workshop_id BIGINT UNSIGNED NOT NULL,
    diagnosis_id BIGINT UNSIGNED NULL,
    service_id BIGINT UNSIGNED NULL,
    scheduled_at DATETIME NULL,
    description TEXT NULL,
    status ENUM('pending','accepted','declined','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
    workshop_notes TEXT NULL,
    admin_notes TEXT NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_diagnosis FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(id) ON DELETE SET NULL,
    CONSTRAINT fk_bookings_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    INDEX idx_bookings_user_id (user_id),
    INDEX idx_bookings_workshop_id (workshop_id),
    INDEX idx_bookings_vehicle_id (vehicle_id),
    INDEX idx_bookings_diagnosis_id (diagnosis_id),
    INDEX idx_bookings_service_id (service_id),
    INDEX idx_bookings_status (status),
    INDEX idx_bookings_scheduled_at (scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_status_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(50) NULL,
    new_status VARCHAR(50) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_booking_logs_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_logs_changed_by FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_booking_logs_booking_id (booking_id),
    INDEX idx_booking_logs_changed_by (changed_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. SOS Dispatch
-- =====================================================

CREATE TABLE sos_service_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    icon VARCHAR(255) NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_sos_service_types_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sos_providers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    workshop_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    whatsapp VARCHAR(30) NULL,
    city VARCHAR(100) NOT NULL,
    area VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    is_available BOOLEAN NOT NULL DEFAULT TRUE,
    rating_avg DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending','approved','rejected','suspended') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_sos_providers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_sos_providers_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    INDEX idx_sos_providers_user_id (user_id),
    INDEX idx_sos_providers_workshop_id (workshop_id),
    INDEX idx_sos_providers_status (status),
    INDEX idx_sos_providers_available (is_available),
    INDEX idx_sos_providers_lat_lng (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sos_provider_services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sos_provider_id BIGINT UNSIGNED NOT NULL,
    sos_service_type_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_sos_provider_services_provider FOREIGN KEY (sos_provider_id) REFERENCES sos_providers(id) ON DELETE CASCADE,
    CONSTRAINT fk_sos_provider_services_type FOREIGN KEY (sos_service_type_id) REFERENCES sos_service_types(id) ON DELETE CASCADE,
    UNIQUE KEY uq_sos_provider_service (sos_provider_id, sos_service_type_id),
    INDEX idx_sos_provider_services_provider_id (sos_provider_id),
    INDEX idx_sos_provider_services_type_id (sos_service_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sos_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    vehicle_id BIGINT UNSIGNED NULL,
    sos_service_type_id BIGINT UNSIGNED NOT NULL,
    assigned_provider_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    image_path VARCHAR(255) NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    urgency ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    status ENUM('pending','assigned','accepted','on_the_way','arrived','completed','cancelled') NOT NULL DEFAULT 'pending',
    accepted_at TIMESTAMP NULL,
    arrived_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_sos_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_sos_requests_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    CONSTRAINT fk_sos_requests_type FOREIGN KEY (sos_service_type_id) REFERENCES sos_service_types(id),
    CONSTRAINT fk_sos_requests_provider FOREIGN KEY (assigned_provider_id) REFERENCES sos_providers(id) ON DELETE SET NULL,
    INDEX idx_sos_requests_user_id (user_id),
    INDEX idx_sos_requests_vehicle_id (vehicle_id),
    INDEX idx_sos_requests_provider_id (assigned_provider_id),
    INDEX idx_sos_requests_type_id (sos_service_type_id),
    INDEX idx_sos_requests_status (status),
    INDEX idx_sos_requests_lat_lng (latitude, longitude),
    INDEX idx_sos_requests_urgency (urgency)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sos_request_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sos_request_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(50) NULL,
    new_status VARCHAR(50) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_sos_request_logs_request FOREIGN KEY (sos_request_id) REFERENCES sos_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_sos_request_logs_changed_by FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sos_request_logs_request_id (sos_request_id),
    INDEX idx_sos_request_logs_changed_by (changed_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. AI Emergency Guidance
-- =====================================================

CREATE TABLE emergency_guidance_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    vehicle_id BIGINT UNSIGNED NULL,
    description TEXT NOT NULL,
    symptoms_json JSON NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    ai_response JSON NULL,
    urgency ENUM('low','medium','high') NULL,
    needs_sos BOOLEAN NOT NULL DEFAULT FALSE,
    recommended_sos_service_type_id BIGINT UNSIGNED NULL,
    safety_message TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_emergency_guidance_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_emergency_guidance_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    CONSTRAINT fk_emergency_guidance_sos_type FOREIGN KEY (recommended_sos_service_type_id) REFERENCES sos_service_types(id) ON DELETE SET NULL,
    INDEX idx_emergency_guidance_user_id (user_id),
    INDEX idx_emergency_guidance_vehicle_id (vehicle_id),
    INDEX idx_emergency_guidance_urgency (urgency),
    INDEX idx_emergency_guidance_needs_sos (needs_sos)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. Reviews & Ratings
-- =====================================================

CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    workshop_id BIGINT UNSIGNED NOT NULL,
    booking_id BIGINT UNSIGNED NULL,
    sos_request_id BIGINT UNSIGNED NULL,
    rating TINYINT UNSIGNED NOT NULL,
    quality_rating TINYINT UNSIGNED NULL,
    price_rating TINYINT UNSIGNED NULL,
    punctuality_rating TINYINT UNSIGNED NULL,
    behavior_rating TINYINT UNSIGNED NULL,
    comment TEXT NULL,
    status ENUM('pending','published','hidden') NOT NULL DEFAULT 'published',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_reviews_sos_request FOREIGN KEY (sos_request_id) REFERENCES sos_requests(id) ON DELETE SET NULL,
    INDEX idx_reviews_workshop_id (workshop_id),
    INDEX idx_reviews_user_id (user_id),
    INDEX idx_reviews_status (status),
    INDEX idx_reviews_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. Maintenance Planner
-- =====================================================

CREATE TABLE maintenance_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    default_interval_km INT NULL,
    default_interval_months INT NULL,
    service_category_id BIGINT UNSIGNED NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_maintenance_items_category FOREIGN KEY (service_category_id) REFERENCES service_categories(id) ON DELETE SET NULL,
    INDEX idx_maintenance_items_category_id (service_category_id),
    INDEX idx_maintenance_items_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_maintenance_reminders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    maintenance_item_id BIGINT UNSIGNED NOT NULL,
    last_done_at DATE NULL,
    last_done_mileage INT NULL,
    next_due_at DATE NULL,
    next_due_mileage INT NULL,
    reminder_before_days INT NOT NULL DEFAULT 7,
    status ENUM('active','done','skipped','cancelled') NOT NULL DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_maintenance_reminders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_maintenance_reminders_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_maintenance_reminders_item FOREIGN KEY (maintenance_item_id) REFERENCES maintenance_items(id) ON DELETE CASCADE,
    INDEX idx_maintenance_reminders_user_id (user_id),
    INDEX idx_maintenance_reminders_vehicle_id (vehicle_id),
    INDEX idx_maintenance_reminders_item_id (maintenance_item_id),
    INDEX idx_maintenance_reminders_status (status),
    INDEX idx_maintenance_reminders_next_due_at (next_due_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. Service Ledger
-- =====================================================

CREATE TABLE service_ledgers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    workshop_id BIGINT UNSIGNED NULL,
    booking_id BIGINT UNSIGNED NULL,
    diagnosis_id BIGINT UNSIGNED NULL,
    sos_request_id BIGINT UNSIGNED NULL,
    maintenance_item_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    service_date DATE NOT NULL,
    cost DECIMAL(10,2) NULL,
    mileage_km INT NULL,
    invoice_file VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_service_ledgers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_service_ledgers_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    CONSTRAINT fk_service_ledgers_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    CONSTRAINT fk_service_ledgers_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_service_ledgers_diagnosis FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(id) ON DELETE SET NULL,
    CONSTRAINT fk_service_ledgers_sos_request FOREIGN KEY (sos_request_id) REFERENCES sos_requests(id) ON DELETE SET NULL,
    CONSTRAINT fk_service_ledgers_maintenance_item FOREIGN KEY (maintenance_item_id) REFERENCES maintenance_items(id) ON DELETE SET NULL,
    INDEX idx_service_ledgers_user_id (user_id),
    INDEX idx_service_ledgers_vehicle_id (vehicle_id),
    INDEX idx_service_ledgers_workshop_id (workshop_id),
    INDEX idx_service_ledgers_booking_id (booking_id),
    INDEX idx_service_ledgers_diagnosis_id (diagnosis_id),
    INDEX idx_service_ledgers_sos_request_id (sos_request_id),
    INDEX idx_service_ledgers_service_date (service_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE service_ledger_media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_ledger_id BIGINT UNSIGNED NOT NULL,
    media_type ENUM('image','invoice','document') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_service_ledger_media_ledger FOREIGN KEY (service_ledger_id) REFERENCES service_ledgers(id) ON DELETE CASCADE,
    INDEX idx_service_ledger_media_ledger_id (service_ledger_id),
    INDEX idx_service_ledger_media_type (media_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. CRM / Leads
-- =====================================================

CREATE TABLE leads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    vehicle_id BIGINT UNSIGNED NULL,
    booking_id BIGINT UNSIGNED NULL,
    diagnosis_id BIGINT UNSIGNED NULL,
    sos_request_id BIGINT UNSIGNED NULL,
    source ENUM('profile_view','call_click','whatsapp_click','directions_click','booking','sos','diagnosis_recommendation') NOT NULL,
    status ENUM('new','contacted','booked','in_service','completed','lost','follow_up_needed') NOT NULL DEFAULT 'new',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_leads_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_leads_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_leads_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    CONSTRAINT fk_leads_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_leads_diagnosis FOREIGN KEY (diagnosis_id) REFERENCES diagnoses(id) ON DELETE SET NULL,
    CONSTRAINT fk_leads_sos_request FOREIGN KEY (sos_request_id) REFERENCES sos_requests(id) ON DELETE SET NULL,
    INDEX idx_leads_workshop_id (workshop_id),
    INDEX idx_leads_user_id (user_id),
    INDEX idx_leads_vehicle_id (vehicle_id),
    INDEX idx_leads_status (status),
    INDEX idx_leads_source (source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lead_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_lead_notes_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    CONSTRAINT fk_lead_notes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_lead_notes_lead_id (lead_id),
    INDEX idx_lead_notes_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lead_status_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(50) NULL,
    new_status VARCHAR(50) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_lead_status_logs_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    CONSTRAINT fk_lead_status_logs_changed_by FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_lead_status_logs_lead_id (lead_id),
    INDEX idx_lead_status_logs_changed_by (changed_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workshop_analytics_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    event_type ENUM('profile_view','call_click','whatsapp_click','directions_click','booking_click','sos_click') NOT NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_workshop_analytics_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_workshop_analytics_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_workshop_analytics_workshop_id (workshop_id),
    INDEX idx_workshop_analytics_user_id (user_id),
    INDEX idx_workshop_analytics_event_type (event_type),
    INDEX idx_workshop_analytics_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. Subscriptions & Payments
-- =====================================================

CREATE TABLE plans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duration_days INT NOT NULL,
    description TEXT NULL,
    features JSON NULL,
    is_featured BOOLEAN NOT NULL DEFAULT FALSE,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_plans_status (status),
    INDEX idx_plans_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    starts_at DATE NOT NULL,
    ends_at DATE NOT NULL,
    status ENUM('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
    auto_renew BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_subscriptions_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_subscriptions_plan FOREIGN KEY (plan_id) REFERENCES plans(id),
    INDEX idx_subscriptions_workshop_id (workshop_id),
    INDEX idx_subscriptions_plan_id (plan_id),
    INDEX idx_subscriptions_status (status),
    INDEX idx_subscriptions_ends_at (ends_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    workshop_id BIGINT UNSIGNED NULL,
    subscription_id BIGINT UNSIGNED NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('vodafone_cash','instapay','bank_transfer','cash','paymob','fawry') NOT NULL,
    transaction_reference VARCHAR(255) NULL,
    receipt_image VARCHAR(255) NULL,
    status ENUM('pending','approved','rejected','failed','refunded') NOT NULL DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_payments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_payments_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    CONSTRAINT fk_payments_subscription FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL,
    CONSTRAINT fk_payments_approved_by FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_payments_user_id (user_id),
    INDEX idx_payments_workshop_id (workshop_id),
    INDEX idx_payments_subscription_id (subscription_id),
    INDEX idx_payments_status (status),
    INDEX idx_payments_method (payment_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE featured_placements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workshop_id BIGINT UNSIGNED NOT NULL,
    placement_type ENUM('home','search','category','area','sos') NOT NULL,
    service_category_id BIGINT UNSIGNED NULL,
    city VARCHAR(100) NULL,
    area VARCHAR(100) NULL,
    starts_at DATE NOT NULL,
    ends_at DATE NOT NULL,
    price DECIMAL(10,2) NULL,
    status ENUM('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_featured_placements_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE CASCADE,
    CONSTRAINT fk_featured_placements_category FOREIGN KEY (service_category_id) REFERENCES service_categories(id) ON DELETE SET NULL,
    INDEX idx_featured_workshop_id (workshop_id),
    INDEX idx_featured_category_id (service_category_id),
    INDEX idx_featured_type (placement_type),
    INDEX idx_featured_status (status),
    INDEX idx_featured_dates (starts_at, ends_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. Notifications
-- =====================================================

CREATE TABLE device_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token TEXT NOT NULL,
    platform ENUM('android','ios','web') NOT NULL,
    device_name VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_device_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_device_tokens_user_id (user_id),
    INDEX idx_device_tokens_platform (platform)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    type VARCHAR(100) NOT NULL,
    data JSON NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user_id (user_id),
    INDEX idx_notifications_type (type),
    INDEX idx_notifications_read_at (read_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE whatsapp_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    workshop_id BIGINT UNSIGNED NULL,
    phone VARCHAR(30) NOT NULL,
    message TEXT NOT NULL,
    template_name VARCHAR(100) NULL,
    provider_response JSON NULL,
    status ENUM('pending','sent','failed','delivered','read') NOT NULL DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_whatsapp_messages_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_whatsapp_messages_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    INDEX idx_whatsapp_messages_user_id (user_id),
    INDEX idx_whatsapp_messages_workshop_id (workshop_id),
    INDEX idx_whatsapp_messages_phone (phone),
    INDEX idx_whatsapp_messages_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. Complaints
-- =====================================================

CREATE TABLE complaints (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    workshop_id BIGINT UNSIGNED NULL,
    booking_id BIGINT UNSIGNED NULL,
    sos_request_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open','in_review','resolved','rejected','closed') NOT NULL DEFAULT 'open',
    admin_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_complaints_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_complaints_workshop FOREIGN KEY (workshop_id) REFERENCES workshops(id) ON DELETE SET NULL,
    CONSTRAINT fk_complaints_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_complaints_sos_request FOREIGN KEY (sos_request_id) REFERENCES sos_requests(id) ON DELETE SET NULL,
    INDEX idx_complaints_user_id (user_id),
    INDEX idx_complaints_workshop_id (workshop_id),
    INDEX idx_complaints_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE complaint_media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    complaint_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    media_type ENUM('image','video','document') NOT NULL,
    created_at TIMESTAMP NULL,
    CONSTRAINT fk_complaint_media_complaint FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
    INDEX idx_complaint_media_complaint_id (complaint_id),
    INDEX idx_complaint_media_type (media_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. Admin / Settings
-- =====================================================

CREATE TABLE app_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL,
    type ENUM('string','number','boolean','json','text') NOT NULL DEFAULT 'string',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE static_pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    version VARCHAR(50) NOT NULL DEFAULT '1.0',
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_static_pages_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Optional Seed Data for Core Lookup Tables
-- =====================================================

INSERT INTO service_categories (name, slug, status, sort_order, created_at, updated_at) VALUES
('Mechanics', 'mechanics', 'active', 1, NOW(), NOW()),
('Electricity', 'electricity', 'active', 2, NOW(), NOW()),
('Bodywork', 'bodywork', 'active', 3, NOW(), NOW()),
('Paint', 'paint', 'active', 4, NOW(), NOW()),
('Upholstery', 'upholstery', 'active', 5, NOW(), NOW()),
('Car Wash', 'car-wash', 'active', 6, NOW(), NOW()),
('Tires', 'tires', 'active', 7, NOW(), NOW()),
('Batteries', 'batteries', 'active', 8, NOW(), NOW()),
('Oil & Filters', 'oil-filters', 'active', 9, NOW(), NOW()),
('AC', 'ac', 'active', 10, NOW(), NOW()),
('Brakes', 'brakes', 'active', 11, NOW(), NOW()),
('Suspension', 'suspension', 'active', 12, NOW(), NOW()),
('Computer Scan', 'computer-scan', 'active', 13, NOW(), NOW()),
('Towing', 'towing', 'active', 14, NOW(), NOW()),
('Detailing', 'detailing', 'active', 15, NOW(), NOW()),
('Ceramic Coating', 'ceramic-coating', 'active', 16, NOW(), NOW()),
('PPF', 'ppf', 'active', 17, NOW(), NOW());

INSERT INTO sos_service_types (name, slug, status, created_at, updated_at) VALUES
('Towing', 'towing', 'active', NOW(), NOW()),
('Dead Battery', 'dead-battery', 'active', NOW(), NOW()),
('Flat Tire', 'flat-tire', 'active', NOW(), NOW()),
('Overheating', 'overheating', 'active', NOW(), NOW()),
('Car Not Starting', 'car-not-starting', 'active', NOW(), NOW()),
('Out of Fuel', 'out-of-fuel', 'active', NOW(), NOW()),
('Accident Support', 'accident-support', 'active', NOW(), NOW()),
('Locked Key', 'locked-key', 'active', NOW(), NOW()),
('Brake Emergency', 'brake-emergency', 'active', NOW(), NOW()),
('Electrical Emergency', 'electrical-emergency', 'active', NOW(), NOW());

INSERT INTO maintenance_items (name, description, default_interval_km, default_interval_months, status, created_at, updated_at) VALUES
('Oil Change', 'Engine oil change reminder.', 5000, 6, 'active', NOW(), NOW()),
('Oil Filter', 'Oil filter replacement reminder.', 5000, 6, 'active', NOW(), NOW()),
('Air Filter', 'Air filter replacement reminder.', 10000, 12, 'active', NOW(), NOW()),
('Fuel Filter', 'Fuel filter replacement reminder.', 20000, 24, 'active', NOW(), NOW()),
('AC Filter', 'Cabin or AC filter replacement reminder.', 10000, 12, 'active', NOW(), NOW()),
('Battery', 'Battery check or replacement reminder.', NULL, 24, 'active', NOW(), NOW()),
('Tires', 'Tire inspection or replacement reminder.', 40000, 36, 'active', NOW(), NOW()),
('Brakes', 'Brake inspection or pads replacement reminder.', 20000, 12, 'active', NOW(), NOW()),
('AC Check', 'Air conditioning maintenance reminder.', NULL, 12, 'active', NOW(), NOW()),
('License Renewal', 'Car license renewal reminder.', NULL, 12, 'active', NOW(), NOW()),
('Insurance Renewal', 'Insurance renewal reminder.', NULL, 12, 'active', NOW(), NOW()),
('Pre-travel Check', 'Pre-travel car inspection reminder.', NULL, NULL, 'active', NOW(), NOW());

INSERT INTO plans (name, code, price, duration_days, description, features, is_featured, status, created_at, updated_at) VALUES
('Free', 'free', 0.00, 30, 'Basic workshop visibility.', JSON_OBJECT('profile', true, 'limited_images', true), false, 'active', NOW(), NOW()),
('Basic', 'basic', 500.00, 30, 'Full workshop profile and leads.', JSON_OBJECT('profile', true, 'leads', true, 'images', true), false, 'active', NOW(), NOW()),
('Pro', 'pro', 1000.00, 30, 'Higher visibility, verified badge, basic analytics and CRM.', JSON_OBJECT('profile', true, 'leads', true, 'verified_badge', true, 'analytics', true, 'crm', 'basic'), false, 'active', NOW(), NOW()),
('Premium', 'premium', 1500.00, 30, 'Featured listing, priority search, full CRM and reports.', JSON_OBJECT('profile', true, 'featured', true, 'priority_search', true, 'analytics', true, 'crm', 'full'), true, 'active', NOW(), NOW());

INSERT INTO static_pages (slug, title, content, version, status, created_at, updated_at) VALUES
('terms', 'Terms and Conditions', 'Terms and conditions content goes here.', '1.0', 'draft', NOW(), NOW()),
('privacy', 'Privacy Policy', 'Privacy policy content goes here.', '1.0', 'draft', NOW(), NOW()),
('diagnosis-disclaimer', 'Diagnosis Disclaimer', 'AI diagnosis is preliminary and is not a replacement for a professional mechanic inspection.', '1.0', 'draft', NOW(), NOW()),
('safety-disclaimer', 'Safety Disclaimer', 'Emergency guidance is general and safety-first. Always stop safely and seek professional assistance for dangerous situations.', '1.0', 'draft', NOW(), NOW());

-- Done
