<?php
class Student extends Model {

    public function getAll() {
        $stmt = $this->db->query("
            SELECT s.*, c.nom as class_name, u.email, u.status, u.username,
                   up.username as parent_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN parents p ON s.parent_id = p.id
            LEFT JOIN users up ON p.user_id = up.id
            ORDER BY s.nom, s.prenom
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.nom as class_name, u.email, u.status, u.username, s.parent_id
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByParentId($parentId) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.nom as class_name
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.parent_id = ?
            ORDER BY s.nom, s.prenom
        ");
        $stmt->execute([$parentId]);
        return $stmt->fetchAll();
    }

    public function getByClassId($classId) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.email, u.username
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.class_id = ?
            ORDER BY s.nom, s.prenom
        ");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public function create($username, $email, $password, $class_id, $parent_id, $nom, $prenom, $date_naissance, $sexe, $adresse) {
        try {
            $this->db->beginTransaction();

            // Check if user already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                throw new Exception("Nom d'utilisateur ou email déjà utilisé.");
            }

            // Check class capacity if class is set
            if (!empty($class_id)) {
                $stmt = $this->db->prepare("SELECT capacite_max, (SELECT COUNT(*) FROM students WHERE class_id = id) as current_count FROM classes WHERE id = ?");
                $stmt->execute([$class_id]);
                $classInfo = $stmt->fetch();
                if ($classInfo && $classInfo['current_count'] >= $classInfo['capacite_max']) {
                    throw new Exception("La classe a atteint sa capacité maximale.");
                }
            }

            // Create User account
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'student')");
            $stmt->execute([$username, $password_hash, $email]);
            $user_id = $this->db->lastInsertId();

            // Create Student profile
            $stmt = $this->db->prepare("
                INSERT INTO students (user_id, class_id, parent_id, nom, prenom, date_naissance, sexe, adresse)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                !empty($class_id) ? $class_id : null,
                !empty($parent_id) ? $parent_id : null,
                $nom,
                $prenom,
                $date_naissance,
                $sexe,
                $adresse
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $class_id, $parent_id, $nom, $prenom, $date_naissance, $sexe, $adresse, $email, $status) {
        try {
            $this->db->beginTransaction();

            // Get user_id of student
            $stmt = $this->db->prepare("SELECT user_id, class_id FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $student = $stmt->fetch();
            if (!$student) {
                throw new Exception("Étudiant introuvable.");
            }

            // Check class capacity if changing class
            if (!empty($class_id) && $class_id != $student['class_id']) {
                $stmt = $this->db->prepare("SELECT capacite_max, (SELECT COUNT(*) FROM students WHERE class_id = id) as current_count FROM classes WHERE id = ?");
                $stmt->execute([$class_id]);
                $classInfo = $stmt->fetch();
                if ($classInfo && $classInfo['current_count'] >= $classInfo['capacite_max']) {
                    throw new Exception("La classe sélectionnée a atteint sa capacité maximale.");
                }
            }

            // Update user status and email
            $stmt = $this->db->prepare("UPDATE users SET email = ?, status = ? WHERE id = ?");
            $stmt->execute([$email, $status, $student['user_id']]);

            // Update Student profile
            $stmt = $this->db->prepare("
                UPDATE students 
                SET class_id = ?, parent_id = ?, nom = ?, prenom = ?, date_naissance = ?, sexe = ?, adresse = ?
                WHERE id = ?
            ");
            $stmt->execute([
                !empty($class_id) ? $class_id : null,
                !empty($parent_id) ? $parent_id : null,
                $nom,
                $prenom,
                $date_naissance,
                $sexe,
                $adresse,
                $id
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        // Deleting user will cascade delete the student record due to FOREIGN KEY (... ON DELETE CASCADE)
        $stmt = $this->db->prepare("SELECT user_id FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
        if ($student) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$student['user_id']]);
        }
        return false;
    }
}
