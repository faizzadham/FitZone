-- Personal Trainer Session Schedule Feature
-- Run this after the initial gym_db.sql

USE gym_db;

-- Trainers table
CREATE TABLE IF NOT EXISTS trainers (
    trainer_id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    available_days VARCHAR(200) NOT NULL,
    available_time VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20),
    status ENUM('Available', 'Busy') NOT NULL DEFAULT 'Available',
    session_fee DECIMAL(10,2) NOT NULL DEFAULT 50.00
) ENGINE=InnoDB;

-- Session bookings table
CREATE TABLE IF NOT EXISTS session_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    trainer_id INT NOT NULL,
    session_date DATE NOT NULL,
    session_time VARCHAR(20) NOT NULL,
    session_type ENUM('Strength', 'Cardio', 'Weight Loss', 'Rehab') NOT NULL DEFAULT 'Strength',
    booking_status ENUM('Pending', 'Approved', 'Rejected', 'Cancelled', 'Completed') NOT NULL DEFAULT 'Pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(member_id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(trainer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed trainers
INSERT INTO trainers (trainer_name, specialization, available_days, available_time, contact_number, status, session_fee) VALUES
('Ahmad Rizal', 'Strength Training', 'Mon, Tue, Wed, Thu, Fri', '8:00 AM - 12:00 PM', '012-3456789', 'Available', 80.00),
('Sarah Tan', 'Cardio & HIIT', 'Mon, Wed, Fri, Sat', '2:00 PM - 6:00 PM', '013-9876543', 'Available', 70.00),
('David Lee', 'Weight Loss', 'Tue, Thu, Sat', '9:00 AM - 1:00 PM', '011-2345678', 'Available', 75.00),
('Nurul Aisyah', 'Rehabilitation', 'Mon, Tue, Wed, Thu, Fri', '10:00 AM - 4:00 PM', '014-5678901', 'Available', 90.00);
