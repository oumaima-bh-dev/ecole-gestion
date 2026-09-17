<?php
$fmt = function ($amount) {
    return number_format($amount ?? 0, 2, ',', ' ') . ' DH';
};

$statusBadge = function ($status) {
    if ($status === 'Payé') return 'bg-success-soft';
    if ($status === 'Partiellement payé') return 'bg-warning-soft';
    return 'bg-danger-soft';
};
?>
<div class="container-fluid p-0">
    <div class="no-print">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestion des Paiements & Tarifs</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="fa-solid fa-plus me-1"></i> Enregistrer un Paiement
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
                <div class="metric-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon bg-success-soft"><i class="fa-solid fa-cash-register"></i></div>
                        <div>
                            <div class="text-muted small fw-medium">Total encaissé</div>
                            <div class="h4 mb-0 fw-bold"><?php echo $fmt($financialSummary['total_collected'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
                <div class="metric-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon bg-warning-soft"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div>
                            <div class="text-muted small fw-medium">Total restant</div>
                            <div class="h4 mb-0 fw-bold"><?php echo $fmt($financialSummary['total_remaining'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
                <div class="metric-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon bg-danger-soft"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div>
                            <div class="text-muted small fw-medium">Total impayé</div>
                            <div class="h4 mb-0 fw-bold"><?php echo $fmt($financialSummary['total_unpaid'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="metric-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="metric-icon bg-primary-soft"><i class="fa-solid fa-user-check"></i></div>
                        <div>
                            <div class="text-muted small fw-medium">Élèves à jour / en retard</div>
                            <div class="h4 mb-0 fw-bold"><?php echo intval($financialSummary['students_paid'] ?? 0); ?> / <?php echo intval($financialSummary['students_late'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-5 mb-4 mb-xl-0">
                <div class="custom-table-container h-100">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-layer-group text-primary me-2"></i>Tableau de bord financier par niveau</h5>
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>Niveau</th>
                                    <th>Attendu</th>
                                    <th>Encaissé</th>
                                    <th>Reste</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($levelFinances)): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">Aucune donnée financière.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($levelFinances as $level): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($level['niveau_name']); ?></td>
                                            <td><?php echo $fmt($level['total_expected']); ?></td>
                                            <td class="text-success fw-bold"><?php echo $fmt($level['total_collected']); ?></td>
                                            <td class="text-warning fw-bold"><?php echo $fmt($level['total_remaining']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="custom-table-container h-100">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-school text-primary me-2"></i>Vue financière par classe</h5>
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Niveau</th>
                                    <th>Élèves</th>
                                    <th>Attendu</th>
                                    <th>Encaissé</th>
                                    <th>Reste</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($classFinances)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">Aucune classe à afficher.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($classFinances as $classFinance): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($classFinance['class_name']); ?></td>
                                            <td><span class="badge bg-primary-soft"><?php echo htmlspecialchars($classFinance['niveau_name']); ?></span></td>
                                            <td><?php echo intval($classFinance['student_count']); ?></td>
                                            <td><?php echo $fmt($classFinance['total_expected']); ?></td>
                                            <td class="text-success fw-bold"><?php echo $fmt($classFinance['total_collected']); ?></td>
                                            <td class="text-warning fw-bold"><?php echo $fmt($classFinance['total_remaining']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4 border-bottom-0" id="financeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="situations-tab" data-bs-toggle="tab" data-bs-target="#situations-pane" type="button" role="tab">
                    <i class="fa-solid fa-users-viewfinder me-2"></i>Situations élèves
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments-pane" type="button" role="tab">
                    <i class="fa-solid fa-receipt me-2"></i>Historique des paiements
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="situations-pane" role="tabpanel">
                <div class="custom-table-container mb-4">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Situation financière détaillée par élève</h5>
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th>Total à payer</th>
                                    <th>Payé</th>
                                    <th>Reste</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($studentSituations)): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">Aucun élève trouvé.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($studentSituations as $situation): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($situation['nom'] . ' ' . $situation['prenom']); ?></div>
                                                <span class="text-muted small"><?php echo htmlspecialchars($situation['niveau_name'] ?? 'Sans niveau'); ?></span>
                                            </td>
                                            <td><span class="badge bg-secondary-soft"><?php echo htmlspecialchars($situation['class_name'] ?? 'Sans classe'); ?></span></td>
                                            <td class="fw-bold"><?php echo $fmt($situation['total_due']); ?></td>
                                            <td class="text-success fw-bold"><?php echo $fmt($situation['total_paid']); ?></td>
                                            <td class="text-warning fw-bold"><?php echo $fmt($situation['remaining_due']); ?></td>
                                            <td><span class="badge badge-custom <?php echo $statusBadge($situation['payment_status']); ?>"><?php echo htmlspecialchars($situation['payment_status']); ?></span></td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feeModal<?php echo $situation['student_id']; ?>">
                                                    <i class="fa-solid fa-pen"></i> Tarif
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="payments-pane" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="custom-table-container">
                            <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt text-primary me-2"></i>Historique détaillé des encaissements</h5>
                            <div class="table-responsive">
                                <table class="table table-custom align-middle">
                                    <thead>
                                        <tr>
                                            <th>N° Reçu</th>
                                            <th>Élève</th>
                                            <th>Type</th>
                                            <th>Mode</th>
                                            <th>Montant</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($payments)): ?>
                                            <tr><td colspan="7" class="text-center text-muted py-4">Aucune transaction enregistrée.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($payments as $pay): ?>
                                                <tr class="<?php echo (isset($receipt['id']) && $receipt['id'] == $pay['id']) ? 'table-warning bg-opacity-25' : ''; ?>">
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($pay['recu_no']); ?></div>
                                                        <span class="text-muted small"><?php echo htmlspecialchars($pay['reference_paiement'] ?: 'Sans référence'); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($pay['nom'] . ' ' . $pay['prenom']); ?><br>
                                                        <span class="text-muted small"><?php echo htmlspecialchars($pay['class_name'] ?? 'Non affecté'); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $badge = 'bg-secondary-soft';
                                                        if ($pay['type_paiement'] === 'Inscription') $badge = 'bg-primary-soft';
                                                        if ($pay['type_paiement'] === 'Mensualite') $badge = 'bg-success-soft';
                                                        if ($pay['type_paiement'] === 'Frais divers') $badge = 'bg-warning-soft';
                                                        ?>
                                                        <span class="badge badge-custom <?php echo $badge; ?>"><?php echo htmlspecialchars($pay['type_paiement']); ?></span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($pay['mode_paiement'] ?? 'Espèces'); ?></td>
                                                    <td class="fw-bold"><?php echo $fmt($pay['montant']); ?></td>
                                                    <td class="text-muted"><?php echo date('d/m/Y', strtotime($pay['date_paiement'])); ?></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a href="index.php?controller=admin&action=payments&receipt_id=<?php echo $pay['id']; ?>" class="btn btn-sm btn-outline-dark">
                                                                <i class="fa-solid fa-print"></i> Reçu
                                                            </a>
                                                            <form action="index.php?controller=admin&action=payments" method="POST" onsubmit="return confirm('Supprimer cette transaction ?');">
                                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                                <input type="hidden" name="action" value="delete_payment">
                                                                <input type="hidden" name="id" value="<?php echo $pay['id']; ?>">
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

                    <div class="col-lg-4">
                        <div class="custom-table-container">
                            <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt text-warning me-2"></i>Aperçu du reçu</h5>
                            <?php if (empty($receipt)): ?>
                                <div class="text-center text-muted py-5 border border-dashed rounded-3">
                                    <i class="fa-solid fa-print fs-1 mb-2"></i>
                                    <p class="m-0">Sélectionnez "Reçu" dans l'historique pour l'afficher ou l'imprimer.</p>
                                </div>
                            <?php else: ?>
                                <div class="border rounded-3 p-3 bg-light shadow-sm position-relative">
                                    <div class="text-center pb-3 border-bottom mb-3">
                                        <h6 class="fw-bold m-0 text-primary">ÉCOLE PRIVÉE EXCELLENCE</h6>
                                        <small class="text-muted">123 Rue Scolaire, Paris</small>
                                    </div>
                                    <div class="row small mb-3">
                                        <div class="col-6">
                                            <div class="text-muted">N° Reçu :</div>
                                            <strong class="text-dark"><?php echo htmlspecialchars($receipt['recu_no']); ?></strong>
                                        </div>
                                        <div class="col-6 text-end">
                                            <div class="text-muted">Date :</div>
                                            <strong><?php echo date('d/m/Y', strtotime($receipt['date_paiement'])); ?></strong>
                                        </div>
                                    </div>
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="text-muted small">Élève concerné :</div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($receipt['student_nom'] . ' ' . $receipt['student_prenom']); ?></div>
                                        <div class="small text-muted">Classe : <?php echo htmlspecialchars($receipt['class_name'] ?? 'Non affecté'); ?></div>
                                    </div>
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="text-muted small">Parent associé :</div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($receipt['parent_name'] ?? 'Non associé'); ?></div>
                                        <div class="small text-muted">Tél : <?php echo htmlspecialchars($receipt['parent_tel'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted">Nature :</span>
                                            <span class="fw-medium text-dark"><?php echo htmlspecialchars($receipt['type_paiement']); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted">Mode :</span>
                                            <span class="fw-medium text-dark"><?php echo htmlspecialchars($receipt['mode_paiement'] ?? 'Espèces'); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                            <span class="fw-bold text-dark">Montant payé :</span>
                                            <span class="h5 fw-bold text-success m-0"><?php echo $fmt($receipt['montant']); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center small mt-2">
                                            <span class="text-muted">Reste après paiement :</span>
                                            <span class="fw-bold"><?php echo $fmt($receipt['remaining_due']); ?></span>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button onclick="printReceipt();" class="btn btn-dark">
                                            <i class="fa-solid fa-print me-1"></i> Imprimer le reçu
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($receipt): ?>
        <div class="d-none print-receipt d-print-block p-5 border" style="background-color: white; color: black; font-family: 'Outfit', sans-serif;">
            <div class="row align-items-center pb-4 mb-4 border-bottom">
                <div class="col-8">
                    <h2 class="fw-bold m-0 text-primary">ÉCOLE PRIVÉE EXCELLENCE</h2>
                    <p class="m-0 text-muted">Service de facturation et comptabilité</p>
                    <small>Email: finance@ecole-excellence.com | Tél: 01 23 45 67 89</small>
                </div>
                <div class="col-4 text-end">
                    <h4 class="fw-bold text-uppercase m-0">Reçu de Paiement</h4>
                    <small class="text-muted">Réf: <?php echo htmlspecialchars($receipt['recu_no']); ?></small>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <div class="text-muted uppercase small tracking-wider">Date du paiement :</div>
                    <div class="fw-bold"><?php echo date('d/m/Y', strtotime($receipt['date_paiement'])); ?></div>
                </div>
                <div class="col-6 text-end">
                    <div class="text-muted uppercase small tracking-wider">Mode de paiement :</div>
                    <div class="fw-bold"><?php echo htmlspecialchars($receipt['mode_paiement'] ?? 'Espèces'); ?></div>
                    <?php if (!empty($receipt['reference_paiement'])): ?>
                        <small>Référence : <?php echo htmlspecialchars($receipt['reference_paiement']); ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-6 border-end">
                    <h6 class="fw-bold text-muted mb-2">ÉLÈVE</h6>
                    <div class="fw-bold fs-5"><?php echo htmlspecialchars($receipt['student_nom'] . ' ' . $receipt['student_prenom']); ?></div>
                    <div>Classe: <?php echo htmlspecialchars($receipt['class_name'] ?? 'Non affecté'); ?></div>
                </div>
                <div class="col-6 ps-4">
                    <h6 class="fw-bold text-muted mb-2">PARENT D'ÉLÈVE</h6>
                    <div class="fw-bold fs-5"><?php echo htmlspecialchars($receipt['parent_name'] ?? 'Non associé'); ?></div>
                    <div>Tél: <?php echo htmlspecialchars($receipt['parent_tel'] ?? 'N/A'); ?></div>
                </div>
            </div>

            <table class="table table-bordered mb-5">
                <thead class="table-light">
                    <tr>
                        <th>Désignation</th>
                        <th class="text-end" style="width: 170px;">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($receipt['type_paiement']); ?></strong><br>
                            <span class="text-muted small">Paiement réglementaire pour l'année scolaire en cours.</span>
                        </td>
                        <td class="text-end fw-bold fs-5"><?php echo $fmt($receipt['montant']); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="row mb-5">
                <div class="col-4"><span class="text-muted">Total dû :</span><br><strong><?php echo $fmt($receipt['total_due']); ?></strong></div>
                <div class="col-4"><span class="text-muted">Total payé :</span><br><strong><?php echo $fmt($receipt['total_paid']); ?></strong></div>
                <div class="col-4 text-end"><span class="text-muted">Reste à payer :</span><br><strong><?php echo $fmt($receipt['remaining_due']); ?></strong></div>
            </div>

            <div class="row align-items-center mt-5">
                <div class="col-6 text-muted small">
                    * Ce document fait office de preuve de paiement officiel.
                </div>
                <div class="col-6 text-end">
                    <div class="d-inline-block border-top pt-2 text-center" style="width: 200px;">
                        <small class="text-muted">Cachet & Signature de l'école</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php foreach ($studentSituations as $situation): ?>
    <div class="modal fade" id="feeModal<?php echo $situation['student_id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" action="index.php?controller=admin&action=payments" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="update_student_fee">
                <input type="hidden" name="student_id" value="<?php echo $situation['student_id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tarif de <?php echo htmlspecialchars($situation['nom'] . ' ' . $situation['prenom']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Montant total à payer (DH)</label>
                        <input type="number" class="form-control" name="total_due" value="<?php echo htmlspecialchars($situation['total_due']); ?>" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note interne</label>
                        <textarea class="form-control" name="fee_notes" rows="3"><?php echo htmlspecialchars($situation['fee_notes']); ?></textarea>
                    </div>
                    <div class="p-3 bg-light rounded-3 border small">
                        <div class="d-flex justify-content-between"><span>Déjà payé</span><strong><?php echo $fmt($situation['total_paid']); ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Reste actuel</span><strong><?php echo $fmt($situation['remaining_due']); ?></strong></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=admin&action=payments" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_payment">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Enregistrer un Paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Sélectionner l'Élève</label>
                    <select class="form-select" name="student_id" required>
                        <?php foreach ($students as $stud): ?>
                            <option value="<?php echo $stud['id']; ?>"><?php echo htmlspecialchars($stud['nom'] . ' ' . $stud['prenom']); ?> (<?php echo htmlspecialchars($stud['class_name'] ?? 'Sans classe'); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type de paiement</label>
                    <select class="form-select" name="type_paiement" required>
                        <option value="Inscription">Frais d'Inscription</option>
                        <option value="Mensualite">Mensualité Scolaire</option>
                        <option value="Frais divers">Frais divers</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Montant (DH)</label>
                        <input type="number" class="form-control" name="montant" placeholder="Ex: 500" step="0.01" min="1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de paiement</label>
                        <input type="date" class="form-control" name="date_paiement" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mode de paiement</label>
                    <select class="form-select" name="mode_paiement" required>
                        <option value="Espèces">Espèces</option>
                        <option value="Carte bancaire">Carte bancaire</option>
                        <option value="Virement">Virement</option>
                        <option value="Chèque">Chèque</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Référence</label>
                    <input type="text" class="form-control" name="reference_paiement" placeholder="Ex: N° chèque, référence virement...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Note</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="Information complémentaire sur le paiement"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
            </div>
        </form>
    </div>
</div>
