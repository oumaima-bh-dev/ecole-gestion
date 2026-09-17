<?php
class AdminController extends Controller {

    private $classModel;
    private $studentModel;
    private $teacherModel;
    private $parentModel;
    private $matiereModel;
    private $paymentModel;

    public function __construct() {
        parent::__construct();
        // Protect all actions to Admin only
        $this->checkAuth(['admin']);

        // Load models
        require_once dirname(__DIR__) . '/models/Classe.php';
        require_once dirname(__DIR__) . '/models/Student.php';
        require_once dirname(__DIR__) . '/models/Teacher.php';
        require_once dirname(__DIR__) . '/models/SchoolParent.php';
        require_once dirname(__DIR__) . '/models/Matiere.php';
        require_once dirname(__DIR__) . '/models/Payment.php';

        $this->classModel = new Classe();
        $this->studentModel = new Student();
        $this->teacherModel = new Teacher();
        $this->parentModel = new SchoolParent();
        $this->matiereModel = new Matiere();
        $this->paymentModel = new Payment();
    }

    /**
     * Dashboard view
     */
    public function dashboard() {
        $stats = [
            'students' => count($this->studentModel->getAll()),
            'teachers' => count($this->teacherModel->getAll()),
            'parents'  => count($this->parentModel->getAll()),
            'classes'  => count($this->classModel->getAll())
        ];

        $finances = $this->paymentModel->getFinancialSummary();
        $recentPayments = array_slice($this->paymentModel->getAll(), 0, 5);

        $this->render('admin/dashboard', [
            'stats' => $stats,
            'finances' => $finances,
            'recentPayments' => $recentPayments
        ]);
    }

    /**
     * Class, Subject and Assignment Management
     */
    public function classes() {
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!$this->validateCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Jeton CSRF invalide.";
                $this->redirect('admin', 'classes');
            }

            $action = $_POST['action'];

            try {
                if ($action === 'add_class') {
                    $nom = $this->sanitize($_POST['nom']);
                    $niveau_id = intval($_POST['niveau_id']);
                    $capacite = intval($_POST['capacite_max']);
                    $this->classModel->create($nom, $niveau_id, $capacite);
                    $_SESSION['success'] = "Classe ajoutée avec succès.";
                } elseif ($action === 'delete_class') {
                    $id = intval($_POST['id']);
                    $this->classModel->delete($id);
                    $_SESSION['success'] = "Classe supprimée.";
                } elseif ($action === 'add_subject') {
                    $nom = $this->sanitize($_POST['nom']);
                    $coef = intval($_POST['coefficient']);
                    $this->matiereModel->create($nom, $coef);
                    $_SESSION['success'] = "Matière ajoutée.";
                } elseif ($action === 'delete_subject') {
                    $id = intval($_POST['id']);
                    $this->matiereModel->delete($id);
                    $_SESSION['success'] = "Matière supprimée.";
                } elseif ($action === 'assign_teacher') {
                    $class_id = intval($_POST['class_id']);
                    $matiere_id = intval($_POST['matiere_id']);
                    $teacher_id = intval($_POST['teacher_id']);
                    $this->classModel->assignTeacher($class_id, $matiere_id, $teacher_id);
                    $_SESSION['success'] = "Affectation de l'enseignant réussie.";
                } elseif ($action === 'delete_assignment') {
                    $id = intval($_POST['id']);
                    $this->classModel->deleteAssignment($id);
                    $_SESSION['success'] = "Affectation annulée.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur: " . $e->getMessage();
            }

            $this->redirect('admin', 'classes');
        }

        // Selected class for view assignments details
        $selectedClassId = isset($_GET['class_id']) ? intval($_GET['class_id']) : null;
        $assignments = [];
        if ($selectedClassId) {
            $assignments = $this->classModel->getAssignments($selectedClassId);
        }

