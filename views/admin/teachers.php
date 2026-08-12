<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestion des Enseignants</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            <i class="fa-solid fa-plus me-1"></i> Nouvel Enseignant
        </button>
    </div>

    <div class="custom-table-container">
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>Enseignant</th>
                        <th>Diplôme / Qualification</th>
                        <th>Téléphone</th>
                        <th>Date d'Embauche</th>
                        <th>Statut du Compte</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($teachers)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun enseignant configuré.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold">M./Mme <?php echo htmlspecialchars(ucfirst($teacher['username'])); ?></div>
                                    <span class="text-muted small"><?php echo htmlspecialchars($teacher['email']); ?></span>
                                </td>
                                <td><span class="badge bg-success bg-opacity-10 text-success fw-medium"><?php echo htmlspecialchars($teacher['diplome']); ?></span></td>
                                <td><?php echo htmlspecialchars($teacher['telephone']); ?></td>
                                <td class="text-muted"><?php echo date('d/m/Y', strtotime($teacher['date_embauche'])); ?></td>
                                <td>
                                    <?php if ($teacher['status'] === 'active'): ?>
                                        <span class="badge bg-success-soft text-success">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-soft text-danger">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTeacherModal<?php echo $teacher['id']; ?>">
                                            <i class="fa-solid fa-user-pen"></i> Modifier
                                        </button>
                                        <form action="index.php?controller=admin&action=teachers" method="POST" onsubmit="return confirm('Voulez-vous supprimer cet enseignant ?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="action" value="delete_teacher">
                                            <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
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

<!-- Modals: Edit Teacher -->
<?php if (!empty($teachers)): ?>
    <?php foreach ($teachers as $teacher): ?>
<div class="modal fade" id="editTeacherModal<?php echo $teacher['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=teachers" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="edit_teacher">
            <input type="hidden" name="id" value="<?php echo $teacher['id']; ?>">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Modifier l'Enseignant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" class="form-control" name="telephone" value="<?php echo htmlspecialchars($teacher['telephone']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Diplôme / Spécialisation</label>
                    <input type="text" class="form-control" name="diplome" value="<?php echo htmlspecialchars($teacher['diplome']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date d'embauche</label>
                    <input type="date" class="form-control" name="date_embauche" value="<?php echo $teacher['date_embauche']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Statut du Compte</label>
                    <select class="form-select" name="status" required>
                        <option value="active" <?php echo ($teacher['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactive" <?php echo ($teacher['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modal: Add Teacher -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=teachers" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_teacher">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Créer un compte Enseignant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" name="username" placeholder="M. Martin" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" name="email" placeholder="teacher@ecole.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" class="form-control" name="telephone" placeholder="06XXXXXXXX" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Diplôme / Qualification</label>
                    <input type="text" class="form-control" name="diplome" placeholder="Ex: Licence Anglais, Master Physique" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date d'Embauche</label>
                    <input type="date" class="form-control" name="date_embauche" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le compte</button>
            </div>
        </form>
    </div>
</div>


