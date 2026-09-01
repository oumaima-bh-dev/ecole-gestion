<?php
class StudentController extends Controller {

    private $studentModel;
    private $documentModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth(['student']);

        require_once dirname(__DIR__) . '/models/Student.php';
        require_once dirname(__DIR__) . '/models/DocumentPedagogique.php';

        $this->studentModel = new Student();
        $this->documentModel = new DocumentPedagogique();
    }

    public function documents() {
        $studentId = $_SESSION['profile_id'];
        $student = $this->studentModel->getById($studentId);
        $classId = !empty($student['class_id']) ? intval($student['class_id']) : null;
        $selectedCategoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

        $categories = [];
        $documents = [];

        if ($classId) {
            $categories = $this->documentModel->getCategoriesForClass($classId);
            $validCategoryIds = array_column($categories, 'id');
            if ($selectedCategoryId && !in_array($selectedCategoryId, $validCategoryIds)) {
                $selectedCategoryId = null;
            }
            $documents = $this->documentModel->getStudentDocuments($classId, $selectedCategoryId);
        }

        $this->render('student/documents', [
            'student' => $student,
            'categories' => $categories,
            'documents' => $documents,
            'selectedCategoryId' => $selectedCategoryId
        ]);
    }

    public function downloadDocument() {
        $this->serveStudentDocument('attachment');
    }

    public function viewDocument() {
        $this->serveStudentDocument('inline');
    }

    private function serveStudentDocument($disposition) {
        $studentId = $_SESSION['profile_id'];
        $student = $this->studentModel->getById($studentId);
        $classId = !empty($student['class_id']) ? intval($student['class_id']) : null;
        $documentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$classId || !$documentId) {
            $_SESSION['error'] = "Document introuvable.";
            $this->redirect('student', 'documents');
        }

        $document = $this->documentModel->getActiveDocumentForClass($documentId, $classId);
        if (!$document) {
            $_SESSION['error'] = "Accès non autorisé ou document indisponible.";
            $this->redirect('student', 'documents');
        }

        $absolutePath = dirname(__DIR__) . '/' . $document['file_path'];
        if (!is_file($absolutePath)) {
            $_SESSION['error'] = "Fichier introuvable.";
            $this->redirect('student', 'documents');
        }

        header('Content-Type: ' . ($document['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($document['original_name']) . '"');
        header('Content-Length: ' . filesize($absolutePath));
        readfile($absolutePath);
        exit();
    }
}
