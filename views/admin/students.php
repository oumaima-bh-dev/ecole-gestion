<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestion des Élèves & Parents</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addParentModal">
                <i class="fa-solid fa-users me-1"></i> Nouveau Parent
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="fa-solid fa-plus me-1"></i> Nouvel Élève
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="studentParentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="students-tab" data-bs-toggle="tab" data-bs-target="#students-pane" type="button" role="tab" aria-controls="students-pane" aria-selected="true" style="border-radius: 8px 8px 0 0;">
                <i class="fa-solid fa-user-graduate me-2"></i>Élèves (<?php echo count($students); ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="parents-tab" data-bs-toggle="tab" data-bs-target="#parents-pane" type="button" role="tab" aria-controls="parents-pane" aria-selected="false" style="border-radius: 8px 8px 0 0;">
                <i class="fa-solid fa-users me-2"></i>Parents (<?php echo count($parents); ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Students Tab Pane -->
        <div class="tab-pane fade show active" id="students-pane" role="tabpanel" aria-labelledby="students-tab">
            <div class="custom-table-container">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Nom & Prénom</th>
                                <th>Classe</th>
                                <th>Parent Associé</th>
                                <th>Né(e) le</th>
                                <th>Genre</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucun élève inscrit pour le moment.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($student['nom'] . ' ' . $student['prenom']); ?></div>
                                            <span class="text-muted small"><?php echo htmlspecialchars($student['email']); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($student['class_name']): ?>
                                                <span class="badge bg-primary-soft text-primary"><?php echo htmlspecialchars($student['class_name']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-soft text-secondary">Non affecté</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($student['parent_name']): ?>
                                                <span><i class="fa-solid fa-user me-2 text-muted"></i><?php echo htmlspecialchars($student['parent_name']); ?></span>
                                            <?php else: ?>
                                                <span class="text-danger small"><i class="fa-solid fa-circle-exclamation me-1"></i> Aucun parent</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted"><?php echo date('d/m/Y', strtotime($student['date_naissance'])); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo $student['sexe']; ?></span></td>
                                        <td>
                                            <?php if ($student['status'] === 'active'): ?>
                                                <span class="badge bg-success-soft text-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-soft text-danger">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStudentModal<?php echo $student['id']; ?>">
                                                    <i class="fa-solid fa-user-pen"></i> Modifier
                                                </button>
                                                <form action="index.php?controller=admin&action=students" method="POST" onsubmit="return confirm('Voulez-vous supprimer le compte de cet élève ?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="action" value="delete_student">
                                                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
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

        <!-- Parents Tab Pane -->
        <div class="tab-pane fade" id="parents-pane" role="tabpanel" aria-labelledby="parents-tab">
            <div class="custom-table-container">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Parent</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Email</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($parents)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucun parent enregistré.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($parents as $parent): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($parent['username']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($parent['telephone']); ?></td>
                                        <td class="text-muted"><?php echo htmlspecialchars($parent['adresse']); ?></td>
                                        <td><?php echo htmlspecialchars($parent['email']); ?></td>
                                        <td>
                                            <?php if ($parent['status'] === 'active'): ?>
                                                <span class="badge bg-success-soft text-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-soft text-danger">Inactif</span>
                                            <?php endif; ?>
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
</div>

<!-- Modals: Edit Student (Extracted from loop) -->
<?php if (!empty($students)): ?>
    <?php foreach ($students as $student): ?>
<div class="modal fade" id="editStudentModal<?php echo $student['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=students" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="edit_student">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Modifier l'élève</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($student['nom']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" class="form-control" name="prenom" value="<?php echo htmlspecialchars($student['prenom']); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Classe</label>
                        <select class="form-select" name="class_id">
                            <option value="">-- Sans classe --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $student['class_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Parent</label>
                        <select class="form-select" name="parent_id">
                            <option value="">-- Aucun parent --</option>
                            <?php foreach ($parents as $p): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo ($p['id'] == $student['parent_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de Naissance</label>
                        <input type="date" class="form-control" name="date_naissance" value="<?php echo $student['date_naissance']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sexe</label>
                        <select class="form-select" name="sexe" required>
                            <option value="M" <?php echo ($student['sexe'] === 'M') ? 'selected' : ''; ?>>Masculin (M)</option>
                            <option value="F" <?php echo ($student['sexe'] === 'F') ? 'selected' : ''; ?>>Féminin (F)</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea class="form-control" name="adresse" rows="2" required><?php echo htmlspecialchars($student['adresse']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Statut du Compte</label>
                    <select class="form-select" name="status" required>
                        <option value="active" <?php echo ($student['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactive" <?php echo ($student['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
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

<!-- Modal: Add Student -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=students" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_student">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Créer un profil Élève</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-primary fw-bold mb-3">Identifiants de Connexion</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" name="username" placeholder="lucas.dupont" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email de l'élève</label>
                    <input type="email" class="form-control" name="email" placeholder="student@ecole.com" required>
                </div>

                <hr>
                <h6 class="text-primary fw-bold mb-3">Informations Personnelles</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom de famille</label>
                        <input type="text" class="form-control" name="nom" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" class="form-control" name="prenom" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de Naissance</label>
                        <input type="date" class="form-control" name="date_naissance" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sexe</label>
                        <select class="form-select" name="sexe" required>
                            <option value="M">Masculin (M)</option>
                            <option value="F">Féminin (F)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Affecter à une classe</label>
                        <select class="form-select" name="class_id">
                            <option value="">-- Pas de classe --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lier à un Parent</label>
                        <select class="form-select" name="parent_id">
                            <option value="">-- Aucun parent --</option>
                            <?php foreach ($parents as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse de résidence</label>
                    <textarea class="form-control" name="adresse" rows="2" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le compte</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Parent -->
<div class="modal fade" id="addParentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=students" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_parent">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Créer un compte Parent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" name="username" placeholder="M. Dupont" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" name="email" placeholder="parent@ecole.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" class="form-control" name="telephone" placeholder="06XXXXXXXX" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Adresse de résidence</label>
                    <textarea class="form-control" name="adresse" rows="2" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>


