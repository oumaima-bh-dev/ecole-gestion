CREATE DATABASE IF NOT EXISTS `ecole_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecole_db`;

-- Users table (authentication and general identity)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `role` ENUM('admin', 'teacher', 'parent', 'student') NOT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Niveaux (Levels) e.g. Primaire, College, Lycee
CREATE TABLE IF NOT EXISTS `niveaux` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Classes table
CREATE TABLE IF NOT EXISTS `classes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(50) NOT NULL UNIQUE,
  `niveau_id` INT NOT NULL,
  `capacite_max` INT DEFAULT 30,
  FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Subjects (Matieres) table
CREATE TABLE IF NOT EXISTS `matieres` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL UNIQUE,
  `coefficient` INT DEFAULT 1
) ENGINE=InnoDB;

-- Parents profiles
CREATE TABLE IF NOT EXISTS `parents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `telephone` VARCHAR(20) NOT NULL,
  `adresse` VARCHAR(255) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Students profiles
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `class_id` INT DEFAULT NULL,
  `parent_id` INT DEFAULT NULL,
  `nom` VARCHAR(50) NOT NULL,
  `prenom` VARCHAR(50) NOT NULL,
  `date_naissance` DATE NOT NULL,
  `sexe` ENUM('M', 'F') NOT NULL,
  `adresse` VARCHAR(255) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Teachers profiles
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `telephone` VARCHAR(20) NOT NULL,
  `diplome` VARCHAR(100) NOT NULL,
  `date_embauche` DATE NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Assignments table: association between teacher, class, and matiere
CREATE TABLE IF NOT EXISTS `class_teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `class_id` INT NOT NULL,
  `matiere_id` INT NOT NULL,
  `teacher_id` INT NOT NULL,
  UNIQUE KEY `uniq_assignment` (`class_id`, `matiere_id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Grades (Notes) table
CREATE TABLE IF NOT EXISTS `notes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `matiere_id` INT NOT NULL,
  `type_examen` VARCHAR(50) NOT NULL, -- e.g. 'Devoir 1', 'Examen 1', 'Devoir 2', etc.
  `note` DECIMAL(4,2) NOT NULL CHECK (`note` BETWEEN 0.00 AND 20.00),
  `date_examen` DATE NOT NULL,
  FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Absences table
CREATE TABLE IF NOT EXISTS `absences` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `date_absence` DATE NOT NULL,
  `statut` ENUM('absent', 'present') DEFAULT 'absent',
  `justifie` TINYINT(1) DEFAULT 0,
  `motif` VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Payments table
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `type_paiement` ENUM('Inscription', 'Mensualite', 'Frais divers') NOT NULL,
  `montant` DECIMAL(10,2) NOT NULL,
  `date_paiement` DATE NOT NULL,
  `recu_no` VARCHAR(50) NOT NULL UNIQUE,
  `mode_paiement` VARCHAR(50) NOT NULL DEFAULT 'Espèces',
  `reference_paiement` VARCHAR(100) DEFAULT NULL,
  `notes` VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Student financial situation
CREATE TABLE IF NOT EXISTS `student_fees` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL UNIQUE,
  `total_due` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `notes` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Document categories created by teachers
CREATE TABLE IF NOT EXISTS `document_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `nom` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_teacher_category` (`teacher_id`, `nom`),
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Pedagogical documents published for assigned classes
CREATE TABLE IF NOT EXISTS `pedagogical_documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `class_id` INT NOT NULL,
  `titre` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(120) DEFAULT NULL,
  `file_size` INT DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  INDEX `idx_documents_class_status` (`class_id`, `status`),
  INDEX `idx_documents_teacher` (`teacher_id`)
) ENGINE=InnoDB;
