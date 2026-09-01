<?php
class DocumentPedagogique extends Model {

    public function createCategory($teacherId, $nom) {
        $stmt = $this->db->prepare("
            INSERT INTO document_categories (teacher_id, nom)
            VALUES (?, ?)
        ");
        return $stmt->execute([$teacherId, $nom]);
    }

    public function getCategoriesByTeacher($teacherId) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM document_categories
            WHERE teacher_id = ?
            ORDER BY nom
        ");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }

    public function getCategoriesForClass($classId) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT dc.*
            FROM document_categories dc
            JOIN pedagogical_documents pd ON pd.category_id = dc.id
            WHERE pd.class_id = ? AND pd.status = 'active'
            ORDER BY dc.nom
        ");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public function teacherCanAccessClass($teacherId, $classId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM class_teachers
            WHERE teacher_id = ? AND class_id = ?
        ");
        $stmt->execute([$teacherId, $classId]);
        return $stmt->fetchColumn() > 0;
    }

    public function categoryBelongsToTeacher($categoryId, $teacherId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM document_categories
            WHERE id = ? AND teacher_id = ?
        ");
        $stmt->execute([$categoryId, $teacherId]);
        return $stmt->fetchColumn() > 0;
    }

    public function createDocument($teacherId, $categoryId, $classId, $titre, $description, $fileName, $originalName, $filePath, $mimeType, $fileSize) {
        if (!$this->teacherCanAccessClass($teacherId, $classId)) {
            throw new Exception("Vous ne pouvez publier que pour une classe qui vous est affectée.");
        }

        if (!$this->categoryBelongsToTeacher($categoryId, $teacherId)) {
            throw new Exception("Catégorie invalide.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO pedagogical_documents
                (teacher_id, category_id, class_id, titre, description, file_name, original_name, file_path, mime_type, file_size)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$teacherId, $categoryId, $classId, $titre, $description, $fileName, $originalName, $filePath, $mimeType, $fileSize]);
    }

    public function updateDocument($id, $teacherId, $categoryId, $classId, $titre, $description, $fileData = null) {
        if (!$this->teacherCanAccessClass($teacherId, $classId)) {
            throw new Exception("Vous ne pouvez publier que pour une classe qui vous est affectée.");
        }

        if (!$this->categoryBelongsToTeacher($categoryId, $teacherId)) {
            throw new Exception("Catégorie invalide.");
        }

        if ($fileData) {
            $stmt = $this->db->prepare("
                UPDATE pedagogical_documents
                SET category_id = ?, class_id = ?, titre = ?, description = ?,
                    file_name = ?, original_name = ?, file_path = ?, mime_type = ?, file_size = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND teacher_id = ?
            ");
            return $stmt->execute([
                $categoryId,
                $classId,
                $titre,
                $description,
                $fileData['file_name'],
                $fileData['original_name'],
                $fileData['file_path'],
                $fileData['mime_type'],
                $fileData['file_size'],
                $id,
                $teacherId
            ]);
        }

        $stmt = $this->db->prepare("
            UPDATE pedagogical_documents
            SET category_id = ?, class_id = ?, titre = ?, description = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND teacher_id = ?
        ");
        return $stmt->execute([$categoryId, $classId, $titre, $description, $id, $teacherId]);
    }

    public function toggleStatus($id, $teacherId) {
        $stmt = $this->db->prepare("
            UPDATE pedagogical_documents
            SET status = IF(status = 'active', 'inactive', 'active'), updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND teacher_id = ?
        ");
        return $stmt->execute([$id, $teacherId]);
    }

    public function deleteDocument($id, $teacherId) {
        $document = $this->getTeacherDocument($id, $teacherId);
        if (!$document) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM pedagogical_documents WHERE id = ? AND teacher_id = ?");
        $deleted = $stmt->execute([$id, $teacherId]);

        if ($deleted && !empty($document['file_path'])) {
            $absolutePath = dirname(__DIR__) . '/' . $document['file_path'];
            if (is_file($absolutePath)) {
                unlink($absolutePath);
            }
        }

        return $deleted;
    }

    public function getTeacherDocuments($teacherId) {
        $stmt = $this->db->prepare("
            SELECT pd.*, dc.nom as category_name, c.nom as class_name
            FROM pedagogical_documents pd
            JOIN document_categories dc ON pd.category_id = dc.id
            JOIN classes c ON pd.class_id = c.id
            WHERE pd.teacher_id = ?
            ORDER BY pd.created_at DESC
        ");
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }

    public function getTeacherDocument($id, $teacherId) {
        $stmt = $this->db->prepare("
            SELECT pd.*, dc.nom as category_name, c.nom as class_name
            FROM pedagogical_documents pd
            JOIN document_categories dc ON pd.category_id = dc.id
            JOIN classes c ON pd.class_id = c.id
            WHERE pd.id = ? AND pd.teacher_id = ?
        ");
        $stmt->execute([$id, $teacherId]);
        return $stmt->fetch();
    }

    public function getStudentDocuments($classId, $categoryId = null) {
        $sql = "
            SELECT pd.*, dc.nom as category_name, c.nom as class_name, u.username as teacher_name
            FROM pedagogical_documents pd
            JOIN document_categories dc ON pd.category_id = dc.id
            JOIN classes c ON pd.class_id = c.id
            JOIN teachers t ON pd.teacher_id = t.id
            JOIN users u ON t.user_id = u.id
            WHERE pd.class_id = ? AND pd.status = 'active'
        ";
        $params = [$classId];

        if ($categoryId) {
            $sql .= " AND pd.category_id = ?";
            $params[] = $categoryId;
        }

        $sql .= " ORDER BY dc.nom, pd.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getActiveDocumentForClass($id, $classId) {
        $stmt = $this->db->prepare("
            SELECT pd.*
            FROM pedagogical_documents pd
            WHERE pd.id = ? AND pd.class_id = ? AND pd.status = 'active'
        ");
        $stmt->execute([$id, $classId]);
        return $stmt->fetch();
    }
}
