<?php
if (!isset($_SESSION)) {
    session_start();
}

$currentController = $_GET['controller'] ?? '';
$currentAction = $_GET['action'] ?? '';
$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? 'Utilisateur';

$roleLabels = [
    'admin' => 'Admin',
    'teacher' => 'Enseignant',
    'parent' => 'Parent',
    'student' => 'Élève',
];

$navItems = [
    'admin' => [
        ['action' => 'dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Tableau de bord'],
        ['action' => 'classes', 'icon' => 'fa-graduation-cap', 'label' => 'Classes'],
        ['action' => 'students', 'icon' => 'fa-user-graduate', 'label' => 'Élèves'],
        ['action' => 'teachers', 'icon' => 'fa-chalkboard-user', 'label' => 'Enseignants'],
        ['action' => 'payments', 'icon' => 'fa-wallet', 'label' => 'Paiements'],
    ],
    'teacher' => [
        ['action' => 'dashboard', 'icon' => 'fa-gauge-high', 'label' => 'Mon espace'],
        ['action' => 'classes', 'icon' => 'fa-chalkboard-user', 'label' => 'Mes classes'],
        ['action' => 'grades', 'icon' => 'fa-pen-to-square', 'label' => 'Saisie des notes'],
        ['action' => 'attendance', 'icon' => 'fa-calendar-check', 'label' => 'Présences'],
    ],
    'parent' => [
        ['action' => 'dashboard', 'icon' => 'fa-users', 'label' => 'Suivi des enfants'],
    ],
];

$controllerForRole = $role === 'teacher' ? 'teacher' : ($role === 'parent' ? 'parent' : 'admin');
$displayRole = $roleLabels[$role] ?? ucfirst($role);
$initials = strtoupper(substr($username, 0, 2));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion École Privée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../../assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body>

<div class="wrapper">
    <nav id="sidebar" class="no-print">
        <div class="sidebar-header">
            <div class="d-flex align-items-center gap-3">
                <div class="brand-mark">
                    <i class="fa-solid fa-school"></i>
                </div>
                <div>
                    <h5 class="brand-title m-0 fw-bold">École Privée</h5>
                    <div class="brand-subtitle">Gestion scolaire</div>
                </div>
            </div>
        </div>

        <ul class="list-unstyled components">
            <?php foreach (($navItems[$role] ?? []) as $item): ?>
                <?php $active = $currentController === $controllerForRole && $currentAction === $item['action']; ?>
                <li class="<?php echo $active ? 'active' : ''; ?>">
                    <a href="index.php?controller=<?php echo $controllerForRole; ?>&action=<?php echo $item['action']; ?>">
                        <i class="fa-solid <?php echo $item['icon']; ?>"></i>
                        <span><?php echo htmlspecialchars($item['label']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>

            <li class="mt-4 pt-4 border-top border-light border-opacity-10">
                <a href="index.php?controller=auth&action=logout" class="text-warning">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light mb-4 no-print">
            <div class="container-fluid p-0 gap-3">
                <div class="input-group top-search">
                    <span class="input-group-text bg-white border-end-0 text-secondary">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input class="form-control border-start-0" type="search" placeholder="Rechercher...">
                </div>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    <a class="btn btn-sm btn-light border" href="index.php">
                        <i class="fa-solid fa-house"></i>
                    </a>
                    <span class="text-secondary small d-none d-md-block text-end">
                        <strong class="text-dark"><?php echo htmlspecialchars(ucfirst($username)); ?></strong><br>
                        <?php echo htmlspecialchars($displayRole); ?>
                    </span>
                    <span class="user-avatar">
                        <?php echo htmlspecialchars($initials); ?>
                    </span>
                </div>
            </div>
        </nav>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show bg-success text-white mb-4 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show bg-danger text-white mb-4 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

