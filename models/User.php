<?php
class User extends Model {

    /**
     * Authenticate user with username and password
     */
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                return 'inactive';
            }
            // Remove password hash from array before returning
            unset($user['password']);
            return $user;
        }
        return false;
    }

    /**
     * Get specific user profile info based on role
     */
    public function getProfileData($userId, $role) {
        switch ($role) {
            case 'teacher':
                $stmt = $this->db->prepare("SELECT t.*, u.username, u.email FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.id = ?");
                $stmt->execute([$userId]);
                return $stmt->fetch();
            case 'parent':
                $stmt = $this->db->prepare("SELECT p.*, u.username, u.email FROM parents p JOIN users u ON p.user_id = u.id WHERE u.id = ?");
                $stmt->execute([$userId]);
                return $stmt->fetch();
            case 'student':
                $stmt = $this->db->prepare("
                    SELECT s.*, u.username, u.email, c.nom as class_name 
                    FROM students s 
                    JOIN users u ON s.user_id = u.id 
                    LEFT JOIN classes c ON s.class_id = c.id 
                    WHERE u.id = ?
                ");
                $stmt->execute([$userId]);
                return $stmt->fetch();
            default:
                $stmt = $this->db->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                return $stmt->fetch();
        }
    }
}
