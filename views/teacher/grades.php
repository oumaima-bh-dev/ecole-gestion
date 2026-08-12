<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Saisie des Notes</h1>
    </div>

    <!-- Selection card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Sélectionnez la classe et la matière</h5>
            <div class="row">
                <div class="col-md-8">
                    <form action="index.php" method="GET" class="d-flex flex-wrap gap-2">
                        <input type="hidden" name="controller" value="teacher">
                        <input type="hidden" name="action" value="grades">
                        
                        <select class="form-select flex-fill" name="class_subject" onchange="const vals = this.value.split('|'); if(vals.length===2){ window.location.href = 'index.php?controller=teacher&action=grades&class_id=' + vals[0] + '&matiere_id=' + vals[1]; }">
                            <option value="">-- Choisir une affectation --</option>
                            <?php foreach ($assignments as $asg): ?>
                                <?php $optVal = $asg['class_id'] . '|' . $asg['matiere_id']; ?>
                                <option value="<?php echo $optVal; ?>" <?php echo ($selectedClassId == $asg['class_id'] && $selectedMatiereId == $asg['matiere_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($asg['class_name'] . ' - ' . $asg['matiere_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Entry Sheet (Only if valid class/matiere selected) -->
    <?php if ($isValidAssignment): ?>
        <?php
        // Find subject name
        $subName = '';
        $clsName = '';
        foreach ($assignments as $asg) {
            if ($asg['class_id'] == $selectedClassId && $asg['matiere_id'] == $selectedMatiereId) {
                $subName = $asg['matiere_name'];
                $clsName = $asg['class_name'];
                break;
            }
        }
        ?>
        <form action="index.php?controller=teacher&action=grades&class_id=<?php echo $selectedClassId; ?>&matiere_id=<?php echo $selectedMatiereId; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="custom-table-container">
                <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fa-solid fa-list-check text-primary me-2"></i>Fiche d'Évaluation : <span class="text-primary"><?php echo htmlspecialchars($clsName); ?></span> <span class="text-muted mx-2">&bull;</span> <?php echo htmlspecialchars($subName); ?></h5>
                
                <!-- Evaluation details -->
                <div class="row g-3 mb-5 p-4 bg-light rounded-4 border border-light-subtle">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Type d'évaluation</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-3"><i class="fa-solid fa-pen"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 rounded-end-3" name="type_examen" placeholder="Ex: Devoir 1, Contrôle continu..." required style="background-color: white; box-shadow: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Date de l'évaluation</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-3"><i class="fa-solid fa-calendar-day"></i></span>
                            <input type="date" class="form-control border-start-0 ps-0 rounded-end-3" name="date_examen" value="<?php echo date('Y-m-d'); ?>" required style="background-color: white; box-shadow: none;">
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr class="table-light">
                                <th class="ps-4 text-uppercase text-secondary small tracking-wider">Élève</th>
                                <th style="width: 200px;" class="pe-4 text-end text-uppercase text-secondary small tracking-wider">Note (sur 20)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="ps-4 align-middle">
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($student['nom'] . ' ' . $student['prenom']); ?></div>
                                    </td>
                                    <td class="pe-4">
                                        <div class="input-group shadow-sm" style="border-radius: 12px;">
                                            <input type="number" class="form-control text-center font-monospace fw-bold fs-5" name="notes[<?php echo $student['student_id']; ?>]" placeholder="--" step="0.25" min="0" max="20" style="border-radius: 12px 0 0 12px; border-color: #dee2e6; color: var(--brand-primary);">
                                            <span class="input-group-text bg-light fw-bold text-muted border-start-0" style="border-radius: 0 12px 12px 0; border-color: #dee2e6;">/20</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">
                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Enregistrer les notes
                    </button>
                </div>
            </div>
        </form>
    <?php elseif ($selectedClassId): ?>
        <div class="alert alert-danger">
            Affectation introuvable ou accès refusé.
        </div>
    <?php endif; ?>
</div>
