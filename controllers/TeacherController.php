<?php
class TeacherController extends Controller {

    private $teacherModel;
    private $studentModel;
    private $noteModel;
    private $absenceModel;
    private $classModel;
    private $documentModel;

    public function __construct() {
        parent::__construct();
        // Secure page to teachers only
        $this->checkAuth(['teacher']);

        require_once dirname(__DIR__) . '/models/Teacher.php';
        require_once dirname(__DIR__) . '/models/Student.php';
        require_once dirname(__DIR__) . '/models/Note.php';
        require_once dirname(__DIR__) . '/models/Absence.php';
        require_once dirname(__DIR__) . '/models/Classe.php';
        require_once dirname(__DIR__) . '/models/DocumentPedagogique.php';

        $this->teacherModel = new Teacher();
        $this->studentModel = new Student();
        $this->noteModel = new Note();
        $this->absenceModel = new Absence();
        $this->classModel = new Classe();
        $this->documentModel = new DocumentPedagogique();
    }

    /**
     * Teacher Overview Dashboard
     */
    public function dashboard() {
        $teacherId = $_SESSION['profile_id'];
        $teacher = $this->teacherModel->getById($teacherId);
        $assignments = $this->teacherModel->getAssignedClasses($teacherId);

        $this->render('teacher/dashboard', [
            'teacher' => $teacher,
            'assignments' => $assignments
        ]);
    }

    /**
     * Teacher Classes Viewer
     */
    public function classes() {
        $teacherId = $_SESSION['profile_id'];
        $assignments = $this->teacherModel->getAssignedClasses($teacherId);

        $selectedClassId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;

        // Verify teacher belongs to this class
        $isValidClass = false;
        if ($selectedClassId) {
            foreach ($assignments as $asg) {
                if ($asg['class_id'] == $selectedClassId) {
                    $isValidClass = true;
                    break;
                }
            }
        }

        $students = [];
        if ($isValidClass) {
            $students = $this->studentModel->getByClassId($selectedClassId);
        }

        $this->render('teacher/classes', [
            'assignments' => $assignments,
            'selectedClassId' => $selectedClassId,
            'students' => $students,
            'isValidClass' => $isValidClass
        ]);
    }

