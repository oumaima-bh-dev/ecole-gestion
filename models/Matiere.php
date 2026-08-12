<?php
class Matiere extends Model {

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM matieres ORDER BY nom");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM matieres WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($nom, $coefficient) {
        $stmt = $this->db->prepare("INSERT INTO matieres (nom, coefficient) VALUES (?, ?)");
        return $stmt->execute([$nom, $coefficient]);
    }

    public function update($id, $nom, $coefficient) {
        $stmt = $this->db->prepare("UPDATE matieres SET nom = ?, coefficient = ? WHERE id = ?");
        return $stmt->execute([$nom, $coefficient, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM matieres WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
