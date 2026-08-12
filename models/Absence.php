<?php
class Absence extends Model {

    public function getStudentAbsences($studentId) {
        $stmt = $this->db->prepare("
            SELECT * FROM absences 
            WHERE student_id = ? 
            ORDER BY date_absence DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getByClassAndDate($classId, $date) {
        $stmt = $this->db->prepare("
            SELECT s.id as student_id, s.nom, s.prenom, a.id as absence_id, a.statut, a.justifie, a.motif
            FROM students s
            LEFT JOIN absences a ON s.id = a.student_id AND a.date_absence = ?
            WHERE s.class_id = ?
            ORDER BY s.nom, s.prenom
        ");
        $stmt->execute([$date, $classId]);
        return $stmt->fetchAll();
    }

    public function saveAbsence($studentId, $dateAbsence, $statut, $justifie = 0, $motif = '') {
        // Check if record exists for this date and student
        $stmt = $this->db->prepare("SELECT id FROM absences WHERE student_id = ? AND date_absence = ?");
        $stmt->execute([$studentId, $dateAbsence]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE absences 
                SET statut = ?, justifie = ?, motif = ? 
                WHERE id = ?
            ");
            return $stmt->execute([$statut, $justifie, $motif, $existing['id']]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO absences (student_id, date_absence, statut, justifie, motif)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$studentId, $dateAbsence, $statut, $justifie, $motif]);
        }
    }

    public function getStats($studentId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'absent' THEN 1 ELSE 0 END) as absences,
                SUM(CASE WHEN statut = 'absent' AND justifie = 1 THEN 1 ELSE 0 END) as justified,
                SUM(CASE WHEN statut = 'absent' AND justifie = 0 THEN 1 ELSE 0 END) as unjustified
            FROM absences
            WHERE student_id = ?
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetch();
    }

    public function updateJustification($absenceId, $justifie, $motif) {
        $stmt = $this->db->prepare("UPDATE absences SET justifie = ?, motif = ? WHERE id = ?");
        return $stmt->execute([$justifie, $motif, $absenceId]);
    }
}