    /**
     * Saisie des notes
     */
    public function grades() {
        $teacherId = $_SESSION['profile_id'];
        $assignments = $this->teacherModel->getAssignedClasses($teacherId);

        $selectedClassId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;
        $selectedMatiereId = isset($_GET['matiere_id']) ? intval($_GET['matiere_id']) : null;

        // Verify that this class+matiere is indeed assigned to this teacher to prevent security bypass
        $isValidAssignment = false;
        if ($selectedClassId && $selectedMatiereId) {
            foreach ($assignments as $asg) {
                if ($asg['class_id'] == $selectedClassId && $asg['matiere_id'] == $selectedMatiereId) {
                    $isValidAssignment = true;
                    break;
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Jeton CSRF invalide.";
                $this->redirect('teacher', 'grades', ['class_id' => $selectedClassId, 'matiere_id' => $selectedMatiereId]);
            }

            if (!$isValidAssignment) {
                $_SESSION['error'] = "Action non autorisée sur cette classe.";
                $this->redirect('teacher', 'grades');
            }

            $typeExamen = $this->sanitize($_POST['type_examen']);
            $dateExamen = $_POST['date_examen'];
            $grades = $_POST['notes'] ?? []; // student_id => note value

            try {
                foreach ($grades as $studentId => $value) {
                    if ($value === '') continue; // Skip empty fields
                    $noteVal = floatval($value);
                    if ($noteVal < 0 || $noteVal > 20) {
                        throw new Exception("Les notes doivent être comprises entre 0 et 20.");
                    }
                    $this->noteModel->saveNote($studentId, $selectedMatiereId, $typeExamen, $noteVal, $dateExamen);
                }
                $_SESSION['success'] = "Notes enregistrées avec succès.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur: " . $e->getMessage();
            }

            $this->redirect('teacher', 'grades', ['class_id' => $selectedClassId, 'matiere_id' => $selectedMatiereId]);
        }

        // Fetch student list and existing notes if assignment is valid
        $students = [];
        if ($isValidAssignment) {
            $students = $this->noteModel->getByClassAndMatiere($selectedClassId, $selectedMatiereId);
        }

        $this->render('teacher/grades', [
            'assignments' => $assignments,
            'selectedClassId' => $selectedClassId,
            'selectedMatiereId' => $selectedMatiereId,
            'students' => $students,
            'isValidAssignment' => $isValidAssignment
        ]);
    }

    /**
     * Attendance entry
     */
    public function attendance() {
        $teacherId = $_SESSION['profile_id'];
        $assignments = $this->teacherModel->getAssignedClasses($teacherId);

        $selectedClassId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;
        $dateAbsence = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

        // Verify teacher belongs to this class
        $isValidClass = false;
        if ($selectedClassId) {
            foreach ($assignments as $asg) {
                if ($asg['class_id'] == $selectedClassId) {
                    $isValidClass = true;
                    break;
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Jeton CSRF invalide.";
                $this->redirect('teacher', 'attendance', ['class_id' => $selectedClassId, 'date' => $dateAbsence]);
            }

            if (!$isValidClass) {
                $_SESSION['error'] = "Action non autorisée pour cette classe.";
                $this->redirect('teacher', 'attendance');
            }

            $attendanceData = $_POST['attendance'] ?? []; // student_id => 'present'/'absent'
            $motifs = $_POST['motifs'] ?? []; // student_id => motif text
            $justified = $_POST['justified'] ?? []; // student_id => 1 or 0

            try {
                foreach ($attendanceData as $studentId => $status) {
                    $motif = isset($motifs[$studentId]) ? $this->sanitize($motifs[$studentId]) : '';
                    $just = isset($justified[$studentId]) ? intval($justified[$studentId]) : 0;
                    $this->absenceModel->saveAbsence($studentId, $dateAbsence, $status, $just, $motif);
                }
                $_SESSION['success'] = "Feuille d'absence enregistrée.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur : " . $e->getMessage();
            }

            $this->redirect('teacher', 'attendance', ['class_id' => $selectedClassId, 'date' => $dateAbsence]);
        }

        $students = [];
        if ($isValidClass) {
            $students = $this->absenceModel->getByClassAndDate($selectedClassId, $dateAbsence);
        }

        $this->render('teacher/attendance', [
            'assignments' => $assignments,
            'selectedClassId' => $selectedClassId,
            'dateAbsence' => $dateAbsence,
            'students' => $students,
            'isValidClass' => $isValidClass
        ]);
    }

    /**
     * Gestion des documents pédagogiques
     */
    public function documents() {
        $teacherId = $_SESSION['profile_id'];
        $assignments = $this->teacherModel->getAssignedClasses($teacherId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!$this->validateCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Jeton CSRF invalide.";
                $this->redirect('teacher', 'documents');
            }

            $action = $_POST['action'];

            try {
                if ($action === 'add_category') {
                    $nom = $this->sanitize($_POST['nom']);
                    if ($nom === '') {
                        throw new Exception("Le nom de la catégorie est obligatoire.");
                    }
                    $this->documentModel->createCategory($teacherId, $nom);
                    $_SESSION['success'] = "Catégorie ajoutée.";
                } elseif ($action === 'add_document') {
                    $categoryId = intval($_POST['category_id']);
                    $classId = intval($_POST['class_id']);
                    $titre = $this->sanitize($_POST['titre']);
                    $description = $this->sanitize($_POST['description'] ?? '');
                    $this->validateTeacherDocumentScope($teacherId, $categoryId, $classId);
                    $fileData = $this->handleDocumentUpload('document_file');

                    $this->documentModel->createDocument(
                        $teacherId,
                        $categoryId,
                        $classId,
                        $titre,
                        $description,
                        $fileData['file_name'],
                        $fileData['original_name'],
                        $fileData['file_path'],
                        $fileData['mime_type'],
                        $fileData['file_size']
                    );
                    $_SESSION['success'] = "Document ajouté avec succès.";
                } elseif ($action === 'edit_document') {
                    $id = intval($_POST['id']);
                    $categoryId = intval($_POST['category_id']);
                    $classId = intval($_POST['class_id']);
                    $titre = $this->sanitize($_POST['titre']);
                    $description = $this->sanitize($_POST['description'] ?? '');
                    $oldDocument = $this->documentModel->getTeacherDocument($id, $teacherId);
                    if (!$oldDocument) {
                        throw new Exception("Document introuvable.");
                    }
                    $this->validateTeacherDocumentScope($teacherId, $categoryId, $classId);

                    $fileData = null;
                    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $fileData = $this->handleDocumentUpload('document_file');
                    }

                    $this->documentModel->updateDocument($id, $teacherId, $categoryId, $classId, $titre, $description, $fileData);
                    if ($fileData && !empty($oldDocument['file_path'])) {
                        $oldPath = dirname(__DIR__) . '/' . $oldDocument['file_path'];
                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $_SESSION['success'] = "Document modifié.";
                } elseif ($action === 'toggle_document') {
                    $id = intval($_POST['id']);
                    $this->documentModel->toggleStatus($id, $teacherId);
                    $_SESSION['success'] = "Statut du document mis à jour.";
                } elseif ($action === 'delete_document') {
                    $id = intval($_POST['id']);
                    $this->documentModel->deleteDocument($id, $teacherId);
                    $_SESSION['success'] = "Document supprimé définitivement.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur: " . $e->getMessage();
            }

            $this->redirect('teacher', 'documents');
        }

        $this->render('teacher/documents', [
            'assignments' => $assignments,
            'categories' => $this->documentModel->getCategoriesByTeacher($teacherId),
            'documents' => $this->documentModel->getTeacherDocuments($teacherId)
        ]);
    }

    public function downloadDocument() {
        $this->serveTeacherDocument('attachment');
    }

    public function viewDocument() {
        $this->serveTeacherDocument('inline');
    }

    private function serveTeacherDocument($disposition) {
        $teacherId = $_SESSION['profile_id'];
        $documentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $document = $this->documentModel->getTeacherDocument($documentId, $teacherId);

        if (!$document) {
            $_SESSION['error'] = "Document introuvable.";
            $this->redirect('teacher', 'documents');
        }

        $absolutePath = dirname(__DIR__) . '/' . $document['file_path'];
        if (!is_file($absolutePath)) {
            $_SESSION['error'] = "Fichier introuvable.";
            $this->redirect('teacher', 'documents');
        }

        header('Content-Type: ' . ($document['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($document['original_name']) . '"');
        header('Content-Length: ' . filesize($absolutePath));
        readfile($absolutePath);
        exit();
    }

    private function validateTeacherDocumentScope($teacherId, $categoryId, $classId) {
        if (!$this->documentModel->teacherCanAccessClass($teacherId, $classId)) {
            throw new Exception("Vous ne pouvez publier que pour une classe qui vous est affectée.");
        }

        if (!$this->documentModel->categoryBelongsToTeacher($categoryId, $teacherId)) {
            throw new Exception("Catégorie invalide.");
        }
    }

    private function handleDocumentUpload($fieldName) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Veuillez sélectionner un fichier valide.");
        }

        $file = $_FILES[$fieldName];
        $maxSize = 10 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            throw new Exception("Le fichier ne doit pas dépasser 10 Mo.");
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Type de fichier non autorisé.");
        }

        $uploadDir = dirname(__DIR__) . '/uploads/documents';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $safeName = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $uploadDir . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Impossible d'enregistrer le fichier.");
        }

        return [
            'file_name' => $safeName,
            'original_name' => basename($file['name']),
            'file_path' => 'uploads/documents/' . $safeName,
            'mime_type' => $file['type'] ?? null,
            'file_size' => intval($file['size'])
        ];
    }
}
