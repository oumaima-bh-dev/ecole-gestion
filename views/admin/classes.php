<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestion des Classes & Matières</h1>
    </div>

    <div class="row">
        <!-- Classes List Card -->
        <div class="col-lg-8 mb-4">
            <div class="custom-table-container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold m-0"><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Liste des Classes</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClassModal" style="border-radius: 8px;">
                        <i class="fa-solid fa-plus me-1"></i> Nouvelle Classe
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Classe</th>
                                <th>Niveau</th>
                                <th>Effectif</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classes)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucune classe configurée.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classes as $classe): ?>
                                    <tr class="<?php echo ($selectedClassId == $classe['id']) ? 'table-primary bg-opacity-25' : ''; ?>">
                                        <td>
                                            <a href="index.php?controller=admin&action=classes&class_id=<?php echo $classe['id']; ?>" class="fw-bold text-decoration-none">
                                                <?php echo htmlspecialchars($classe['nom']); ?>
                                            </a>
                                        </td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?php echo htmlspecialchars($classe['niveau_nom']); ?></span></td>
                                        <td>
                                            <div class="progress" style="height: 6px; width: 100px; display: inline-block; vertical-align: middle;">
                                                <?php 
                                                $pct = min(100, ($classe['student_count'] / $classe['capacite_max']) * 100);
                                                $barColor = $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success');
                                                ?>
                                                <div class="progress-bar <?php echo $barColor; ?>" role="progressbar" style="width: <?php echo $pct; ?>%"></div>
                                            </div>
                                            <span class="small ms-2"><?php echo $classe['student_count']; ?> / <?php echo $classe['capacite_max']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="index.php?controller=admin&action=classes&class_id=<?php echo $classe['id']; ?>" class="btn btn-sm btn-outline-primary" title="Gérer les cours">
                                                    <i class="fa-solid fa-book-open"></i> Cours
                                                </a>
                                                <form action="index.php?controller=admin&action=classes" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette classe ?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="delete_class">
                                                    <input type="hidden" name="id" value="<?php echo $classe['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Subjects List Card -->
        <div class="col-lg-4 mb-4">
            <div class="custom-table-container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold m-0"><i class="fa-solid fa-book text-success me-2"></i>Matières</h5>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSubjectModal" style="border-radius: 8px;">
                        <i class="fa-solid fa-plus me-1"></i> Ajouter
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Coef</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($subjects)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucune matière enregistrée.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($subject['nom']); ?></td>
                                        <td><?php echo $subject['coefficient']; ?></td>
                                        <td>
                                            <form action="index.php?controller=admin&action=classes" method="POST" onsubmit="return confirm('Supprimer cette matière ?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="action" value="delete_subject">
                                                <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 6px;">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments details (shows only if a class is selected) -->
    <?php if ($selectedClassId): ?>
        <?php 
        // Find selected class name
        $selectedClassName = '';
        foreach ($classes as $c) {
            if ($c['id'] == $selectedClassId) {
                $selectedClassName = $c['nom'];
                break;
            }
        }
        ?>
        <div class="custom-table-container mb-4 mt-2">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-chalkboard-user text-warning me-2"></i>Matières & Enseignants affectés pour la classe <strong><?php echo htmlspecialchars($selectedClassName); ?></strong></h5>
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#assignTeacherModal" style="border-radius: 8px;">
                    <i class="fa-solid fa-link me-1"></i> Affecter un Enseignant
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Coefficient</th>
                            <th>Enseignant</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Aucune matière affectée à un enseignant pour cette classe.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo htmlspecialchars($assignment['matiere_nom']); ?></td>
                                    <td><?php echo $assignment['coefficient']; ?></td>
                                    <td><i class="fa-solid fa-user-tie me-2 text-secondary"></i><?php echo htmlspecialchars(ucfirst($assignment['teacher_username'])); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($assignment['teacher_email']); ?></td>
                                    <td>
                                        <form action="index.php?controller=admin&action=classes" method="POST" onsubmit="return confirm('Annuler cette affectation ?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="action" value="delete_assignment">
                                            <input type="hidden" name="id" value="<?php echo $assignment['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa-solid fa-unlink me-1"></i> Retirer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Add Class -->
<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=classes" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_class">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ajouter une nouvelle Classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nom de la classe</label>
                    <input type="text" class="form-control" name="nom" placeholder="Ex: Terminale S2, CM1-A" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Niveau d'enseignement</label>
                    <select class="form-select" name="niveau_id" required>
                        <?php foreach ($niveaux as $niv): ?>
                            <option value="<?php echo $niv['id']; ?>"><?php echo htmlspecialchars($niv['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Capacité maximale d'élèves</label>
                    <input type="number" class="form-control" name="capacite_max" value="30" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Subject -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=classes" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_subject">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ajouter une Matière</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nom de la matière</label>
                    <input type="text" class="form-control" name="nom" placeholder="Ex: Philosophie, Espagnol" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Coefficient</label>
                    <input type="number" class="form-control" name="coefficient" value="2" min="1" max="10" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-success">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Assign Teacher -->
<div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=classes" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="assign_teacher">
            <input type="hidden" name="class_id" value="<?php echo $selectedClassId; ?>">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Affecter un Enseignant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Sélectionner la Matière</label>
                    <select class="form-select" name="matiere_id" required>
                        <?php foreach ($subjects as $sub): ?>
                            <option value="<?php echo $sub['id']; ?>"><?php echo htmlspecialchars($sub['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sélectionner l'Enseignant</label>
                    <select class="form-select" name="teacher_id" required>
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars(ucfirst($t['username'])); ?> (<?php echo htmlspecialchars($t['diplome']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-warning text-dark">Lier le cours</button>
            </div>
        </form>
    </div>
</div>


