<?php
class Note extends Model {

    public function getStudentNotes($studentId) {
        $stmt = $this->db->prepare("
            SELECT n.*, m.nom as matiere_nom, m.coefficient 
            FROM notes n
            JOIN matieres m ON n.matiere_id = m.id
            WHERE n.student_id = ?
            ORDER BY n.date_examen DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getByClassAndMatiere($classId, $matiereId) {
        $stmt = $this->db->prepare("
            SELECT id as student_id, nom, prenom
            FROM students
            WHERE class_id = ?
            ORDER BY nom, prenom
        ");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public function saveNote($studentId, $matiereId, $typeExamen, $noteValue, $dateExamen) {
        // Check if grade already exists for this specific combination
        $stmt = $this->db->prepare("
            SELECT id FROM notes 
            WHERE student_id = ? AND matiere_id = ? AND type_examen = ? AND date_examen = ?
        ");
        $stmt->execute([$studentId, $matiereId, $typeExamen, $dateExamen]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE notes SET note = ? WHERE id = ?");
            return $stmt->execute([$noteValue, $existing['id']]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO notes (student_id, matiere_id, type_examen, note, date_examen)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$studentId, $matiereId, $typeExamen, $noteValue, $dateExamen]);
        }
    }

    /**
     * Calculates averages per subject for a student
     */
    public function getAveragesByMatiere($studentId) {
        $stmt = $this->db->prepare("
            SELECT m.id as matiere_id, m.nom as matiere_nom, m.coefficient,
                   AVG(n.note) as moyenne_matiere,
                   COUNT(n.id) as total_evaluations
            FROM matieres m
            LEFT JOIN notes n ON m.id = n.matiere_id AND n.student_id = ?
            GROUP BY m.id, m.nom, m.coefficient
            HAVING total_evaluations > 0
            ORDER BY m.nom
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    /**
     * Calculates general weighted average
     */
    public function getGeneralAverage($studentId) {
        $averages = $this->getAveragesByMatiere($studentId);
        if (empty($averages)) {
            return null;
        }

        $totalPoints = 0;
        $totalCoefficients = 0;

        foreach ($averages as $avg) {
            $totalPoints += $avg['moyenne_matiere'] * $avg['coefficient'];
            $totalCoefficients += $avg['coefficient'];
        }

        return $totalCoefficients > 0 ? round($totalPoints / $totalCoefficients, 2) : 0;
    }

    public function deleteNote($id) {
        $stmt = $this->db->prepare("DELETE FROM notes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
