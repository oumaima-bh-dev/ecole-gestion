<?php
class Teacher extends Model {

    public function getAll() {
        $stmt = $this->db->query("
            SELECT t.*, u.username, u.email, u.status 
            FROM teachers t
            JOIN users u ON t.user_id = u.id
            ORDER BY u.username
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT t.*, u.username, u.email, u.status 
            FROM teachers t
            JOIN users u ON t.user_id = u.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($username, $email, $password, $telephone, $diplome, $date_embauche) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                throw new Exception("Nom d'utilisateur ou email déjà utilisé.");
            }

            // Create user
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'teacher')");
            $stmt->execute([$username, $password_hash, $email]);
            $user_id = $this->db->lastInsertId();

            // Create teacher profile
            $stmt = $this->db->prepare("
                INSERT INTO teachers (user_id, telephone, diplome, date_embauche)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $telephone, $diplome, $date_embauche]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $telephone, $diplome, $date_embauche, $email, $status) {
        try {
            $this->db->beginTransaction();

            // Get user id
            $stmt = $this->db->prepare("SELECT user_id FROM teachers WHERE id = ?");
            $stmt->execute([$id]);
            $teacher = $stmt->fetch();
            if (!$teacher) {
                throw new Exception("Enseignant introuvable.");
            }

            // Update user status and email
            $stmt = $this->db->prepare("UPDATE users SET email = ?, status = ? WHERE id = ?");
            $stmt->execute([$email, $status, $teacher['user_id']]);

            // Update profile
            $stmt = $this->db->prepare("
                UPDATE teachers 
                SET telephone = ?, diplome = ?, date_embauche = ?
                WHERE id = ?
            ");
            $stmt->execute([$telephone, $diplome, $date_embauche, $id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("SELECT user_id FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $teacher = $stmt->fetch();
        if ($teacher) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$teacher['user_id']]);
        }
        return false;
    }

    // Get classes and subjects assigned to this teacher
    public function getAssignedClasses($teacherId) {
        $stmt = $this->db->prepare("
            SELECT ct.id as assignment_id, c.id as class_id, c.nom as class_name, 
                   m.id as matiere_id, m.nom as matiere_name, n.nom as niveau_name
            FROM class_teachers ct
            JOIN classes c ON ct.class_id = c.id
            JOIN niveaux n ON c.niveau_id = n.id
            JOIN matieres m ON ct.matiere_id = m.id
            WHERE ct.teacher_id = ?
            ORDER BY c.nom, m.nom
        ");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }
}
