<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Mes Classes</h1>
    </div>

    <!-- Filters card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Sélectionnez une classe pour voir ses élèves</h5>
            <form action="index.php" method="GET" class="row g-3">
                <input type="hidden" name="controller" value="teacher">
                <input type="hidden" name="action" value="classes">
                
                <div class="col-md-8">
                    <select class="form-select form-select-lg" name="class_id" required style="border-radius: 10px;">
                        <option value="">-- Choisir une classe --</option>
                        <?php 
                        $seenClasses = [];
                        foreach ($assignments as $asg): 
                            if (in_array($asg['class_id'], $seenClasses)) continue;
                            $seenClasses[] = $asg['class_id'];
                        ?>
                            <option value="<?php echo $asg['class_id']; ?>" <?php echo ($selectedClassId == $asg['class_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($asg['class_name'] . ' (' . $asg['niveau_name'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100" style="border-radius: 10px;">Voir la classe</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Class details and students -->
    <?php if ($isValidClass): ?>
        <?php
        $clsName = '';
        foreach ($assignments as $asg) {
            if ($asg['class_id'] == $selectedClassId) {
                $clsName = $asg['class_name'];
                break;
            }
        }
        ?>
        <div class="custom-table-container">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3 border-bottom pb-3">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-users text-primary me-2"></i>Liste des élèves : <span class="text-primary"><?php echo htmlspecialchars($clsName); ?></span></h5>
                
                <!-- Quick actions -->
                <div class="d-flex gap-2">
                    <?php
                    // Get first subject for this class to make the "Notes" link work smoothly
                    $firstMatiereId = null;
                    foreach ($assignments as $asg) {
                        if ($asg['class_id'] == $selectedClassId) {
                            $firstMatiereId = $asg['matiere_id'];
                            break;
                        }
                    }
                    ?>
                    <?php if ($firstMatiereId): ?>
                        <a href="index.php?controller=teacher&action=grades&class_id=<?php echo $selectedClassId; ?>&matiere_id=<?php echo $firstMatiereId; ?>" class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Saisir Notes
                        </a>
                    <?php endif; ?>
                    <a href="index.php?controller=teacher&action=attendance&class_id=<?php echo $selectedClassId; ?>" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                        <i class="fa-solid fa-calendar-check me-1"></i> Faire l'Appel
                    </a>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr class="table-light">
                            <th class="ps-4 text-uppercase text-secondary small tracking-wider">Élève</th>
                            <th class="text-uppercase text-secondary small tracking-wider">Email</th>
                            <th class="text-uppercase text-secondary small tracking-wider">Date de naissance</th>
                            <th class="pe-4 text-center text-uppercase text-secondary small tracking-wider">Sexe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Aucun élève n'est encore affecté à cette classe.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="ps-4 align-middle">
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($student['nom'] . ' ' . $student['prenom']); ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="text-decoration-none text-muted">
                                            <i class="fa-regular fa-envelope me-1"></i> <?php echo htmlspecialchars($student['email']); ?>
                                        </a>
                                    </td>
                                    <td class="align-middle text-muted">
                                        <?php echo date('d/m/Y', strtotime($student['date_naissance'])); ?>
                                    </td>
                                    <td class="pe-4 align-middle text-center">
                                        <?php if ($student['sexe'] === 'M'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><i class="fa-solid fa-mars"></i> Garçon</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="fa-solid fa-venus"></i> Fille</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="text-muted small text-end">
                Total : <?php echo count($students); ?> élève(s)
            </div>
        </div>
    <?php elseif (isset($_GET['class_id'])): ?>
        <div class="alert alert-danger" style="border-radius: 12px;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Vous n'avez pas accès à cette classe.
        </div>
    <?php endif; ?>
</div>
