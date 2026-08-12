<div class="container-fluid p-0">
    <div class="p-4 mb-4 bg-white rounded-3 border">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 fw-bold mb-1">Bonjour, M./Mme <?php echo htmlspecialchars(ucfirst($teacher['username'])); ?> !</h1>
                <p class="text-muted m-0">Bienvenue dans votre espace enseignant. Vous pouvez saisir les notes et suivre les absences de vos classes.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-success p-2 fs-6"><?php echo htmlspecialchars($teacher['diplome']); ?></span>
                <div class="small text-muted mt-1">En poste depuis le <?php echo date('d/m/Y', strtotime($teacher['date_embauche'])); ?></div>
            </div>
        </div>
    </div>

    <h4 class="fw-bold mb-3">Mes Cours & Affectations</h4>
    
    <div class="row">
        <?php if (empty($assignments)): ?>
            <div class="col-12">
                <div class="alert alert-info py-4 text-center">
                    <i class="fa-solid fa-circle-info fs-2 mb-2"></i>
                    <p class="m-0">Vous n'avez pas encore de classes affectées pour cette année scolaire.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($assignments as $asg): ?>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <span class="badge bg-primary-soft text-primary mb-2"><?php echo htmlspecialchars($asg['niveau_name']); ?></span>
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($asg['class_name']); ?></h5>
                            <h6 class="text-muted mb-4"><?php echo htmlspecialchars($asg['matiere_name']); ?></h6>
                            
                            <div class="d-flex gap-2 border-top pt-3">
                                <a href="index.php?controller=teacher&action=grades&class_id=<?php echo $asg['class_id']; ?>&matiere_id=<?php echo $asg['matiere_id']; ?>" class="btn btn-outline-primary btn-sm flex-fill py-2" style="border-radius: 8px;">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Saisir Notes
                                </a>
                                <a href="index.php?controller=teacher&action=attendance&class_id=<?php echo $asg['class_id']; ?>" class="btn btn-outline-secondary btn-sm flex-fill py-2" style="border-radius: 8px;">
                                    <i class="fa-solid fa-calendar-check me-1"></i> Présences
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