        $this->render('admin/classes', [
            'classes' => $this->classModel->getAll(),
            'niveaux' => $this->classModel->getNiveaux(),
            'subjects' => $this->matiereModel->getAll(),
            'teachers' => $this->teacherModel->getAll(),
            'selectedClassId' => $selectedClassId,
            'assignments' => $assignments
        ]);
    }

    /**
     * Student & Parent CRUDs
     */
    public function students() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!$this->validateCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Jeton CSRF invalide.";
                $this->redirect('admin', 'students');
            }

            $action = $_POST['action'];

            try {
                if ($action === 'add_student') {
                    $username = $this->sanitize($_POST['username']);
                    $email = $this->sanitize($_POST['email']);
                    $password = $_POST['password'];
                    $class_id = !empty($_POST['class_id']) ? intval($_POST['class_id']) : null;
                    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
                    $nom = $this->sanitize($_POST['nom']);
                    $prenom = $this->sanitize($_POST['prenom']);
                    $dob = $_POST['date_naissance'];
                    $sexe = $_POST['sexe'];
                    $adresse = $this->sanitize($_POST['adresse']);

                    $this->studentModel->create($username, $email, $password, $class_id, $parent_id, $nom, $prenom, $dob, $sexe, $adresse);
                    $_SESSION['success'] = "Élève créé avec succès.";
                } elseif ($action === 'edit_student') {
                    $id = intval($_POST['id']);
                    $class_id = !empty($_POST['class_id']) ? intval($_POST['class_id']) : null;
                    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
                    $nom = $this->sanitize($_POST['nom']);
                    $prenom = $this->sanitize($_POST['prenom']);
                    $dob = $_POST['date_naissance'];
                    $sexe = $_POST['sexe'];
                    $adresse = $this->sanitize($_POST['adresse']);
                    $email = $this->sanitize($_POST['email']);
                    $status = $_POST['status'];

                    $this->studentModel->update($id, $class_id, $parent_id, $nom, $prenom, $dob, $sexe, $adresse, $email, $status);
                    $_SESSION['success'] = "Profil élève mis à jour.";
                } elseif ($action === 'delete_student') {
                    $id = intval($_POST['id']);
                    $this->studentModel->delete($id);
                    $_SESSION['success'] = "Élève supprimé.";
                } elseif ($action === 'add_parent') {
                    $username = $this->sanitize($_POST['username']);
                    $email = $this->sanitize($_POST['email']);
                    $password = $_POST['password'];
                    $tel = $this->sanitize($_POST['telephone']);
                    $adresse = $this->sanitize($_POST['adresse']);

                    $this->parentModel->create($username, $email, $password, $tel, $adresse);
                    $_SESSION['success'] = "Compte Parent créé avec succès.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur: " . $e->getMessage();
            }

            $this->redirect('admin', 'students');
        }

        $this->render('admin/students', [
            'students' => $this->studentModel->getAll(),
            'classes' => $this->classModel->getAll(),
            'parents' => $this->parentModel->getAll()
        ]);
    }

    /**
     * Teacher CRUD
     */
    public function teachers() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!$this->validateCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Jeton CSRF invalide.";
                $this->redirect('admin', 'teachers');
            }

            $action = $_POST['action'];

            try {
                if ($action === 'add_teacher') {
                    $username = $this->sanitize($_POST['username']);
                    $email = $this->sanitize($_POST['email']);
                    $password = $_POST['password'];
                    $tel = $this->sanitize($_POST['telephone']);
                    $diplome = $this->sanitize($_POST['diplome']);
                    $date_embauche = $_POST['date_embauche'];

                    $this->teacherModel->create($username, $email, $password, $tel, $diplome, $date_embauche);
                    $_SESSION['success'] = "Compte Enseignant créé.";
                } elseif ($action === 'edit_teacher') {
                    $id = intval($_POST['id']);
                    $tel = $this->sanitize($_POST['telephone']);
                    $diplome = $this->sanitize($_POST['diplome']);
                    $date_embauche = $_POST['date_embauche'];
                    $email = $this->sanitize($_POST['email']);
                    $status = $_POST['status'];

                    $this->teacherModel->update($id, $tel, $diplome, $date_embauche, $email, $status);
                    $_SESSION['success'] = "Profil Enseignant mis à jour.";
                } elseif ($action === 'delete_teacher') {
                    $id = intval($_POST['id']);
                    $this->teacherModel->delete($id);
                    $_SESSION['success'] = "Enseignant supprimé.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur: " . $e->getMessage();
            }

            $this->redirect('admin', 'teachers');
        }

        $this->render('admin/teachers', [
            'teachers' => $this->teacherModel->getAll()
        ]);
    }

    /**
     * Payment Operations
     */
    public function payments() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!$this->validateCSRF($_POST['csrf_token'])) {
                $_SESSION['error'] = "Jeton CSRF invalide.";
                $this->redirect('admin', 'payments');
            }

            $action = $_POST['action'];

            try {
                if ($action === 'add_payment') {
                    $student_id = intval($_POST['student_id']);
                    $type = $_POST['type_paiement'];
                    $montant = floatval($_POST['montant']);
                    $date = $_POST['date_paiement'];
                    $mode = $this->sanitize($_POST['mode_paiement'] ?? 'Espèces');
                    $reference = $this->sanitize($_POST['reference_paiement'] ?? '');
                    $notes = $this->sanitize($_POST['notes'] ?? '');

                    $this->paymentModel->create($student_id, $type, $montant, $date, $mode, $reference, $notes);
                    $_SESSION['success'] = "Paiement enregistré avec succès.";
                } elseif ($action === 'update_student_fee') {
                    $student_id = intval($_POST['student_id']);
                    $total_due = floatval($_POST['total_due']);
                    $notes = $this->sanitize($_POST['fee_notes'] ?? '');
                    if ($total_due < 0) {
                        throw new Exception("Le montant total à payer ne peut pas être négatif.");
                    }
                    $this->paymentModel->updateStudentFee($student_id, $total_due, $notes);
                    $_SESSION['success'] = "Situation financière de l'élève mise à jour.";
                } elseif ($action === 'delete_payment') {
                    $id = intval($_POST['id']);
                    $this->paymentModel->delete($id);
                    $_SESSION['success'] = "Paiement supprimé.";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Erreur: " . $e->getMessage();
            }

            $this->redirect('admin', 'payments');
        }

        // View invoice details if requested
        $receiptId = isset($_GET['receipt_id']) ? intval($_GET['receipt_id']) : null;
        $receipt = null;
        if ($receiptId) {
            $receipt = $this->paymentModel->getById($receiptId);
        }

        $this->render('admin/payments', [
            'payments' => $this->paymentModel->getAll(),
            'students' => $this->studentModel->getAll(),
            'receipt' => $receipt,
            'financialSummary' => $this->paymentModel->getFinancialSummary(),
            'levelFinances' => $this->paymentModel->getFinancialByLevel(),
            'classFinances' => $this->paymentModel->getFinancialByClass(),
            'studentSituations' => $this->paymentModel->getStudentFinancialSituations()
        ]);
    }
}


