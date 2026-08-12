<?php
class SchoolParent extends Model {

    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, u.username, u.email, u.status 
            FROM parents p
            JOIN users u ON p.user_id = u.id
            ORDER BY u.username
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, u.email, u.status 
            FROM parents p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($username, $email, $password, $telephone, $adresse) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                throw new Exception("Nom d'utilisateur ou email déjà utilisé.");
            }

            // Create user
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'parent')");
            $stmt->execute([$username, $password_hash, $email]);
            $user_id = $this->db->lastInsertId();

            // Create parent profile
            $stmt = $this->db->prepare("
                INSERT INTO parents (user_id, telephone, adresse)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$user_id, $telephone, $adresse]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $telephone, $adresse, $email, $status) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT user_id FROM parents WHERE id = ?");
            $stmt->execute([$id]);
            $parent = $stmt->fetch();
            if (!$parent) {
                throw new Exception("Parent introuvable.");
            }

            // Update user
            $stmt = $this->db->prepare("UPDATE users SET email = ?, status = ? WHERE id = ?");
            $stmt->execute([$email, $status, $parent['user_id']]);

            // Update profile
            $stmt = $this->db->prepare("
                UPDATE parents 
                SET telephone = ?, adresse = ?
                WHERE id = ?
            ");
            $stmt->execute([$telephone, $adresse, $id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        $stmt = $this->db->prepare("SELECT user_id FROM parents WHERE id = ?");
        $stmt->execute([$id]);
        $parent = $stmt->fetch();
        if ($parent) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$parent['user_id']]);
        }
        return false;
    }
}
