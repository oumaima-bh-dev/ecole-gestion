<?php
class Database {
    private static $host = 'localhost';
    private static $db_name = 'ecole_db';
    private static $username = 'root';
    private static $password = '';
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                // First try connecting to MySQL without selecting database to ensure it exists
                $temp_pdo = new PDO(
                    "mysql:host=" . self::$host,
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                
                // Create database if not exists
                $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `" . self::$db_name . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $temp_pdo = null;

                // Connect to the specific database
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4",
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );

                // Auto-initialize tables if empty
                self::initializeDatabase();
                self::ensureDocumentTables();
                self::ensureFinancialTables();

            } catch (PDOException $exception) {
                die("Connection error: " . $exception->getMessage());
            }
        }
        return self::$conn;
    }

    private static function initializeDatabase() {
        // Check if users table exists
        try {
            $stmt = self::$conn->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() == 0) {
                // Read and run schema.sql
                $schemaFile = dirname(__DIR__) . '/schema.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    self::$conn->exec($sql);
                    self::seedData();
                }
            }
        } catch (PDOException $e) {
            // Log or handle error
        }
    }

    private static function ensureDocumentTables() {
        self::$conn->exec("
            CREATE TABLE IF NOT EXISTS `document_categories` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `teacher_id` INT NOT NULL,
              `nom` VARCHAR(100) NOT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY `uniq_teacher_category` (`teacher_id`, `nom`),
              FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        self::$conn->exec("
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private static function ensureFinancialTables() {
        self::$conn->exec("
            CREATE TABLE IF NOT EXISTS `student_fees` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `student_id` INT NOT NULL UNIQUE,
              `total_due` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
              `notes` VARCHAR(255) DEFAULT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        self::addColumnIfMissing('payments', 'mode_paiement', "VARCHAR(50) NOT NULL DEFAULT 'Espèces'");
        self::addColumnIfMissing('payments', 'reference_paiement', "VARCHAR(100) DEFAULT NULL");
        self::addColumnIfMissing('payments', 'notes', "VARCHAR(255) DEFAULT NULL");

        self::$conn->exec("
            INSERT INTO student_fees (student_id, total_due)
            SELECT s.id,
                   CASE
                       WHEN n.nom = 'Primaire' THEN 6000.00
                       WHEN n.nom = 'Collège' THEN 7500.00
                       WHEN n.nom = 'Lycée' THEN 9000.00
                       ELSE 0.00
                   END
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN niveaux n ON c.niveau_id = n.id
            LEFT JOIN student_fees sf ON sf.student_id = s.id
            WHERE sf.id IS NULL
        ");
    }

    private static function addColumnIfMissing($table, $column, $definition) {
        $stmt = self::$conn->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        if ($stmt->rowCount() == 0) {
            self::$conn->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
        }
    }

    private static function seedData() {
        // Let's check if we already have data
        $check = self::$conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($check > 0) {
            return; // Already seeded
        }

        // Insert Levels
        self::$conn->exec("INSERT INTO niveaux (nom) VALUES ('Primaire'), ('Collège'), ('Lycée')");
        $niveau_primaire = 1;
        $niveau_college = 2;
        $niveau_lycee = 3;

        // Insert Classes
        self::$conn->exec("INSERT INTO classes (nom, niveau_id, capacite_max) VALUES 
            ('CP - A', 1, 25),
            ('CM2 - B', 1, 25),
            ('3ème - A', 2, 30),
            ('Terminale - S1', 3, 30)
        ");

        // Insert Matieres (Subjects)
        self::$conn->exec("INSERT INTO matieres (nom, coefficient) VALUES 
            ('Mathématiques', 5),
            ('Physique-Chimie', 4),
            ('Français', 3),
            ('SVT', 3),
            ('Histoire-Géographie', 2),
            ('Anglais', 2)
        ");

        // Hashed passwords
        $pwd_admin = password_hash('admin123', PASSWORD_BCRYPT);
        $pwd_teacher = password_hash('teacher123', PASSWORD_BCRYPT);
        $pwd_parent = password_hash('parent123', PASSWORD_BCRYPT);
        $pwd_student = password_hash('student123', PASSWORD_BCRYPT);

        // 1. Insert Admin User
        $stmt = self::$conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $pwd_admin, 'admin@ecole.com', 'admin']);

        // 2. Insert Teacher User & Profile
        $stmt->execute(['teacher1', $pwd_teacher, 'teacher1@ecole.com', 'teacher']);
        $teacher1_user_id = self::$conn->lastInsertId();
        
        $stmt->execute(['teacher2', $pwd_teacher, 'teacher2@ecole.com', 'teacher']);
        $teacher2_user_id = self::$conn->lastInsertId();

        $stmt_t = self::$conn->prepare("INSERT INTO teachers (user_id, telephone, diplome, date_embauche) VALUES (?, ?, ?, ?)");
        $stmt_t->execute([$teacher1_user_id, '0612345678', 'Master Mathématiques', '2023-09-01']);
        $teacher1_id = self::$conn->lastInsertId();
        
        $stmt_t->execute([$teacher2_user_id, '0687654321', 'Doctorat Physique', '2024-01-15']);
        $teacher2_id = self::$conn->lastInsertId();

        // 3. Insert Parent User & Profile
        $stmt->execute(['parent1', $pwd_parent, 'parent1@ecole.com', 'parent']);
        $parent1_user_id = self::$conn->lastInsertId();

        $stmt_p = self::$conn->prepare("INSERT INTO parents (user_id, telephone, adresse) VALUES (?, ?, ?)");
        $stmt_p->execute([$parent1_user_id, '0611223344', '123 Rue de la République, Paris']);
        $parent1_id = self::$conn->lastInsertId();

        // 4. Insert Student User & Profile
        $stmt->execute(['student1', $pwd_student, 'student1@ecole.com', 'student']);
        $student1_user_id = self::$conn->lastInsertId();

        $stmt_s = self::$conn->prepare("INSERT INTO students (user_id, class_id, parent_id, nom, prenom, date_naissance, sexe, adresse) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        // Enrolled in class 4 (Terminale - S1), Parent id 1
        $stmt_s->execute([$student1_user_id, 4, $parent1_id, 'Dupont', 'Lucas', '2008-05-14', 'M', '123 Rue de la République, Paris']);
        $student1_id = self::$conn->lastInsertId();

        // Another student without user account or with secondary parent (let's keep it simple: one more student associated with parent1)
        $stmt->execute(['student2', $pwd_student, 'student2@ecole.com', 'student']);
        $student2_user_id = self::$conn->lastInsertId();
        $stmt_s->execute([$student2_user_id, 4, $parent1_id, 'Dupont', 'Emma', '2009-11-23', 'F', '123 Rue de la République, Paris']);
        $student2_id = self::$conn->lastInsertId();

        // Assign Class-Teacher-Subject
        // teacher 1 (Math) in class 4 (Terminale S1)
        self::$conn->exec("INSERT INTO class_teachers (class_id, matiere_id, teacher_id) VALUES (4, 1, $teacher1_id)");
        // teacher 2 (Physique) in class 4
        self::$conn->exec("INSERT INTO class_teachers (class_id, matiere_id, teacher_id) VALUES (4, 2, $teacher2_id)");

        // Insert Grades (Notes) for student1 (Lucas) and student2 (Emma)
        $stmt_n = self::$conn->prepare("INSERT INTO notes (student_id, matiere_id, type_examen, note, date_examen) VALUES (?, ?, ?, ?, ?)");
        // Math notes for Lucas
        $stmt_n->execute([$student1_id, 1, 'Devoir 1', 14.50, '2026-10-10']);
        $stmt_n->execute([$student1_id, 1, 'Examen 1', 16.00, '2026-11-15']);
        // Physique notes for Lucas
        $stmt_n->execute([$student1_id, 2, 'Devoir 1', 12.00, '2026-10-12']);
        $stmt_n->execute([$student1_id, 2, 'Examen 1', 11.50, '2026-11-18']);

        // Math notes for Emma
        $stmt_n->execute([$student2_id, 1, 'Devoir 1', 11.00, '2026-10-10']);
        $stmt_n->execute([$student2_id, 1, 'Examen 1', 13.00, '2026-11-15']);

        // Insert Absences
        $stmt_a = self::$conn->prepare("INSERT INTO absences (student_id, date_absence, statut, justifie, motif) VALUES (?, ?, ?, ?, ?)");
        $stmt_a->execute([$student1_id, '2026-10-05', 'absent', 1, 'Visite médicale']);
        $stmt_a->execute([$student1_id, '2026-11-02', 'absent', 0, 'Pas de motif fourni']);

        self::$conn->exec("INSERT INTO student_fees (student_id, total_due) VALUES ($student1_id, 9000.00), ($student2_id, 9000.00)");

        // Insert Payments
        $stmt_pay = self::$conn->prepare("INSERT INTO payments (student_id, type_paiement, montant, date_paiement, recu_no, mode_paiement, reference_paiement) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_pay->execute([$student1_id, 'Inscription', 1500.00, '2026-09-01', 'REC-2026-001', 'Espèces', '']);
        $stmt_pay->execute([$student1_id, 'Mensualite', 500.00, '2026-10-01', 'REC-2026-002', 'Virement', 'VIR-2026-002']);
        $stmt_pay->execute([$student2_id, 'Inscription', 1500.00, '2026-09-01', 'REC-2026-003', 'Carte bancaire', 'CB-2026-003']);
    }
}
