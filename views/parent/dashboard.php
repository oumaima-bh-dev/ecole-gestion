<div class="container-fluid p-0">
    <div class="p-4 mb-4 bg-white rounded-3 border">
        <h1 class="h3 fw-bold mb-1">Espace Parent : <?php echo htmlspecialchars(ucfirst($parent['username'])); ?></h1>
        <p class="text-muted m-0">Suivez en temps réel la scolarité, les notes, l'assiduité et les paiements de vos enfants.</p>
    </div>

    <?php if (empty($children)): ?>
        <div class="alert alert-warning py-4 text-center">
            <i class="fa-solid fa-users-slash fs-2 mb-2"></i>
            <p class="m-0">Aucun élève n'est actuellement rattaché à votre compte parent. Veuillez contacter l'administration.</p>
        </div>
    <?php else: ?>
        <!-- Children selector buttons if multiple children -->
        <?php if (count($children) > 1): ?>
            <div class="d-flex gap-2 mb-4">
                <?php foreach ($children as $child): ?>
                    <a href="index.php?controller=parent&action=dashboard&child_id=<?php echo $child['id']; ?>" class="btn <?php echo ($selectedChildId == $child['id']) ? 'btn-primary' : 'btn-outline-primary'; ?> px-4 py-2" style="border-radius: 12px;">
                        <i class="fa-solid fa-user-graduate me-2"></i><?php echo htmlspecialchars($child['prenom'] . ' ' . $child['nom']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Selected Child Dashboard Overview -->
        <?php if ($childProfile): ?>
            <div class="row mb-4">
                <!-- Child Info Card -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex flex-column align-items-center text-center">
                            <div class="badge bg-primary text-white p-3 rounded-circle mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;">
                                <?php echo strtoupper(substr($childProfile['prenom'], 0, 1) . substr($childProfile['nom'], 0, 1)); ?>
                            </div>
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($childProfile['prenom'] . ' ' . $childProfile['nom']); ?></h5>
                            <span class="badge bg-primary-soft text-primary mb-3 px-3 py-2"><?php echo htmlspecialchars($childProfile['class_name'] ?? 'Non affecté'); ?></span>
                            
                            <div class="w-100 mt-3 pt-3 border-top text-start small">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Date de naissance :</span>
                                    <span class="fw-medium"><?php echo date('d/m/Y', strtotime($childProfile['date_naissance'])); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Genre :</span>
                                    <span class="fw-medium"><?php echo $childProfile['sexe'] === 'M' ? 'Masculin' : 'Féminin'; ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Adresse :</span>
                                    <span class="fw-medium text-end" style="max-width: 150px;"><?php echo htmlspecialchars($childProfile['adresse']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats summary Row -->
                <div class="col-md-8">
                    <div class="row h-100">
                        <div class="col-sm-6 mb-4">
                            <div class="metric-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="metric-icon bg-primary-soft">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>
                                    <div class="text-muted small fw-medium">Moyenne Générale</div>
                                </div>
                                <div class="h2 mb-0 fw-bold mt-2 text-primary">
                                    <?php echo $generalAverage !== null ? number_format($generalAverage, 2, ',', ' ') . ' / 20' : 'N/A'; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 mb-4">
                            <div class="metric-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="metric-icon bg-danger-soft">
                                        <i class="fa-solid fa-calendar-times"></i>
                                    </div>
                                    <div class="text-muted small fw-medium">Absences Non Justifiées</div>
                                </div>
                                <div class="h2 mb-0 fw-bold mt-2 text-danger">
                                    <?php echo $absenceStats['unjustified'] ?? 0; ?>
                                    <span class="small text-muted fw-normal" style="font-size: 14px;">(sur <?php echo $absenceStats['absences'] ?? 0; ?> totales)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Tabs -->
            <ul class="nav nav-pills mb-3 gap-2 no-print" id="childTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-2" id="notes-tab" data-bs-toggle="pill" data-bs-target="#notes-pane" type="button" role="tab" style="border-radius: 10px;">
                        <i class="fa-solid fa-file-invoice me-2"></i>Notes & Bulletin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2" id="absences-tab" data-bs-toggle="pill" data-bs-target="#absences-pane" type="button" role="tab" style="border-radius: 10px;">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>Absences
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2" id="billing-tab" data-bs-toggle="pill" data-bs-target="#billing-pane" type="button" role="tab" style="border-radius: 10px;">
                        <i class="fa-solid fa-wallet me-2"></i>Paiements
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="childTabsContent">
                <!-- Notes / Bulletin Pane -->
                <div class="tab-pane fade show active" id="notes-pane" role="tabpanel" aria-labelledby="notes-tab">
                    <div class="row">
                        <!-- Subject averages -->
                        <div class="col-lg-6 mb-4">
                            <div class="custom-table-container">
                                <h5 class="fw-bold mb-4"><i class="fa-solid fa-book text-success me-2"></i>Moyennes par Matière</h5>
                                <div class="table-responsive">
                                    <table class="table table-custom align-middle">
                                        <thead>
                                            <tr>
                                                <th>Matière</th>
                                                <th>Coef</th>
                                                <th>Moyenne</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($averages)): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Aucune note enregistrée.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($averages as $avg): ?>
                                                    <tr>
                                                        <td class="fw-bold"><?php echo htmlspecialchars($avg['matiere_nom']); ?></td>
                                                        <td><?php echo $avg['coefficient']; ?></td>
                                                        <td class="fw-bold <?php echo $avg['moyenne_matiere'] >= 10 ? 'text-success' : 'text-danger'; ?>">
                                                            <?php echo number_format($avg['moyenne_matiere'], 2, ',', ' '); ?> / 20
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Full grades ledger -->
                        <div class="col-lg-6 mb-4">
                            <div class="custom-table-container">
                                <h5 class="fw-bold mb-4"><i class="fa-solid fa-list-ol text-primary me-2"></i>Détails des Évaluations</h5>
                                <div class="table-responsive">
                                    <table class="table table-custom align-middle">
                                        <thead>
                                            <tr>
                                                <th>Matière</th>
                                                <th>Type</th>
                                                <th>Note</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($notes)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Aucune note individuelle enregistrée.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($notes as $note): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($note['matiere_nom']); ?></td>
                                                        <td><small class="text-muted"><?php echo htmlspecialchars($note['type_examen']); ?></small></td>
                                                        <td class="fw-bold <?php echo $note['note'] >= 10 ? 'text-success' : 'text-danger'; ?>">
                                                            <?php echo number_format($note['note'], 2, ',', ' '); ?> / 20
                                                        </td>
                                                        <td class="small text-muted"><?php echo date('d/m/Y', strtotime($note['date_examen'])); ?></td>
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

                <!-- Absences Pane -->
                <div class="tab-pane fade" id="absences-pane" role="tabpanel" aria-labelledby="absences-tab">
                    <div class="custom-table-container">
                        <h5 class="fw-bold mb-4"><i class="fa-solid fa-calendar-day text-danger me-2"></i>Registre de Présences / Absences</h5>
                        <div class="table-responsive">
                            <table class="table table-custom align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Justification</th>
                                        <th>Motif fourni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($absences)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Aucune absence enregistrée. Félicitations !</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($absences as $abs): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo date('d/m/Y', strtotime($abs['date_absence'])); ?></td>
                                                <td>
                                                    <span class="badge bg-danger-soft text-danger fw-semibold">Absent</span>
                                                </td>
                                                <td>
                                                    <?php if ($abs['justifie'] == 1): ?>
                                                        <span class="badge bg-success text-white"><i class="fa-solid fa-circle-check me-1"></i> Justifiée</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger text-white"><i class="fa-solid fa-triangle-exclamation me-1"></i> Non justifiée</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-muted italic small"><?php echo htmlspecialchars($abs['motif'] ?: 'Aucun motif renseigné.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Billing Pane -->
                <div class="tab-pane fade" id="billing-pane" role="tabpanel" aria-labelledby="billing-tab">
                    <div class="custom-table-container">
                        <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt text-warning me-2"></i>Historique des Règlements Scolaires</h5>
                        <div class="table-responsive">
                            <table class="table table-custom align-middle">
                                <thead>
                                    <tr>
                                        <th>Référence Reçu</th>
                                        <th>Nature du Paiement</th>
                                        <th>Montant Payé</th>
                                        <th>Date de Valeur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($payments)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Aucune transaction enregistrée.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($payments as $pay): ?>
                                            <tr>
                                                <td class="fw-bold font-monospace"><?php echo htmlspecialchars($pay['recu_no']); ?></td>
                                                <td>
                                                    <?php 
                                                    $badge = 'bg-secondary';
                                                    if ($pay['type_paiement'] === 'Inscription') $badge = 'bg-primary-soft text-primary';
                                                    if ($pay['type_paiement'] === 'Mensualite') $badge = 'bg-success-soft text-success';
                                                    if ($pay['type_paiement'] === 'Frais divers') $badge = 'bg-warning-soft text-warning';
                                                    ?>
                                                    <span class="badge badge-custom <?php echo $badge; ?>"><?php echo htmlspecialchars($pay['type_paiement']); ?></span>
                                                </td>
                                                <td class="fw-bold text-success">+ <?php echo number_format($pay['montant'], 2, ',', ' '); ?> DH</td>
                                                <td class="text-muted"><?php echo date('d/m/Y', strtotime($pay['date_paiement'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
