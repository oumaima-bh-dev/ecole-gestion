<?php
class Payment extends Model {

    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, s.nom, s.prenom, c.nom as class_name
            FROM payments p
            JOIN students s ON p.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            ORDER BY p.date_paiement DESC, p.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function getStudentPayments($studentId) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM payments
            WHERE student_id = ?
            ORDER BY date_paiement DESC, id DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, s.nom as student_nom, s.prenom as student_prenom, c.nom as class_name,
                   u.email as parent_email, pa.telephone as parent_tel, up.username as parent_name,
                   COALESCE(sf.total_due, 0) as total_due,
                   COALESCE(ps.total_paid, 0) as total_paid,
                   GREATEST(COALESCE(sf.total_due, 0) - COALESCE(ps.total_paid, 0), 0) as remaining_due
            FROM payments p
            JOIN students s ON p.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN parents pa ON s.parent_id = pa.id
            LEFT JOIN users up ON pa.user_id = up.id
            LEFT JOIN users u ON s.user_id = u.id
            LEFT JOIN student_fees sf ON sf.student_id = s.id
            LEFT JOIN (
                SELECT student_id, SUM(montant) as total_paid
                FROM payments
                GROUP BY student_id
            ) ps ON ps.student_id = s.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($studentId, $typePaiement, $montant, $datePaiement, $modePaiement = 'Espèces', $referencePaiement = '', $notes = '') {
        $this->ensureStudentFee($studentId);
        $recuNo = 'REC-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));

        $stmt = $this->db->prepare("
            INSERT INTO payments (student_id, type_paiement, montant, date_paiement, recu_no, mode_paiement, reference_paiement, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$studentId, $typePaiement, $montant, $datePaiement, $recuNo, $modePaiement, $referencePaiement, $notes]);
    }

    public function updateStudentFee($studentId, $totalDue, $notes = '') {
        $stmt = $this->db->prepare("
            INSERT INTO student_fees (student_id, total_due, notes)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE total_due = VALUES(total_due), notes = VALUES(notes)
        ");
        return $stmt->execute([$studentId, $totalDue, $notes]);
    }

    public function ensureStudentFee($studentId) {
        $stmt = $this->db->prepare("
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
            WHERE s.id = ?
            ON DUPLICATE KEY UPDATE student_id = student_id
        ");
        return $stmt->execute([$studentId]);
    }

    public function getStudentFinancialSituations() {
        $stmt = $this->db->query("
            SELECT s.id as student_id, s.nom, s.prenom, c.nom as class_name, n.nom as niveau_name,
                   COALESCE(sf.total_due, 0) as total_due,
                   COALESCE(sf.notes, '') as fee_notes,
                   COALESCE(SUM(p.montant), 0) as total_paid,
                   GREATEST(COALESCE(sf.total_due, 0) - COALESCE(SUM(p.montant), 0), 0) as remaining_due,
                   CASE
                       WHEN COALESCE(SUM(p.montant), 0) >= COALESCE(sf.total_due, 0) THEN 'Payé'
                       WHEN COALESCE(SUM(p.montant), 0) > 0 THEN 'Partiellement payé'
                       ELSE 'Impayé'
                   END as payment_status
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN niveaux n ON c.niveau_id = n.id
            LEFT JOIN student_fees sf ON sf.student_id = s.id
            LEFT JOIN payments p ON p.student_id = s.id
            GROUP BY s.id, s.nom, s.prenom, c.nom, n.nom, sf.total_due, sf.notes
            ORDER BY c.nom, s.nom, s.prenom
        ");
        return $stmt->fetchAll();
    }

    public function getFinancialSummary() {
        $stmt = $this->db->query("
            SELECT
                COALESCE(SUM(total_due), 0) as total_expected,
                COALESCE(SUM(total_paid), 0) as total_collected,
                COALESCE(SUM(remaining_due), 0) as total_remaining,
                COALESCE(SUM(remaining_due), 0) as total_unpaid,
                SUM(CASE WHEN remaining_due <= 0 THEN 1 ELSE 0 END) as students_paid,
                SUM(CASE WHEN remaining_due > 0 THEN 1 ELSE 0 END) as students_late
            FROM (
                SELECT s.id,
                       COALESCE(sf.total_due, 0) as total_due,
                       COALESCE(SUM(p.montant), 0) as total_paid,
                       GREATEST(COALESCE(sf.total_due, 0) - COALESCE(SUM(p.montant), 0), 0) as remaining_due
                FROM students s
                LEFT JOIN student_fees sf ON sf.student_id = s.id
                LEFT JOIN payments p ON p.student_id = s.id
                GROUP BY s.id, sf.total_due
            ) situation
        ");
        $summary = $stmt->fetch();

        $typeStmt = $this->db->query("
            SELECT
                COALESCE(SUM(montant), 0) as collected_by_type,
                COALESCE(SUM(CASE WHEN type_paiement = 'Inscription' THEN montant ELSE 0 END), 0) as total_inscription,
                COALESCE(SUM(CASE WHEN type_paiement = 'Mensualite' THEN montant ELSE 0 END), 0) as total_mensualite,
                COALESCE(SUM(CASE WHEN type_paiement = 'Frais divers' THEN montant ELSE 0 END), 0) as total_divers
            FROM payments
        ");
        return array_merge($summary ?: [], $typeStmt->fetch() ?: []);
    }

    public function getFinancialByLevel() {
        $stmt = $this->db->query("
            SELECT COALESCE(n.nom, 'Sans niveau') as niveau_name,
                   COALESCE(SUM(COALESCE(sf.total_due, 0)), 0) as total_expected,
                   COALESCE(SUM(COALESCE(ps.total_paid, 0)), 0) as total_collected,
                   COALESCE(SUM(GREATEST(COALESCE(sf.total_due, 0) - COALESCE(ps.total_paid, 0), 0)), 0) as total_remaining
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN niveaux n ON c.niveau_id = n.id
            LEFT JOIN student_fees sf ON sf.student_id = s.id
            LEFT JOIN (
                SELECT student_id, SUM(montant) as total_paid
                FROM payments
                GROUP BY student_id
            ) ps ON ps.student_id = s.id
            GROUP BY n.nom
            ORDER BY FIELD(n.nom, 'Primaire', 'Collège', 'Lycée'), n.nom
        ");
        return $stmt->fetchAll();
    }

    public function getFinancialByClass() {
        $stmt = $this->db->query("
            SELECT COALESCE(c.nom, 'Sans classe') as class_name,
                   COALESCE(n.nom, 'Sans niveau') as niveau_name,
                   COUNT(s.id) as student_count,
                   COALESCE(SUM(COALESCE(sf.total_due, 0)), 0) as total_expected,
                   COALESCE(SUM(COALESCE(ps.total_paid, 0)), 0) as total_collected,
                   COALESCE(SUM(GREATEST(COALESCE(sf.total_due, 0) - COALESCE(ps.total_paid, 0), 0)), 0) as total_remaining
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN niveaux n ON c.niveau_id = n.id
            LEFT JOIN student_fees sf ON sf.student_id = s.id
            LEFT JOIN (
                SELECT student_id, SUM(montant) as total_paid
                FROM payments
                GROUP BY student_id
            ) ps ON ps.student_id = s.id
            GROUP BY c.id, c.nom, n.nom
            ORDER BY n.nom, c.nom
        ");
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM payments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
