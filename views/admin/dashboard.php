<div class="container-fluid p-0">
    <div class="page-header mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div>
                <span class="badge bg-success-soft mb-2">Vue d'ensemble</span>
                <h1 class="h3 mb-1 fw-bold">Bonjour, Administrateur </h1>
                <p class="text-muted mb-0">Suivez les indicateurs essentiels de votre établissement.</p>
            </div>
            <span class="text-muted small mt-3 mt-md-0">
                <i class="fa-regular fa-clock me-1"></i><?php echo date('d/m/Y H:i'); ?>
            </span>
        </div>

        <div class="dashboard-strip">
            <div>
                <span>Gestion pédagogique</span>
                <strong>Classes et notes</strong>
            </div>
            <div>
                <span>Relation famille</span>
                <strong>Parents suivis</strong>
            </div>
            <div>
                <span>Administration</span>
                <strong>Paiements en DH</strong>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon bg-primary-soft">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Élèves</div>
                        <div class="h3 mb-0 fw-bold"><?php echo $stats['students']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4 mb-xl-0">
            <div class="metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon bg-success-soft">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Classes</div>
                        <div class="h3 mb-0 fw-bold"><?php echo $stats['classes']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4 mb-md-0">
            <div class="metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon bg-purple-soft">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Enseignants</div>
                        <div class="h3 mb-0 fw-bold"><?php echo $stats['teachers']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon bg-warning-soft">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Frais collectés</div>
                        <div class="h3 mb-0 fw-bold"><?php echo number_format($finances['total_collected'] ?? 0, 0, ',', ' '); ?> DH</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="custom-table-container h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold m-0">Répartition des recettes</h5>
                    <i class="fa-solid fa-chart-pie text-muted"></i>
                </div>
                <div style="position: relative; height:280px; width:100%">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6 mb-4">
            <div class="custom-table-container h-100">
                <h5 class="fw-bold mb-4">Résumé financier</h5>
                <div class="p-3 mb-3 bg-success-soft rounded-3">
                    <div class="text-muted small">Total des encaissements</div>
                    <div class="h3 fw-bold text-success m-0"><?php echo number_format($finances['total_collected'] ?? 0, 2, ',', ' '); ?> DH</div>
                </div>

                <div class="list-group list-group-flush small">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                        <span><i class="fa-solid fa-circle text-primary me-2 small"></i>Frais d'inscriptions</span>
                        <span class="fw-bold"><?php echo number_format($finances['total_inscription'] ?? 0, 2, ',', ' '); ?> DH</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                        <span><i class="fa-solid fa-circle text-success me-2 small"></i>Mensualités scolaires</span>
                        <span class="fw-bold"><?php echo number_format($finances['total_mensualite'] ?? 0, 2, ',', ' '); ?> DH</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                        <span><i class="fa-solid fa-circle text-warning me-2 small"></i>Frais divers</span>
                        <span class="fw-bold"><?php echo number_format($finances['total_divers'] ?? 0, 2, ',', ' '); ?> DH</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="custom-table-container mt-2">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-bold m-0">Dernières transactions</h5>
            <a href="index.php?controller=admin&action=payments" class="btn btn-sm btn-outline-primary">Voir tout</a>
        </div>
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>N° reçu</th>
                        <th>Élève</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPayments)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aucun paiement enregistré.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentPayments as $payment): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($payment['recu_no']); ?></td>
                                <td><?php echo htmlspecialchars($payment['nom'] . ' ' . $payment['prenom']); ?></td>
                                <td>
                                    <?php
                                    $badge = 'bg-secondary-soft';
                                    if ($payment['type_paiement'] === 'Inscription') $badge = 'bg-primary-soft';
                                    if ($payment['type_paiement'] === 'Mensualite') $badge = 'bg-success-soft';
                                    if ($payment['type_paiement'] === 'Frais divers') $badge = 'bg-warning-soft';
                                    ?>
                                    <span class="badge badge-custom <?php echo $badge; ?>"><?php echo htmlspecialchars($payment['type_paiement']); ?></span>
                                </td>
                                <td class="fw-bold"><?php echo number_format($payment['montant'], 2, ',', ' '); ?> DH</td>
                                <td class="text-muted"><?php echo date('d/m/Y', strtotime($payment['date_paiement'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartElement = document.getElementById('financialChart');

    if (!chartElement || typeof Chart === 'undefined') {
        return;
    }

    new Chart(chartElement.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ["Inscriptions", "Mensualités", "Frais divers"],
            datasets: [{
                data: [
                    <?php echo floatval($finances['total_inscription'] ?? 0); ?>,
                    <?php echo floatval($finances['total_mensualite'] ?? 0); ?>,
                    <?php echo floatval($finances['total_divers'] ?? 0); ?>
                ],
                backgroundColor: ['#004b93', '#13b87a', '#f4b740'],
                borderColor: '#ffffff',
                borderWidth: 4,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 18,
                        font: { family: 'Outfit' }
                    }
                }
            }
        }
    });
});
</script>
