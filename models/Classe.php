<?php
class Classe extends Model {

    public function getAll() {
        $stmt = $this->db->query("
            SELECT c.*, n.nom as niveau_nom, 
                   (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count 
            FROM classes c 
            JOIN niveaux n ON c.niveau_id = n.id
            ORDER BY n.id, c.nom
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT c.*, n.nom as niveau_nom FROM classes c JOIN niveaux n ON c.niveau_id = n.id WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($nom, $niveau_id, $capacite_max) {
        $stmt = $this->db->prepare("INSERT INTO classes (nom, niveau_id, capacite_max) VALUES (?, ?, ?)");
        return $stmt->execute([$nom, $niveau_id, $capacite_max]);
    }

    public function update($id, $nom, $niveau_id, $capacite_max) {
        $stmt = $this->db->prepare("UPDATE classes SET nom = ?, niveau_id = ?, capacite_max = ? WHERE id = ?");
        return $stmt->execute([$nom, $niveau_id, $capacite_max, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM classes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getNiveaux() {
        $stmt = $this->db->query("SELECT * FROM niveaux ORDER BY id");
        return $stmt->fetchAll();
    }

    public function getStudentCount($classId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM students WHERE class_id = ?");
        $stmt->execute([$classId]);
        return $stmt->fetchColumn();
    }

    // Get subject assignments for a class
    public function getAssignments($classId) {
        $stmt = $this->db->prepare("
            SELECT ct.id, ct.matiere_id, ct.teacher_id, m.nom as matiere_nom, m.coefficient, 
                   u.username as teacher_username, u.email as teacher_email
            FROM class_teachers ct
            JOIN matieres m ON ct.matiere_id = m.id
            JOIN teachers t ON ct.teacher_id = t.id
            JOIN users u ON t.user_id = u.id
            WHERE ct.class_id = ?
            ORDER BY m.nom
        ");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    // Assign a teacher to a subject in a class
    public function assignTeacher($classId, $matiereId, $teacherId) {
        // First delete any existing assignment for this class + subject combination to enforce unique key
        $stmt = $this->db->prepare("DELETE FROM class_teachers WHERE class_id = ? AND matiere_id = ?");
        $stmt->execute([$classId, $matiereId]);

        $stmt = $this->db->prepare("INSERT INTO class_teachers (class_id, matiere_id, teacher_id) VALUES (?, ?, ?)");
        return $stmt->execute([$classId, $matiereId, $teacherId]);
    }

    // Remove assignments
    public function deleteAssignment($assignmentId) {
        $stmt = $this->db->prepare("DELETE FROM class_teachers WHERE id = ?");
        return $stmt->execute([$assignmentId]);
    }
}
