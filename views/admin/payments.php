<div class="container-fluid p-0">
    <!-- Main content only visible during non-print -->
    <div class="no-print">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestion des Paiements & Tarifs</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="fa-solid fa-plus me-1"></i> Enregistrer un Paiement
            </button>
        </div>

        <div class="row">
            <!-- Payment history list -->
            <div class="col-lg-8 mb-4">
                <div class="custom-table-container">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt text-primary me-2"></i>Historique des Encaissements</h5>
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>N° Reçu</th>
                                    <th>Élève</th>
                                    <th>Classe</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Aucune transaction enregistrée.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $pay): ?>
                                        <tr class="<?php echo (isset($receipt['id']) && $receipt['id'] == $pay['id']) ? 'table-warning bg-opacity-25' : ''; ?>">
                                            <td class="fw-bold"><?php echo htmlspecialchars($pay['recu_no']); ?></td>
                                            <td><?php echo htmlspecialchars($pay['nom'] . ' ' . $pay['prenom']); ?></td>
                                            <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?php echo htmlspecialchars($pay['class_name'] ?? 'Non affecté'); ?></span></td>
                                            <td>
                                                <?php 
                                                $badge = 'bg-secondary';
                                                if ($pay['type_paiement'] === 'Inscription') $badge = 'bg-primary-soft text-primary';
                                                if ($pay['type_paiement'] === 'Mensualite') $badge = 'bg-success-soft text-success';
                                                if ($pay['type_paiement'] === 'Frais divers') $badge = 'bg-warning-soft text-warning';
                                                ?>
                                                <span class="badge badge-custom <?php echo $badge; ?>"><?php echo htmlspecialchars($pay['type_paiement']); ?></span>
                                            </td>
                                            <td class="fw-bold"><?php echo number_format($pay['montant'], 2, ',', ' '); ?> DH</td>
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

            <!-- Receipt Detail view -->
            <div class="col-lg-4">
                <div class="custom-table-container">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt text-warning me-2"></i>Aperçu du Reçu</h5>
                    <?php if (empty($receipt)): ?>
                        <div class="text-center text-muted py-5 border border-dashed rounded-3">
                            <i class="fa-solid fa-print fs-1 mb-2"></i>
                            <p class="m-0">Sélectionnez "Reçu" dans l'historique pour l'afficher ou l'imprimer.</p>
                        </div>
                    <?php else: ?>
                        <!-- Interactive receipt container -->
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
                                    <span class="text-muted">Nature du paiement :</span>
                                    <span class="fw-medium text-dark"><?php echo htmlspecialchars($receipt['type_paiement']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                    <span class="fw-bold text-dark">Montant Payé :</span>
                                    <span class="h5 fw-bold text-success m-0"><?php echo number_format($receipt['montant'], 2, ',', ' '); ?> DH</span>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button onclick="printReceipt();" class="btn btn-dark">
                                    <i class="fa-solid fa-print me-1"></i> Imprimer le Reçu
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Printable Receipt Layout (only visible in print mode) -->
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
                    <div class="fw-bold">Espèces / Carte / Virement</div>
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
                        <th class="text-end" style="width: 150px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($receipt['type_paiement']); ?></strong><br>
                            <span class="text-muted small">Paiement réglementaire pour l'année scolaire en cours.</span>
                        </td>
                        <td class="text-end fw-bold fs-5"><?php echo number_format($receipt['montant'], 2, ',', ' '); ?> DH</td>
                    </tr>
                </tbody>
            </table>

            <div class="row align-items-center mt-5">
                <div class="col-6 text-muted small">
                    * Ce document fait office de preuve de paiement officiel. Conservé pour faire valoir ce que de droit.
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

<!-- Modal: Add Payment -->
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
                    <label class="form-label">Type de Paiement</label>
                    <select class="form-select" name="type_paiement" required>
                        <option value="Inscription">Frais d'Inscription</option>
                        <option value="Mensualite">Mensualité Scolaire</option>
                        <option value="Frais divers">Frais divers</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Montant (DH)</label>
                    <input type="number" class="form-control" name="montant" placeholder="Ex: 500" step="0.01" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date de Paiement</label>
                    <input type="date" class="form-control" name="date_paiement" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
            </div>
        </form>
    </div>
</div>


