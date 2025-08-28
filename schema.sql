-- Users Table (Extended Laravel default)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Roles & Permissions (Spatie Package)
-- Will be handled by spatie/laravel-permission package

-- Room Types Table (Translatable)
CREATE TABLE room_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    max_occupancy INT NOT NULL,
    amenities JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Room Type Translations
CREATE TABLE room_type_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id BIGINT UNSIGNED NOT NULL,
    locale VARCHAR(10) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    amenities_description TEXT,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_room_type_locale (room_type_id, locale)
);

-- Rooms Table
CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) UNIQUE NOT NULL,
    room_type_id BIGINT UNSIGNED NOT NULL,
    floor_number INT NOT NULL,
    size DECIMAL(8,2), -- in square meters
    smoking_allowed BOOLEAN DEFAULT FALSE,
    status ENUM('available', 'reserved', 'onboard', 'closed') DEFAULT 'available',
    last_maintenance DATE,
    notes TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id)
);

-- Bookings Table
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    guests_count INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'no_show') DEFAULT 'pending',
    special_requests TEXT,
    booking_source ENUM('website', 'phone', 'walk_in') DEFAULT 'website',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Payments Table
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('credit_card', 'debit_card', 'bank_transfer', 'cash') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    transaction_id VARCHAR(255),
    payment_status ENUM('completed', 'failed', 'refunded') DEFAULT 'failed',
    payment_gateway VARCHAR(50),
    gateway_response JSON,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Room Status History Table
CREATE TABLE room_status_histories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id BIGINT UNSIGNED NOT NULL,
    previous_status ENUM('available', 'reserved', 'onboard', 'closed'),
    new_status ENUM('available', 'reserved', 'onboard', 'closed') NOT NULL,
    changed_by BIGINT UNSIGNED NOT NULL,
    reason TEXT,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- Waitlist Table
CREATE TABLE waitlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    room_type_id BIGINT UNSIGNED NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    guests_count INT NOT NULL,
    status ENUM('active', 'notified', 'expired') DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id)
);