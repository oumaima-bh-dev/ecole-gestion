<?php
class TeacherController extends Controller {

    private $teacherModel;
    private $studentModel;
    private $noteModel;
    private $absenceModel;
    private $classModel;

    public function __construct() {
        parent::__construct();
        // Secure page to teachers only
        $this->checkAuth(['teacher']);

        require_once dirname(__DIR__) . '/models/Teacher.php';
        require_once dirname(__DIR__) . '/models/Student.php';
        require_once dirname(__DIR__) . '/models/Note.php';
        require_once dirname(__DIR__) . '/models/Absence.php';
        require_once dirname(__DIR__) . '/models/Classe.php';

        $this->teacherModel = new Teacher();
        $this->studentModel = new Student();
        $this->noteModel = new Note();
        $this->absenceModel = new Absence();
        $this->classModel = new Classe();
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
}
