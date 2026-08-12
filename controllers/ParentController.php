<?php
class ParentController extends Controller {

    private $parentModel;
    private $studentModel;
    private $noteModel;
    private $absenceModel;
    private $paymentModel;

    public function __construct() {
        parent::__construct();
        // Secure to parents only
        $this->checkAuth(['parent']);

        require_once dirname(__DIR__) . '/models/SchoolParent.php';
        require_once dirname(__DIR__) . '/models/Student.php';
        require_once dirname(__DIR__) . '/models/Note.php';
        require_once dirname(__DIR__) . '/models/Absence.php';
        require_once dirname(__DIR__) . '/models/Payment.php';

        $this->parentModel = new SchoolParent();
        $this->studentModel = new Student();
        $this->noteModel = new Note();
        $this->absenceModel = new Absence();
        $this->paymentModel = new Payment();
    }

    /**
     * Parent space: follow children progress
     */
    public function dashboard() {
        $parentId = $_SESSION['profile_id'];
        $parent = $this->parentModel->getById($parentId);

        // Fetch children
        $children = $this->studentModel->getByParentId($parentId);

        // Pick selected child (default to first child if not specified)
        $selectedChildId = isset($_GET['child_id']) ? intval($_GET['child_id']) : null;
        if (empty($selectedChildId) && !empty($children)) {
            $selectedChildId = $children[0]['id'];
        }

        // Verify child belongs to this parent to prevent security bypass
        $childProfile = null;
        $notes = [];
        $averages = [];
        $generalAverage = null;
        $absences = [];
        $absenceStats = null;
        $payments = [];

        if ($selectedChildId) {
            $isOwnChild = false;
            foreach ($children as $child) {
                if ($child['id'] == $selectedChildId) {
                    $isOwnChild = true;
                    break;
                }
            }

            if ($isOwnChild) {
                $childProfile = $this->studentModel->getById($selectedChildId);
                
                // Fetch student notes and averages
                $notes = $this->noteModel->getStudentNotes($selectedChildId);
                $averages = $this->noteModel->getAveragesByMatiere($selectedChildId);
                $generalAverage = $this->noteModel->getGeneralAverage($selectedChildId);
                
                // Fetch absences
                $absences = $this->absenceModel->getStudentAbsences($selectedChildId);
                $absenceStats = $this->absenceModel->getStats($selectedChildId);

                // Fetch payments
                $payments = $this->paymentModel->getStudentPayments($selectedChildId);
            } else {
                $_SESSION['error'] = "Accès refusé pour cet élève.";
                $this->redirect('parent', 'dashboard');
                exit();
            }
        }

        $this->render('parent/dashboard', [
            'parent' => $parent,
            'children' => $children,
            'selectedChildId' => $selectedChildId,
            'childProfile' => $childProfile,
            'notes' => $notes,
            'averages' => $averages,
            'generalAverage' => $generalAverage,
            'absences' => $absences,
            'absenceStats' => $absenceStats,
            'payments' => $payments
        ]);
    }
}
