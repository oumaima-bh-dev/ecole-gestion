<?php
class Payment extends Model {

    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, s.nom, s.prenom, c.nom as class_name 
            FROM payments p
            JOIN students s ON p.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            ORDER BY p.date_paiement DESC
        ");
        return $stmt->fetchAll();
    }

    public function getStudentPayments($studentId) {
        $stmt = $this->db->prepare("
            SELECT * FROM payments 
            WHERE student_id = ? 
            ORDER BY date_paiement DESC
        ");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, s.nom as student_nom, s.prenom as student_prenom, c.nom as class_name,
                   u.email as parent_email, pa.telephone as parent_tel, up.username as parent_name
            FROM payments p
            JOIN students s ON p.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN parents pa ON s.parent_id = pa.id
            LEFT JOIN users up ON pa.user_id = up.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($studentId, $typePaiement, $montant, $datePaiement) {
        // Generate unique receipt number
        $recuNo = 'REC-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));

        $stmt = $this->db->prepare("
            INSERT INTO payments (student_id, type_paiement, montant, date_paiement, recu_no)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$studentId, $typePaiement, $montant, $datePaiement, $recuNo]);
    }

    public function getFinancialSummary() {
        $stmt = $this->db->query("
            SELECT 
                SUM(montant) as total_collected,
                SUM(CASE WHEN type_paiement = 'Inscription' THEN montant ELSE 0 END) as total_inscription,
                SUM(CASE WHEN type_paiement = 'Mensualite' THEN montant ELSE 0 END) as total_mensualite,
                SUM(CASE WHEN type_paiement = 'Frais divers' THEN montant ELSE 0 END) as total_divers
            FROM payments
        ");
        return $stmt->fetch();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM payments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
